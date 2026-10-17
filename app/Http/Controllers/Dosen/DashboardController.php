<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Internship;
use App\Models\InternshipReport;
use App\Models\Logbook;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $u = $request->user();

        $stats = [
            'total' => Internship::forLecturer($u)->count(),
            'berjalan' => Internship::forLecturer($u)->where('status', 'berjalan')->count(),
            'logbook_menunggu' => Logbook::forLecturer($u)->where('status', 'menunggu')->count(),
            'logbook_revisi' => Logbook::forLecturer($u)->where('status', 'perlu_revisi')->count(),
            'laporan_menunggu' => InternshipReport::forLecturer($u)->where('status', 'menunggu')->count(),
        ];

        $students = Internship::forLecturer($u)->with('student')->withApproved()
            ->withCount([
                'logbooks as menunggu_count' => fn ($q) => $q->where('status', 'menunggu'),
                'report as laporan_menunggu' => fn ($q) => $q->where('status', 'menunggu'),
            ])->get();

        $chart = [
            'labels' => $students->map(fn ($i) => $i->student->name)->values(),
            'data' => $students->map(fn ($i) => $i->progress)->values(),
        ];

        $followUp = $students->filter(fn ($i) => $i->menunggu_count + $i->laporan_menunggu > 0)
            ->sortByDesc(fn ($i) => $i->menunggu_count + $i->laporan_menunggu)->take(6);

        $recent = ActivityLog::forLecturer($u)->with(['user', 'internship.student'])->latest('id')->limit(8)->get();

        return view('dosen.dashboard', compact('stats', 'students', 'chart', 'followUp', 'recent'));
    }
}
