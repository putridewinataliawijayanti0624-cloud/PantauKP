<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/** Dipakai untuk pemeriksaan logbook dan laporan. */
class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isDosen() ?? false;
    }

    public function rules(): array
    {
        return [
            'keputusan' => ['required', 'in:disetujui,perlu_revisi'],
            'komentar' => ['required_if:keputusan,perlu_revisi', 'nullable', 'string', 'max:3000'],
        ];
    }

    public function messages(): array
    {
        return ['komentar.required_if' => 'Alasan wajib diisi ketika meminta revisi.'];
    }
}
