<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('status', ['belum_diunggah', 'menunggu', 'perlu_revisi', 'disetujui'])->default('belum_diunggah');
            $table->unsignedSmallInteger('versi_terbaru')->default(0);
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('report_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('internship_reports')->cascadeOnDelete();
            $table->unsignedSmallInteger('versi');
            $table->string('path');
            $table->string('nama_asli');
            $table->unsignedInteger('ukuran');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->unique(['report_id', 'versi']);
        });

        Schema::create('report_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_version_id')->constrained('report_versions')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->enum('keputusan', ['disetujui', 'perlu_revisi']);
            $table->text('komentar')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_reviews');
        Schema::dropIfExists('report_versions');
        Schema::dropIfExists('internship_reports');
    }
};
