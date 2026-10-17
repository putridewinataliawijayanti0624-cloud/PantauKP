<?php

namespace App\Services;

use App\Models\InternshipReport;
use App\Support\PrivateStorage;
use App\Models\LecturerProfile;
use App\Models\Logbook;
use App\Models\LogbookAttachment;
use App\Models\LogbookReview;
use App\Models\ReportReview;
use App\Models\ReportVersion;
use App\Models\StudentProfile;
use App\Models\Supervision;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserService
{
    public function __construct(private ActivityLogger $logger, private StudentService $students) {}

    public function create(User $admin, array $d): User
    {
        return DB::transaction(function () use ($admin, $d) {
            if ($d['role'] === 'mahasiswa') {
                $lecturer = User::where('role', 'dosen')->findOrFail($d['dosen_id']);
                $user = $this->students->register($lecturer, $d, $admin)->student;
            } else {
                $user = new User(['name' => $d['name'], 'email' => $d['email'], 'password' => $d['password']]);
                $user->forceFill(['role' => $d['role'], 'is_active' => true])->save();

                if ($d['role'] === 'dosen') {
                    LecturerProfile::create(['user_id' => $user->id, 'nidn' => $d['nidn'], 'program_studi' => $d['program_studi']]);
                }
            }

            $this->logger->log($admin, 'user.dibuat', null, $user, ['nama' => $user->name, 'role' => $d['role']]);

            return $user;
        });
    }

    public function update(User $admin, User $user, array $d): User
    {
        return DB::transaction(function () use ($admin, $user, $d) {
            $user->update(['name' => $d['name'], 'email' => $d['email']]);
            $user->forceFill(['is_active' => (bool) $d['is_active']])->save();

            if ($user->isDosen()) {
                LecturerProfile::updateOrCreate(['user_id' => $user->id], ['nidn' => $d['nidn'], 'program_studi' => $d['program_studi']]);
            }

            if ($user->isMahasiswa()) {
                StudentProfile::updateOrCreate(['user_id' => $user->id], [
                    'nim' => $d['nim'], 'program_studi' => $d['program_studi'], 'no_hp' => $d['no_hp'] ?? null,
                ]);

                // Pindah dosen pembimbing pada magang terbaru.
                if (! empty($d['dosen_id']) && ($internship = $user->currentInternship())) {
                    $internship->supervision()->updateOrCreate(['internship_id' => $internship->id], ['lecturer_id' => $d['dosen_id']]);
                }
            }

            $this->logger->log($admin, 'user.diubah', null, $user, ['nama' => $user->name]);

            return $user;
        });
    }

    public function resetPassword(User $admin, User $user, string $password): void
    {
        DB::transaction(function () use ($admin, $user, $password) {
            $user->update(['password' => $password]);
            $user->setRememberToken(Str::random(60));
            $user->save();

            // Akhiri sesi aktif milik pengguna tersebut (jika sesi disimpan di database).
            if (config('session.driver') === 'database') {
                DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
            }

            $this->logger->log($admin, 'user.password_direset', null, $user, ['nama' => $user->name]);
        });
    }

    public function toggleActive(User $admin, User $user): User
    {
        $user->forceFill(['is_active' => ! $user->is_active])->save();
        $this->logger->log($admin, $user->is_active ? 'user.diaktifkan' : 'user.dinonaktifkan', null, $user, ['nama' => $user->name]);

        return $user;
    }

    /** @return string|null pesan galat bila penghapusan ditolak */
    public function delete(User $admin, User $user): ?string
    {
        if ($user->is($admin)) {
            return 'Anda tidak dapat menghapus akun sendiri.';
        }

        if ($user->isDosen() && (
            Supervision::where('lecturer_id', $user->id)->exists()
            || LogbookReview::where('reviewer_id', $user->id)->exists()
            || ReportReview::where('reviewer_id', $user->id)->exists()
        )) {
            return 'Dosen ini masih memiliki mahasiswa bimbingan atau riwayat pemeriksaan. Nonaktifkan akunnya, atau pindahkan mahasiswanya terlebih dahulu.';
        }

        $paths = collect();
        if ($user->isMahasiswa()) {
            $ids = $user->internships()->pluck('id');
            $paths = LogbookAttachment::whereIn('logbook_id', Logbook::whereIn('internship_id', $ids)->select('id'))->pluck('path')
                ->merge(ReportVersion::whereIn('report_id', InternshipReport::whereIn('internship_id', $ids)->select('id'))->pluck('path'));
        }

        DB::transaction(function () use ($admin, $user) {
            $this->logger->log($admin, 'user.dihapus', null, null, ['nama' => $user->name, 'email' => $user->email, 'role' => $user->role->value]);
            $user->delete(); // data turunan (magang, logbook, laporan) terhapus lewat cascade
        });

        // File demo bersama (seed/) tidak boleh ikut terhapus.
        PrivateStorage::disk()->delete($paths->reject(fn ($p) => str_starts_with($p, 'seed/'))->unique()->values()->all());

        return null;
    }
}
