<?php

namespace App\Console\Commands;

use App\Models\LecturerProfile;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BuatDosen extends Command
{
    protected $signature = 'app:buat-dosen {name} {email} {nidn} {--prodi=Teknik Informatika}';

    protected $description = 'Membuat akun dosen (registrasi publik dosen sengaja tidak ada)';

    public function handle(): int
    {
        $password = $this->secret('Password (min. 8 karakter)');
        if (strlen((string) $password) < 8) {
            $this->error('Password minimal 8 karakter.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($password) {
            $u = new User(['name' => $this->argument('name'), 'email' => $this->argument('email'), 'password' => $password]);
            $u->forceFill(['role' => 'dosen', 'is_active' => true])->save();
            LecturerProfile::create(['user_id' => $u->id, 'nidn' => $this->argument('nidn'), 'program_studi' => $this->option('prodi')]);
        });

        $this->info('Akun dosen dibuat.');

        return self::SUCCESS;
    }
}
