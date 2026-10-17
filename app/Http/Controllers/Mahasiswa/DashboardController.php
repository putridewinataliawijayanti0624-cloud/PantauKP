<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\LogbookReview;
use App\Models\ReportReview;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $internship = $request->user()->currentInternship();

        if (! $internship) {
            return view('mahasiswa.dashboard', ['internship' => null]);
        }

        $internship->load('lecturer', 'report')->loadCount([
            'logbooks as total_count' => fn ($q) => $q->where('status', '!=', 'draft'),
            'logbooks as approved_count' => fn ($q) => $q->where('status', 'disetujui'),
            'logbooks as menunggu_count' => fn ($q) => $q->where('status', 'menunggu'),
            'logbooks as revisi_count' => fn ($q) => $q->where('status', 'perlu_revisi'),
            'logbooks as draft_count' => fn ($q) => $q->where('status', 'draft'),
        ]);

        $logFeedback = LogbookReview::whereIn('logbook_id', $internship->logbooks()->select('id'))
            ->with('reviewer', 'logbook')->latest('id')->limit(3)->get();

        $reportFeedback = $internship->report
            ? ReportReview::whereIn('report_version_id', $internship->report->versions()->select('id'))
                ->with('reviewer')->latest('id')->first()
            : null;

        return view('mahasiswa.dashboard', compact('internship', 'logFeedback', 'reportFeedback'));
    }
}
