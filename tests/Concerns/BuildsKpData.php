<?php

namespace Tests\Concerns;

use App\Enums\LogbookStatus;
use App\Models\Internship;
use App\Models\InternshipReport;
use App\Models\Logbook;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;

trait BuildsKpData
{
    /** @return array{0: User, 1: User, 2: Internship} [dosen, mahasiswa, magang] */
    protected function makeSetup(): array
    {
        $dosen = User::factory()->dosen()->create();
        $mhs = User::factory()->mahasiswa()->create();
        StudentProfile::create(['user_id' => $mhs->id, 'nim' => fake()->unique()->numerify('22#####'), 'program_studi' => 'Teknik Informatika']);

        $i = Internship::create([
            'student_id' => $mhs->id, 'nama_perusahaan' => 'PT Uji Coba',
            'tanggal_mulai' => now()->subDays(30)->toDateString(), 'tanggal_selesai' => now()->addDays(60)->toDateString(),
            'status' => 'berjalan', 'target_logbook' => 10,
        ]);
        $i->supervision()->create(['lecturer_id' => $dosen->id]);
        InternshipReport::create(['internship_id' => $i->id]);

        return [$dosen, $mhs, $i];
    }

    protected function makeAdmin(): User
    {
        return User::factory()->admin()->create();
    }

    protected function makeLogbook(Internship $i, LogbookStatus $status = LogbookStatus::Draft, int $submission = 0): Logbook
    {
        $l = new Logbook([
            'tanggal' => now()->subDay()->toDateString(), 'judul' => 'Judul asli', 'deskripsi' => 'Deskripsi kegiatan',
            'hasil' => 'Hasil kegiatan', 'kendala' => 'Kendala', 'rencana_tindak_lanjut' => 'Rencana',
        ]);
        $l->internship_id = $i->id;
        $l->status = $status;
        $l->submission_number = $submission;
        $l->submitted_at = $submission ? now() : null;
        $l->save();

        return $l;
    }

    protected function logbookPayload(array $override = []): array
    {
        return array_merge([
            'aksi' => 'draft', 'tanggal' => now()->subDay()->toDateString(), 'judul' => 'Belajar Laravel',
            'deskripsi' => 'Mempelajari routing dan controller.', 'hasil' => 'Mengerti dasar routing.',
            'kendala' => '', 'rencana_tindak_lanjut' => '',
        ], $override);
    }

    protected function pdf(string $name = 'laporan.pdf'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF\n");
    }

    protected function png(string $name = 'bukti.png'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='));
    }
}
