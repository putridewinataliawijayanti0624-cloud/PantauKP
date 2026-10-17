<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Models\ActivityLog;
use App\Models\Internship;
use App\Models\StudentProfile;
use App\Models\User;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $u = $request->user();
        $sort = in_array($request->sort, ['nama', 'nim', 'mulai', 'progres'], true) ? $request->sort : 'nama';
        $dir = $request->dir === 'desc' ? 'desc' : 'asc';

        $q = Internship::forLecturer($u)->with('student.studentProfile')->withApproved();

        if ($s = trim((string) $request->q)) {
            $q->where(function ($w) use ($s) {
                $w->where('nama_perusahaan', 'like', "%{$s}%")
                    ->orWhereHas('student', fn ($x) => $x->where('name', 'like', "%{$s}%")
                        ->orWhereHas('studentProfile', fn ($y) => $y->where('nim', 'like', "%{$s}%")));
            });
        }
        if (in_array($request->status, array_keys(Internship::STATUS_LABEL), true)) {
            $q->where('status', $request->status);
        }

        match ($sort) {
            'nim' => $q->orderBy(StudentProfile::select('nim')->whereColumn('student_profiles.user_id', 'internships.student_id'), $dir),
            'mulai' => $q->orderBy('tanggal_mulai', $dir),
            'progres' => $q->orderBy('approved_count', $dir),
            default => $q->orderBy(User::select('name')->whereColumn('users.id', 'internships.student_id'), $dir),
        };

        $internships = $q->paginate(10)->withQueryString();

        return view('dosen.mahasiswa.index', compact('internships', 'sort', 'dir'));
    }

    public function create()
    {
        return view('dosen.mahasiswa.create');
    }

    public function store(StoreStudentRequest $request, StudentService $service)
    {
        $internship = $service->register($request->user(), $request->validated());

        return redirect()->route('dosen.mahasiswa.show', $internship)
            ->with('success', 'Mahasiswa berhasil ditambahkan dan ditugaskan kepada Anda.');
    }

    public function show(Request $request, Internship $internship)
    {
        Gate::authorize('view', $internship);

        $internship->load('student.studentProfile', 'lecturer', 'report.versions.reviews.reviewer')->loadCount([
            'logbooks as approved_count' => fn ($q) => $q->where('status', 'disetujui'),
            'logbooks as menunggu_count' => fn ($q) => $q->where('status', 'menunggu'),
            'logbooks as revisi_count' => fn ($q) => $q->where('status', 'perlu_revisi'),
            'logbooks as total_count' => fn ($q) => $q->where('status', '!=', 'draft'),
        ]);

        $logbooks = $internship->logbooks()->where('status', '!=', 'draft')->withCount('attachments')
            ->latest('tanggal')->paginate(10, ['*'], 'logbook_page')->withQueryString();

        $logs = ActivityLog::where('internship_id', $internship->id)->with('user')->latest('id')->limit(15)->get();

        return view('dosen.mahasiswa.show', compact('internship', 'logbooks', 'logs'));
    }
}
