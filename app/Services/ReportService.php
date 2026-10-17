<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\Internship;
use App\Models\InternshipReport;
use App\Models\ReportReview;
use App\Models\ReportVersion;
use App\Models\User;
use App\Support\PrivateStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function __construct(private ActivityLogger $logger) {}

    public function upload(User $student, Internship $internship, UploadedFile $file): ReportVersion
    {
        return DB::transaction(function () use ($student, $internship, $file) {
            $report = InternshipReport::firstOrCreate(['internship_id' => $internship->id]);
            $versi = $report->versi_terbaru + 1;

            $version = $report->versions()->create([
                'versi' => $versi,
                'path' => PrivateStorage::disk()->putFile("reports/{$report->id}", $file),
                'nama_asli' => $file->getClientOriginalName(),
                'ukuran' => $file->getSize(),
                'uploaded_at' => now(),
            ]);

            $report->status = ReportStatus::Menunggu;
            $report->versi_terbaru = $versi;
            $report->save();

            $this->logger->log($student, 'laporan.diunggah', $internship, $report, [
                'versi' => $versi, 'file' => $version->nama_asli,
            ]);

            return $version;
        });
    }

    public function review(User $reviewer, InternshipReport $report, string $keputusan, ?string $komentar): ReportReview
    {
        abort_unless($report->status === ReportStatus::Menunggu, 403, 'Laporan tidak sedang menunggu pemeriksaan.');
        abort_unless(in_array($keputusan, ['disetujui', 'perlu_revisi'], true), 422);

        return DB::transaction(function () use ($reviewer, $report, $keputusan, $komentar) {
            $version = $report->versions()->where('versi', $report->versi_terbaru)->firstOrFail();

            $review = $version->reviews()->create([
                'reviewer_id' => $reviewer->id,
                'keputusan' => $keputusan,
                'komentar' => $komentar,
            ]);

            $report->status = ReportStatus::from($keputusan);
            $report->save();

            $this->logger->log($reviewer, 'laporan.'.$keputusan, $report->internship, $report, [
                'versi' => $version->versi, 'komentar' => $komentar,
            ]);

            return $review;
        });
    }
}
