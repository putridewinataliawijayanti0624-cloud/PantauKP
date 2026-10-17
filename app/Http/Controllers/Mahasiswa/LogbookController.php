<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Enums\LogbookStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\LogbookRequest;
use App\Models\Logbook;
use App\Services\LogbookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LogbookController extends Controller
{
    public function __construct(private LogbookService $service) {}

    public function index(Request $request)
    {
        $internship = $request->user()->currentInternship();
        $q = $internship ? $internship->logbooks()->withCount('attachments') : Logbook::whereRaw('1 = 0');

        if ($request->filled('status') && LogbookStatus::tryFrom($request->status)) {
            $q->where('status', $request->status);
        }

        $logbooks = $q->latest('tanggal')->paginate(10)->withQueryString();

        return view('mahasiswa.logbook.index', compact('logbooks', 'internship'));
    }

    public function create(Request $request)
    {
        $internship = $request->user()->currentInternship();
        abort_unless($internship, 404);
        Gate::authorize('createLogbook', $internship);

        if ($blocked = $this->notStartedRedirect($internship)) {
            return $blocked;
        }

        return view('mahasiswa.logbook.form', ['logbook' => new Logbook(['tanggal' => $internship->maxLogbookDate()]), 'internship' => $internship]);
    }

    public function store(LogbookRequest $request)
    {
        $internship = $request->user()->currentInternship();
        abort_unless($internship, 404);
        Gate::authorize('createLogbook', $internship);

        if ($blocked = $this->notStartedRedirect($internship)) {
            return $blocked;
        }

        $logbook = $this->service->save(
            $request->user(), $internship, $request->safe()->except(['aksi', 'lampiran', 'hapus_lampiran']),
            $request->file('lampiran', []), [], null, $request->aksi === 'kirim'
        );

        return redirect()->route('mahasiswa.logbook.show', $logbook)
            ->with('success', $request->aksi === 'kirim' ? 'Logbook dikirim untuk diperiksa.' : 'Draft logbook disimpan.');
    }

    public function show(Logbook $logbook)
    {
        Gate::authorize('view', $logbook);
        $logbook->load('internship', 'attachments', 'reviews.reviewer');

        return view('mahasiswa.logbook.show', compact('logbook'));
    }

    public function edit(Logbook $logbook)
    {
        Gate::authorize('update', $logbook);
        $logbook->load('internship', 'attachments', 'reviews.reviewer');

        return view('mahasiswa.logbook.form', ['logbook' => $logbook, 'internship' => $logbook->internship]);
    }

    public function update(LogbookRequest $request, Logbook $logbook)
    {
        Gate::authorize('update', $logbook);

        $this->service->save(
            $request->user(), $logbook->internship, $request->safe()->except(['aksi', 'lampiran', 'hapus_lampiran']),
            $request->file('lampiran', []), $request->input('hapus_lampiran', []), $logbook, $request->aksi === 'kirim'
        );

        return redirect()->route('mahasiswa.logbook.show', $logbook)
            ->with('success', $request->aksi === 'kirim' ? 'Logbook dikirim untuk diperiksa.' : 'Perubahan disimpan.');
    }

    public function submit(Request $request, Logbook $logbook)
    {
        Gate::authorize('update', $logbook);
        $this->service->submit($request->user(), $logbook);

        return redirect()->route('mahasiswa.logbook.show', $logbook)->with('success', 'Logbook dikirim untuk diperiksa.');
    }

    private function notStartedRedirect($internship)
    {
        if ($internship->hasStarted()) {
            return null;
        }

        // Penjelasan tanggal mulai sudah tampil sebagai info di halaman daftar logbook, jadi tidak perlu pesan error tambahan.
        return redirect()->route('mahasiswa.logbook.index');
    }

    public function destroy(Logbook $logbook)
    {
        Gate::authorize('delete', $logbook);
        $logbook->attachments->each(fn ($a) => \App\Support\PrivateStorage::disk()->delete($a->path));
        $logbook->delete();

        return redirect()->route('mahasiswa.logbook.index')->with('success', 'Draft logbook dihapus.');
    }
}
