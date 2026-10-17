<?php

namespace App\Services;

use App\Enums\LogbookStatus;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\LogbookAttachment;
use App\Models\LogbookReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Support\PrivateStorage;

class LogbookService
{
    public function __construct(private ActivityLogger $logger) {}

    /**
     * Simpan logbook (baru/ubah), kelola lampiran, dan opsional langsung kirim.
     *
     * @param  array<int, \Illuminate\Http\UploadedFile>  $files
     * @param  array<int, int|string>  $removeIds
     */
    public function save(User $actor, Internship $internship, array $data, array $files = [], array $removeIds = [], ?Logbook $logbook = null, bool $submit = false): Logbook
    {
        return DB::transaction(function () use ($actor, $internship, $data, $files, $removeIds, $logbook, $submit) {
            if ($logbook) {
                $logbook->update($data);
            } else {
                $logbook = new Logbook($data);
                $logbook->internship_id = $internship->id;
                $logbook->status = LogbookStatus::Draft;
                $logbook->save();
            }

            if ($removeIds) {
                $logbook->attachments()->whereIn('id', $removeIds)->get()->each(function ($a) {
                    PrivateStorage::disk()->delete($a->path);
                    $a->delete();
                });
            }

            foreach ($files as $file) {
                $logbook->attachments()->create([
                    'path' => PrivateStorage::disk()->putFile("logbooks/{$logbook->id}", $file),
                    'nama_asli' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType() ?? 'application/octet-stream',
                    'ukuran' => $file->getSize(),
                ]);
            }

            if ($submit) {
                $this->submit($actor, $logbook);
            }

            return $logbook;
        });
    }

    public function submit(User $actor, Logbook $logbook): Logbook
    {
        abort_unless($logbook->isEditable(), 403, 'Logbook tidak dapat dikirim pada status ini.');

        return DB::transaction(function () use ($actor, $logbook) {
            $logbook->status = LogbookStatus::Menunggu;
            $logbook->submission_number++;
            $logbook->submitted_at = now();
            $logbook->save();

            $this->logger->log($actor, 'logbook.dikirim', $logbook->internship, $logbook, [
                'judul' => $logbook->judul, 'pengiriman_ke' => $logbook->submission_number,
            ]);

            return $logbook;
        });
    }

    /** Dosen memeriksa. Snapshot isi logbook disimpan agar riwayat tidak hilang setelah revisi. */
    public function review(User $reviewer, Logbook $logbook, string $keputusan, ?string $komentar): LogbookReview
    {
        abort_unless($logbook->status === LogbookStatus::Menunggu, 403, 'Logbook tidak sedang menunggu pemeriksaan.');
        abort_unless(in_array($keputusan, ['disetujui', 'perlu_revisi'], true), 422);

        return DB::transaction(function () use ($reviewer, $logbook, $keputusan, $komentar) {
            $review = LogbookReview::create([
                'logbook_id' => $logbook->id,
                'reviewer_id' => $reviewer->id,
                'submission_number' => $logbook->submission_number,
                'keputusan' => $keputusan,
                'komentar' => $komentar,
                'snapshot' => $logbook->only(['judul', 'deskripsi', 'hasil', 'kendala', 'rencana_tindak_lanjut']) + [
                    'tanggal' => $logbook->tanggal->toDateString(),
                    'lampiran' => $logbook->attachments()->pluck('nama_asli')->all(),
                ],
            ]);

            $logbook->status = LogbookStatus::from($keputusan);
            $logbook->save();

            $this->logger->log($reviewer, 'logbook.'.$keputusan, $logbook->internship, $logbook, [
                'judul' => $logbook->judul, 'komentar' => $komentar,
            ]);

            return $review;
        });
    }
}
