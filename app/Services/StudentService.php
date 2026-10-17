<?php

namespace App\Services;

use App\Models\Internship;
use App\Models\InternshipReport;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StudentService
{
    public function __construct(private ActivityLogger $logger) {}

    /** Dosen membuat akun mahasiswa + data magang, otomatis menjadi pembimbingnya. */
    public function register(User $lecturer, array $d, ?User $actor = null): Internship
    {
        return DB::transaction(function () use ($lecturer, $d) {
            $user = new User(['name' => $d['name'], 'email' => $d['email'], 'password' => $d['password']]);
            $user->forceFill(['role' => 'mahasiswa', 'is_active' => true])->save();

            StudentProfile::create([
                'user_id' => $user->id, 'nim' => $d['nim'],
                'program_studi' => $d['program_studi'], 'no_hp' => $d['no_hp'] ?? null,
            ]);

            $today = now()->toDateString();
            $status = $today < $d['tanggal_mulai'] ? 'belum_mulai' : ($today > $d['tanggal_selesai'] ? 'selesai' : 'berjalan');

            $internship = Internship::create([
                'student_id' => $user->id,
                'nama_perusahaan' => $d['nama_perusahaan'],
                'alamat' => $d['alamat'] ?? null,
                'pembimbing_lapangan' => $d['pembimbing_lapangan'] ?? null,
                'tanggal_mulai' => $d['tanggal_mulai'],
                'tanggal_selesai' => $d['tanggal_selesai'],
                'status' => $status,
                'target_logbook' => $d['target_logbook'] ?? Internship::hitungTarget($d['tanggal_mulai'], $d['tanggal_selesai']),
            ]);

            $internship->supervision()->create(['lecturer_id' => $lecturer->id]);
            InternshipReport::create(['internship_id' => $internship->id]);

            $this->logger->log($actor ?? $lecturer, 'mahasiswa.ditambahkan', $internship, $internship, ['nama' => $user->name]);

            return $internship;
        });
    }
}
