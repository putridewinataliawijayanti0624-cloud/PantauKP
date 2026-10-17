<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Internship;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $q = match (true) {
            $user->isAdmin() => ActivityLog::query(),
            $user->isDosen() => ActivityLog::forLecturer($user),
            default => ActivityLog::forStudent($user),
        };

        $students = $user->isDosen() ? Internship::forLecturer($user)->with('student')->get() : collect();

        if ($user->isDosen() && $request->filled('internship_id')) {
            $q->where('internship_id', (int) $request->internship_id);
        }
        if ($request->filled('aksi') && isset(ActivityLog::LABELS[$request->aksi])) {
            $q->where('aksi', $request->aksi);
        }

        $logs = $q->with(['user', 'internship.student'])->latest('id')->paginate(15)->withQueryString();

        return view('aktivitas.index', compact('logs', 'students'));
    }
}
