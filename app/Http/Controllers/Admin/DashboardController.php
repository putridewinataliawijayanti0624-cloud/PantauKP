<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Internship;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $byRole = User::selectRaw('role, count(*) as c')->groupBy('role')->pluck('c', 'role');

        $stats = [
            'total' => User::count(),
            'admin' => (int) ($byRole['admin'] ?? 0),
            'dosen' => (int) ($byRole['dosen'] ?? 0),
            'mahasiswa' => (int) ($byRole['mahasiswa'] ?? 0),
            'aktif' => User::where('is_active', true)->count(),
            'nonaktif' => User::where('is_active', false)->count(),
            'magang_berjalan' => Internship::where('status', 'berjalan')->count(),
        ];

        $chart = ['labels' => ['Admin', 'Dosen', 'Mahasiswa'], 'data' => [$stats['admin'], $stats['dosen'], $stats['mahasiswa']]];
        $latestUsers = User::latest('id')->limit(6)->get();
        $recent = ActivityLog::with(['user', 'internship.student'])->latest('id')->limit(8)->get();

        return view('admin.dashboard', compact('stats', 'chart', 'latestUsers', 'recent'));
    }
}
