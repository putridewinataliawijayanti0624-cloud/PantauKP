<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('judul');
            $table->text('deskripsi');
            $table->text('hasil')->nullable();
            $table->text('kendala')->nullable();
            $table->text('rencana_tindak_lanjut')->nullable();
            $table->enum('status', ['draft', 'menunggu', 'disetujui', 'perlu_revisi'])->default('draft');
            $table->unsignedSmallInteger('submission_number')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->index(['internship_id', 'status']);
            $table->index(['internship_id', 'tanggal']);
        });

        Schema::create('logbook_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logbook_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('nama_asli');
            $table->string('mime', 100);
            $table->unsignedInteger('ukuran');
            $table->timestamps();
        });

        Schema::create('logbook_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logbook_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->unsignedSmallInteger('submission_number');
            $table->enum('keputusan', ['disetujui', 'perlu_revisi']);
            $table->text('komentar')->nullable();
            $table->json('snapshot')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['logbook_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_reviews');
        Schema::dropIfExists('logbook_attachments');
        Schema::dropIfExists('logbooks');
    }
};
