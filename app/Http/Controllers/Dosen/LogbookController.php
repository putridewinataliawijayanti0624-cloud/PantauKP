<?php

namespace App\Http\Controllers\Dosen;

use App\Enums\LogbookStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Internship;
use App\Models\Logbook;
use App\Services\LogbookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LogbookController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();
        $q = Logbook::forLecturer($u)->where('status', '!=', 'draft')->with('internship.student')->withCount('attachments');

        if ($request->filled('internship_id')) {
            $q->where('internship_id', (int) $request->internship_id);
        }
        if ($request->filled('status') && LogbookStatus::tryFrom($request->status)) {
            $q->where('status', $request->status);
        }
        if ($request->filled('dari')) {
            $q->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $q->whereDate('tanggal', '<=', $request->sampai);
        }

        $logbooks = $q->orderByRaw("status = 'menunggu' desc")->latest('tanggal')->paginate(12)->withQueryString();
        $students = Internship::forLecturer($u)->with('student')->get();

        return view('dosen.logbook.index', compact('logbooks', 'students'));
    }

    public function show(Logbook $logbook)
    {
        Gate::authorize('view', $logbook);
        $logbook->load('internship.student.studentProfile', 'attachments', 'reviews.reviewer');

        return view('dosen.logbook.show', compact('logbook'));
    }

    public function review(ReviewRequest $request, Logbook $logbook, LogbookService $service)
    {
        Gate::authorize('review', $logbook);
        $service->review($request->user(), $logbook, $request->keputusan, $request->komentar);

        return redirect()->route('dosen.logbook.show', $logbook)
            ->with('success', $request->keputusan === 'disetujui' ? 'Logbook disetujui.' : 'Permintaan revisi dikirim ke mahasiswa.');
    }
}
