<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadReportRequest;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function show(Request $request)
    {
        $internship = $request->user()->currentInternship();
        abort_unless($internship, 404);
        $internship->load('report.versions.reviews.reviewer');

        return view('mahasiswa.laporan', [
            'internship' => $internship,
            'report' => $internship->report,
            'status' => $internship->reportStatus(),
            'canUpload' => $request->user()->can('uploadReport', $internship),
        ]);
    }

    public function store(UploadReportRequest $request, ReportService $service)
    {
        $internship = $request->user()->currentInternship();
        abort_unless($internship, 404);
        Gate::authorize('uploadReport', $internship);

        $v = $service->upload($request->user(), $internship, $request->file('file'));

        return redirect()->route('mahasiswa.laporan')->with('success', "Laporan versi {$v->versi} berhasil diunggah.");
    }
}
