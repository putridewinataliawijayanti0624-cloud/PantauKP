@extends('layouts.app')
@section('title', 'Logbook')
@section('heading', 'Logbook Kegiatan')
@section('actions')
@if($internship)
    @if($internship->hasStarted())
        <a href="{{ route('mahasiswa.logbook.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Logbook</a>
    @else
        <button class="btn btn-primary" disabled title="Magang belum dimulai"><i class="bi bi-plus-lg me-1"></i>Tambah Logbook</button>
    @endif
@endif
@endsection
@section('content')
@if($internship && ! $internship->hasStarted())
    <div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Magang Anda dimulai pada <strong>{{ $internship->tanggal_mulai->translatedFormat('d F Y') }}</strong>. Logbook dapat dibuat mulai tanggal tersebut.</div>
@endif
<form method="GET" class="card card-body mb-3"><div class="row g-2">
    <div class="col-md-4"><select name="status" class="form-select"><option value="">Semua status</option>
        @foreach(\App\Enums\LogbookStatus::cases() as $st)<option value="{{ $st->value }}" @selected(request('status') === $st->value)>{{ $st->label() }}</option>@endforeach</select></div>
    <div class="col-md-2 d-grid"><button class="btn btn-primary">Filter</button></div>
</div></form>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead class="kp-thead"><tr><th>Tanggal</th><th>Judul</th><th>Lampiran</th><th>Status</th><th></th></tr></thead><tbody>
    @forelse($logbooks as $l)
        <tr><td class="text-nowrap">{{ $l->tanggal->format('d/m/Y') }}</td><td>{{ $l->judul }}</td><td>{{ $l->attachments_count }}</td>
            <td><x-status-badge :status="$l->status" /></td>
            <td class="text-end text-nowrap">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('mahasiswa.logbook.show', $l) }}">Lihat</a>
                @can('update', $l)<a class="btn btn-sm btn-outline-secondary" href="{{ route('mahasiswa.logbook.edit', $l) }}">{{ $l->status->value === 'perlu_revisi' ? 'Revisi' : 'Ubah' }}</a>@endcan
            </td></tr>
    @empty
        <tr><td colspan="5"><x-empty-state icon="journal" text="Belum ada logbook." /></td></tr>
    @endforelse</tbody></table></div>
    @if($logbooks->hasPages())<div class="card-footer">{{ $logbooks->links('partials.pagination') }}</div>@endif
</div>
@endsection
