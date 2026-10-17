<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogbookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isMahasiswa() ?? false;
    }

    public function rules(): array
    {
        $internship = $this->user()->currentInternship();
        $mulai = $internship?->tanggal_mulai?->toDateString() ?? '2000-01-01';
        $maks = $internship?->maxLogbookDate() ?? now()->toDateString();

        return [
            'aksi' => ['required', 'in:draft,kirim'],
            'tanggal' => ['required', 'date', 'after_or_equal:'.$mulai, 'before_or_equal:'.$maks],
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:5000'],
            'hasil' => ['required_if:aksi,kirim', 'nullable', 'string', 'max:5000'],
            'kendala' => ['nullable', 'string', 'max:5000'],
            'rencana_tindak_lanjut' => ['nullable', 'string', 'max:5000'],
            'lampiran' => ['nullable', 'array', 'max:5'],
            'lampiran.*' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
            'hapus_lampiran' => ['nullable', 'array'],
            'hapus_lampiran.*' => ['integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.after_or_equal' => 'Tanggal kegiatan tidak boleh sebelum tanggal mulai magang (:date).',
            'tanggal.before_or_equal' => 'Tanggal kegiatan tidak boleh melewati hari ini (maksimal :date).',
        ];
    }

    public function attributes(): array
    {
        return ['rencana_tindak_lanjut' => 'rencana tindak lanjut', 'lampiran.*' => 'lampiran'];
    }
}
