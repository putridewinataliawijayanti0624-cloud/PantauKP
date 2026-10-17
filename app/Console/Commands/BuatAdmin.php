<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class BuatAdmin extends Command
{
    protected $signature = 'app:buat-admin {name} {email}';

    protected $description = 'Membuat akun administrator';

    public function handle(): int
    {
        $password = $this->secret('Password (min. 8 karakter)');
        if (strlen((string) $password) < 8) {
            $this->error('Password minimal 8 karakter.');

            return self::FAILURE;
        }

        $u = new User(['name' => $this->argument('name'), 'email' => $this->argument('email'), 'password' => $password]);
        $u->forceFill(['role' => 'admin', 'is_active' => true])->save();
        $this->info('Akun admin dibuat.');

        return self::SUCCESS;
    }
}
