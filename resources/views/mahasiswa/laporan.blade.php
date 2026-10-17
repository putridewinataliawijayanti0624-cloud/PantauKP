@extends('layouts.app')
@section('title', 'Laporan KP')
@section('heading', 'Laporan KP')
@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card mb-3"><div class="card-body">
            <div class="mb-2">Status: <x-status-badge :status="$status" /></div>
            @if($canUpload)
                <form method="POST" action="{{ route('mahasiswa.laporan.store') }}" enctype="multipart/form-data" class="mt-3">@csrf
                    <label class="form-label" for="file">{{ $status->value === 'perlu_revisi' ? 'Unggah versi revisi' : 'Unggah laporan (PDF)' }}</label>
                    <input type="file" id="file" name="file" accept="application/pdf,.pdf" class="form-control @error('file') is-invalid @enderror" required>
                    <div class="form-text">Format PDF, maksimal 10 MB.</div>
                    @error('file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <button class="btn btn-primary mt-3"><i class="bi bi-upload me-1"></i>Unggah</button>
                </form>
            @elseif($status->value === 'menunggu')
                <div class="alert alert-warning mb-0 mt-2">Laporan sedang menunggu pemeriksaan dosen.</div>
            @elseif($status->value === 'disetujui')
                <div class="alert alert-success mb-0 mt-2">Laporan Anda telah disetujui.</div>
            @endif
        </div></div>
    </div>
    <div class="col-lg-7">
        <div class="card"><div class="card-header fw-semibold">Riwayat versi &amp; feedback</div><div class="card-body">
            @forelse($report?->versions ?? [] as $v)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex flex-wrap justify-content-between gap-2"><div><strong>Versi {{ $v->versi }}</strong>
                        <div class="small text-muted">{{ $v->nama_asli }} &middot; {{ $v->uploaded_at->translatedFormat('d M Y H:i') }}</div></div>
                        <div><a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('files.laporan', $v) }}">Buka</a></div></div>
                    @foreach($v->reviews as $rv)
                        <div class="border-start border-3 border-{{ $rv->keputusan === 'disetujui' ? 'success' : 'danger' }} ps-3 mt-2">
                            <span class="badge text-bg-{{ $rv->keputusan === 'disetujui' ? 'success' : 'danger' }}">{{ $rv->keputusan === 'disetujui' ? 'Disetujui' : 'Perlu revisi' }}</span>
                            <small class="text-muted ms-1">{{ $rv->reviewer->name }} &middot; {{ $rv->created_at->translatedFormat('d M Y H:i') }}</small>
                            @if($rv->komentar)<div class="pre-wrap">{{ $rv->komentar }}</div>@endif</div>
                    @endforeach
                </div>
            @empty
                <x-empty-state icon="file-earmark-arrow-up" text="Belum ada laporan yang diunggah." />
            @endforelse
        </div></div>
    </div>
</div>
@endsection
