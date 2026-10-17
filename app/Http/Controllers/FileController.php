<?php

namespace App\Http\Controllers;

use App\Models\LogbookAttachment;
use App\Models\ReportVersion;
use App\Support\PrivateStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/** Semua file privat diakses lewat sini, dengan otorisasi pada induknya. */
class FileController extends Controller
{
    public function logbookAttachment(Request $request, LogbookAttachment $attachment)
    {
        Gate::authorize('view', $attachment->logbook);

        return $this->send($attachment->path, $attachment->nama_asli, $attachment->mime, $request);
    }

    public function reportVersion(Request $request, ReportVersion $version)
    {
        Gate::authorize('view', $version->report);

        return $this->send($version->path, $version->nama_asli, 'application/pdf', $request);
    }

    private function send(string $path, string $name, string $mime, Request $request)
    {
        $disk = PrivateStorage::disk();
        abort_unless($disk->exists($path), 404, 'File tidak ditemukan. Aplikasi mencari di: '.$disk->path($path));

        $headers = ['Content-Type' => $mime, 'X-Content-Type-Options' => 'nosniff'];

        return $request->boolean('unduh')
            ? $disk->download($path, $name, $headers)
            : $disk->response($path, $name, $headers);
    }
}
