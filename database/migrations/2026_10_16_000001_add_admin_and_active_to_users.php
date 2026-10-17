<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Untuk database LAMA (sebelum ada role admin). Pada instalasi baru, kolom sudah dibuat
 * oleh migration users sehingga migration ini tidak mengubah apa pun (idempoten).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', fn (Blueprint $t) => $t->boolean('is_active')->default(true)->after('role'));
        }

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','dosen','mahasiswa') NOT NULL DEFAULT 'mahasiswa'");
        }
    }

    public function down(): void
    {
        // Sengaja kosong: menghapus role/kolom dapat menghilangkan data akun admin.
    }
};
