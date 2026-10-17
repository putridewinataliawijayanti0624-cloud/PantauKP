<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isMahasiswa() ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:10240'],
        ];
    }

    public function attributes(): array
    {
        return ['file' => 'file laporan'];
    }
}
