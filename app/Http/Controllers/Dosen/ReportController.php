<?php

namespace App\Http\Controllers\Dosen;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\InternshipReport;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $q = InternshipReport::forLecturer($request->user())->with('internship.student', 'latestVersion');

        if ($request->filled('status') && ReportStatus::tryFrom($request->status)) {
            $q->where('status', $request->status);
        }

        $reports = $q->orderByRaw("status = 'menunggu' desc")->latest('updated_at')->paginate(12)->withQueryString();

        return view('dosen.laporan.index', compact('reports'));
    }

    public function show(InternshipReport $report)
    {
        Gate::authorize('view', $report);
        $report->load('internship.student.studentProfile', 'versions.reviews.reviewer');

        return view('dosen.laporan.show', compact('report'));
    }

    public function review(ReviewRequest $request, InternshipReport $report, ReportService $service)
    {
        Gate::authorize('review', $report);
        $service->review($request->user(), $report, $request->keputusan, $request->komentar);

        return redirect()->route('dosen.laporan.show', $report)
            ->with('success', $request->keputusan === 'disetujui' ? 'Laporan disetujui.' : 'Permintaan revisi laporan dikirim.');
    }
}
