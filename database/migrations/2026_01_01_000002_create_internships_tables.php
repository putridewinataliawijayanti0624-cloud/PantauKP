<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_perusahaan');
            $table->string('alamat')->nullable();
            $table->string('pembimbing_lapangan')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['belum_mulai', 'berjalan', 'selesai'])->default('belum_mulai');
            $table->unsignedSmallInteger('target_logbook')->default(1);
            $table->timestamps();
            $table->index(['student_id', 'status']);
        });

        Schema::create('supervisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('internship_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervisions');
        Schema::dropIfExists('internships');
    }
};
