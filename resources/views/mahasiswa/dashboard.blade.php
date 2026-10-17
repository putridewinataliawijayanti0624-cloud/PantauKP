@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('actions')
@if($internship)
    <a href="{{ route('mahasiswa.logbook.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Logbook</a>
    <a href="{{ route('mahasiswa.laporan') }}" class="btn btn-outline-primary"><i class="bi bi-upload me-1"></i>Laporan KP</a>
@endif
@endsection
@section('content')
@if(! $internship)
    <div class="card"><div class="card-body"><x-empty-state icon="building" text="Data magang Anda belum dibuat. Hubungi dosen pembimbing." /></div></div>
@else
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><x-stat-card icon="journal-text" label="Logbook dikirim" :value="$internship->total_count" /></div>
    <div class="col-6 col-lg-3"><x-stat-card icon="check-circle" label="Disetujui" :value="$internship->approved_count" color="success" /></div>
    <div class="col-6 col-lg-3"><x-stat-card icon="hourglass-split" label="Menunggu pemeriksaan" :value="$internship->menunggu_count" color="warning" /></div>
    <div class="col-6 col-lg-3"><x-stat-card icon="arrow-repeat" label="Perlu revisi" :value="$internship->revisi_count" color="danger" :href="route('mahasiswa.logbook.index', ['status' => 'perlu_revisi'])" /></div>
</div>
<div class="row g-3">
    <div class="col-lg-6"><div class="card h-100"><div class="card-header"><i class="bi bi-building me-2"></i>Magang saya</div><div class="card-body">
        <div class="fw-semibold text-body-emphasis">{{ $internship->nama_perusahaan }}</div>
        <div class="text-muted small mb-2">{{ $internship->tanggal_mulai->translatedFormat('d M Y') }} &ndash; {{ $internship->tanggal_selesai->translatedFormat('d M Y') }}</div>
        <div class="mb-3">Dosen pembimbing: <strong>{{ $internship->lecturer?->name ?? '-' }}</strong></div>
        <div class="d-flex justify-content-between small"><span>Progres kegiatan ({{ $internship->approved_count }}/{{ $internship->target_logbook }} logbook disetujui)</span><strong>{{ $internship->progress }}%</strong></div>
        <div class="progress mb-3" style="height:10px"><div class="progress-bar" style="width:{{ $internship->progress }}%"></div></div>
        <div>Status laporan KP: <x-status-badge :status="$internship->reportStatus()" /></div>
    </div></div></div>
    <div class="col-lg-6"><div class="card h-100"><div class="card-header"><i class="bi bi-chat-left-text me-2"></i>Feedback terbaru dari dosen</div><div class="card-body">
        @forelse($logFeedback as $f)
            <div class="border-start border-3 border-{{ $f->keputusan === 'disetujui' ? 'success' : 'danger' }} ps-3 mb-3">
                <a href="{{ route('mahasiswa.logbook.show', $f->logbook_id) }}" class="fw-semibold">{{ $f->logbook->judul }}</a>
                <span class="badge text-bg-{{ $f->keputusan === 'disetujui' ? 'success' : 'danger' }}">{{ $f->keputusan === 'disetujui' ? 'Disetujui' : 'Perlu revisi' }}</span>
                <div class="pre-wrap small">{{ $f->komentar ?: 'Tanpa komentar.' }}</div>
                <small class="text-muted">{{ $f->reviewer->name }} &middot; {{ $f->created_at->diffForHumans() }}</small>
            </div>
        @empty
            <x-empty-state icon="chat-left-text" text="Belum ada feedback logbook." />
        @endforelse
        @if($reportFeedback)
            <hr><div class="small fw-semibold">Laporan KP</div>
            <div class="pre-wrap small">{{ $reportFeedback->komentar ?: ($reportFeedback->keputusan === 'disetujui' ? 'Laporan disetujui.' : '-') }}</div>
        @endif
    </div></div></div>
</div>
@endif
@endsection
