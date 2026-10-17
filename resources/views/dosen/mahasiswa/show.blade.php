@extends('layouts.app')
@section('title', $internship->student->name)
@section('heading', $internship->student->name)
@section('actions')<a href="{{ route('dosen.mahasiswa.index') }}" class="btn btn-soft"><i class="bi bi-arrow-left me-1"></i>Kembali</a>@endsection
@section('content')
@php $report = $internship->report; $rs = $internship->reportStatus(); @endphp
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><x-stat-card icon="journal-check" label="Logbook dikirim" :value="$internship->total_count" /></div>
    <div class="col-6 col-lg-3"><x-stat-card icon="check-circle" label="Disetujui" :value="$internship->approved_count" color="success" /></div>
    <div class="col-6 col-lg-3"><x-stat-card icon="hourglass-split" label="Menunggu / revisi" :value="$internship->menunggu_count.' / '.$internship->revisi_count" color="warning" /></div>
    <div class="col-6 col-lg-3"><x-stat-card icon="graph-up" label="Progres" :value="$internship->progress.'%'" color="info" /></div>
</div>

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tInfo">Informasi</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tLog">Logbook</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tLap">Laporan KP</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tAkt">Aktivitas</button></li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="tInfo"><div class="card"><div class="card-body"><dl class="row mb-0">
        <dt class="col-sm-3">NIM</dt><dd class="col-sm-9">{{ $internship->student->studentProfile?->nim }}</dd>
        <dt class="col-sm-3">Program studi</dt><dd class="col-sm-9">{{ $internship->student->studentProfile?->program_studi }}</dd>
        <dt class="col-sm-3">Email / HP</dt><dd class="col-sm-9">{{ $internship->student->email }} / {{ $internship->student->studentProfile?->no_hp ?: '-' }}</dd>
        <dt class="col-sm-3">Tempat magang</dt><dd class="col-sm-9">{{ $internship->nama_perusahaan }}<br><small class="text-muted">{{ $internship->alamat }}</small></dd>
        <dt class="col-sm-3">Pembimbing lapangan</dt><dd class="col-sm-9">{{ $internship->pembimbing_lapangan ?: '-' }}</dd>
        <dt class="col-sm-3">Periode</dt><dd class="col-sm-9">{{ $internship->tanggal_mulai->translatedFormat('d F Y') }} &ndash; {{ $internship->tanggal_selesai->translatedFormat('d F Y') }}</dd>
        <dt class="col-sm-3">Status magang</dt><dd class="col-sm-9"><span class="badge text-bg-{{ $internship->status_color }}">{{ $internship->status_label }}</span></dd>
        <dt class="col-sm-3">Dosen pembimbing</dt><dd class="col-sm-9">{{ $internship->lecturer?->name }}</dd>
        <dt class="col-sm-3">Target logbook</dt><dd class="col-sm-9">{{ $internship->target_logbook }}</dd>
    </dl></div></div></div>

    <div class="tab-pane fade" id="tLog"><div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="kp-thead"><tr><th>Tanggal</th><th>Judul</th><th>Lampiran</th><th>Status</th><th></th></tr></thead><tbody>
        @forelse($logbooks as $l)
            <tr><td class="text-nowrap">{{ $l->tanggal->format('d/m/Y') }}</td><td>{{ $l->judul }}</td><td>{{ $l->attachments_count }}</td>
                <td><x-status-badge :status="$l->status" /></td>
                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('dosen.logbook.show', $l) }}">{{ $l->status->value === 'menunggu' ? 'Periksa' : 'Lihat' }}</a></td></tr>
        @empty
            <tr><td colspan="5"><x-empty-state icon="journal" text="Belum ada logbook yang dikirim." /></td></tr>
        @endforelse</tbody></table></div>
        @if($logbooks->hasPages())<div class="card-footer">{{ $logbooks->links('partials.pagination') }}</div>@endif</div></div>

    <div class="tab-pane fade" id="tLap"><div class="card"><div class="card-body">
        <div class="mb-3">Status: <x-status-badge :status="$rs" /></div>
        @if($report && $report->versions->isNotEmpty())
            <a href="{{ route('dosen.laporan.show', $report) }}" class="btn btn-primary mb-3">Buka halaman pemeriksaan laporan</a>
            @foreach($report->versions as $v)
                <div class="border rounded p-2 mb-2">Versi {{ $v->versi }} &middot; {{ $v->nama_asli }} &middot; {{ $v->uploaded_at->translatedFormat('d M Y H:i') }}
                    @foreach($v->reviews as $rv)<div class="small mt-1 text-muted">{{ $rv->keputusan === 'disetujui' ? 'Disetujui' : 'Revisi' }} oleh {{ $rv->reviewer->name }}: {{ $rv->komentar ?: '-' }}</div>@endforeach</div>
            @endforeach
        @else
            <x-empty-state icon="file-earmark" text="Mahasiswa belum mengunggah laporan." />
        @endif
    </div></div></div>

    <div class="tab-pane fade" id="tAkt"><div class="card"><div class="card-body">@include('partials.activity-list', ['logs' => $logs])</div></div></div>
</div>
@endsection
