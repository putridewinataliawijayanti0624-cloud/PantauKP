@extends('layouts.app')
@section('title', 'Mahasiswa Bimbingan')
@section('heading', 'Mahasiswa Bimbingan')
@section('actions')<a href="{{ route('dosen.mahasiswa.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Tambah Mahasiswa</a>@endsection
@section('content')
@php
    $sortLink = fn ($key, $label) => '<a href="'.e(request()->fullUrlWithQuery(['sort' => $key, 'dir' => ($sort === $key && $dir === 'asc') ? 'desc' : 'asc'])).'">'.$label.($sort === $key ? ' <i class="bi bi-caret-'.($dir === 'asc' ? 'up' : 'down').'-fill small"></i>' : '').'</a>';
@endphp
<form method="GET" class="card card-body mb-3">
    <div class="row g-2">
        <div class="col-md-6"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama, NIM, atau tempat magang..."></div>
        <div class="col-md-4">
            <select name="status" class="form-select"><option value="">Semua status magang</option>
                @foreach(\App\Models\Internship::STATUS_LABEL as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary">Cari</button></div>
    </div>
</form>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="kp-thead"><tr>
                <th>{!! $sortLink('nama', 'Nama') !!}</th><th>{!! $sortLink('nim', 'NIM') !!}</th><th>Program studi</th><th>Tempat magang</th>
                <th>{!! $sortLink('mulai', 'Periode') !!}</th><th>Status</th><th style="min-width:130px">{!! $sortLink('progres', 'Progres') !!}</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($internships as $i)
                <tr>
                    <td class="fw-semibold">{{ $i->student->name }}</td>
                    <td>{{ $i->student->studentProfile?->nim }}</td>
                    <td>{{ $i->student->studentProfile?->program_studi }}</td>
                    <td>{{ $i->nama_perusahaan }}</td>
                    <td class="text-nowrap small">{{ $i->tanggal_mulai->format('d/m/Y') }} &ndash; {{ $i->tanggal_selesai->format('d/m/Y') }}</td>
                    <td><span class="badge text-bg-{{ $i->status_color }}">{{ $i->status_label }}</span></td>
                    <td><div class="progress" style="height:8px"><div class="progress-bar" style="width:{{ $i->progress }}%"></div></div><small class="text-muted">{{ $i->progress }}%</small></td>
                    <td class="text-end"><a href="{{ route('dosen.mahasiswa.show', $i) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="8"><x-empty-state icon="people" text="Tidak ada mahasiswa yang cocok." /></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($internships->hasPages())<div class="card-footer">{{ $internships->links('partials.pagination') }}</div>@endif
</div>
@endsection
