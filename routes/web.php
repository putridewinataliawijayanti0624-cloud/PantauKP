<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dosen;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Mahasiswa;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:10,1');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', fn () => redirect()->route(auth()->user()->role->value.'.dashboard'))->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'password'])->name('profile.password');

    Route::get('/files/logbook/{attachment}', [FileController::class, 'logbookAttachment'])->name('files.logbook');
    Route::get('/files/laporan/{version}', [FileController::class, 'reportVersion'])->name('files.laporan');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', Admin\DashboardController::class)->name('dashboard');

        Route::get('/pengguna', [Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/pengguna/tambah', [Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/pengguna', [Admin\UserController::class, 'store'])->name('users.store');
        Route::get('/pengguna/{user}/ubah', [Admin\UserController::class, 'edit'])->name('users.edit');
        Route::put('/pengguna/{user}', [Admin\UserController::class, 'update'])->name('users.update');
        Route::put('/pengguna/{user}/password', [Admin\UserController::class, 'password'])->name('users.password');
        Route::post('/pengguna/{user}/status', [Admin\UserController::class, 'toggle'])->name('users.toggle');
        Route::delete('/pengguna/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/aktivitas', [ActivityController::class, 'index'])->name('aktivitas');
    });

    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::get('/dashboard', Dosen\DashboardController::class)->name('dashboard');

        Route::get('/mahasiswa', [Dosen\StudentController::class, 'index'])->name('mahasiswa.index');
        Route::get('/mahasiswa/tambah', [Dosen\StudentController::class, 'create'])->name('mahasiswa.create');
        Route::post('/mahasiswa', [Dosen\StudentController::class, 'store'])->name('mahasiswa.store');
        Route::get('/mahasiswa/{internship}', [Dosen\StudentController::class, 'show'])->name('mahasiswa.show');

        Route::get('/logbook', [Dosen\LogbookController::class, 'index'])->name('logbook.index');
        Route::get('/logbook/{logbook}', [Dosen\LogbookController::class, 'show'])->name('logbook.show');
        Route::post('/logbook/{logbook}/review', [Dosen\LogbookController::class, 'review'])->name('logbook.review');

        Route::get('/laporan', [Dosen\ReportController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/{report}', [Dosen\ReportController::class, 'show'])->name('laporan.show');
        Route::post('/laporan/{report}/review', [Dosen\ReportController::class, 'review'])->name('laporan.review');

        Route::get('/aktivitas', [ActivityController::class, 'index'])->name('aktivitas');
    });

    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/dashboard', Mahasiswa\DashboardController::class)->name('dashboard');
        Route::get('/magang', [Mahasiswa\InternshipController::class, 'show'])->name('magang');

        Route::resource('logbook', Mahasiswa\LogbookController::class);
        Route::post('/logbook/{logbook}/kirim', [Mahasiswa\LogbookController::class, 'submit'])->name('logbook.submit');

        Route::get('/laporan', [Mahasiswa\ReportController::class, 'show'])->name('laporan');
        Route::post('/laporan', [Mahasiswa\ReportController::class, 'store'])->name('laporan.store');

        Route::get('/aktivitas', [ActivityController::class, 'index'])->name('aktivitas');
    });
});
