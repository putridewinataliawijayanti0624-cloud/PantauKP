<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nim', 30)->unique();
            $table->string('program_studi', 100);
            $table->string('no_hp', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('lecturer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nidn', 30)->unique();
            $table->string('program_studi', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturer_profiles');
        Schema::dropIfExists('student_profiles');
    }
};
