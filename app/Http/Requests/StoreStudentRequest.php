<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDosen() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'nim' => ['required', 'string', 'max:30', 'unique:student_profiles,nim'],
            'program_studi' => ['required', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'pembimbing_lapangan' => ['nullable', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'target_logbook' => ['nullable', 'integer', 'min:1', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return ['nim' => 'NIM', 'no_hp' => 'nomor HP', 'program_studi' => 'program studi'];
    }
}
