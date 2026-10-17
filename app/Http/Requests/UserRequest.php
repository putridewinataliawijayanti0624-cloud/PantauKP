<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Dipakai admin untuk membuat (POST) dan mengubah (PUT) pengguna. */
class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $editing = $this->route('user') instanceof User ? $this->route('user') : null;
        $role = $editing?->role->value ?? $this->input('role');

        $r = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($editing?->id)],
        ];

        if ($editing) {
            $r['is_active'] = ['required', 'boolean'];
        } else {
            $r['role'] = ['required', 'in:admin,dosen,mahasiswa'];
            $r['password'] = ['required', 'string', 'min:8'];
        }

        if ($role === 'dosen') {
            $r['nidn'] = ['required', 'string', 'max:30', Rule::unique('lecturer_profiles', 'nidn')->ignore($editing?->lecturerProfile?->id)];
            $r['program_studi'] = ['required', 'string', 'max:100'];
        }

        if ($role === 'mahasiswa') {
            $r['nim'] = ['required', 'string', 'max:30', Rule::unique('student_profiles', 'nim')->ignore($editing?->studentProfile?->id)];
            $r['program_studi'] = ['required', 'string', 'max:100'];
            $r['no_hp'] = ['nullable', 'string', 'max:20'];
            $dosenExists = Rule::exists('users', 'id')->where('role', 'dosen')->where('is_active', true);
            $r['dosen_id'] = $editing ? ['nullable', $dosenExists] : ['required', $dosenExists];

            if (! $editing) {
                $r += [
                    'nama_perusahaan' => ['required', 'string', 'max:255'],
                    'alamat' => ['nullable', 'string', 'max:255'],
                    'pembimbing_lapangan' => ['nullable', 'string', 'max:255'],
                    'tanggal_mulai' => ['required', 'date'],
                    'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
                    'target_logbook' => ['nullable', 'integer', 'min:1', 'max:500'],
                ];
            }
        }

        return $r;
    }
}
