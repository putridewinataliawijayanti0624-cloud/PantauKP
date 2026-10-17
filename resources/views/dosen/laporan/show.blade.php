@extends('layouts.app')
@section('title', 'Periksa Laporan')
@section('heading', 'Laporan KP: '.$report->internship->student->name)
@section('actions')<a href="{{ route('dosen.laporan.index') }}" class="btn btn-soft"><i class="bi bi-arrow-left me-1"></i>Kembali</a>@endsection
@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card"><div class="card-header d-flex justify-content-between"><span class="fw-semibold">Riwayat versi</span><x-status-badge :status="$report->status" /></div>
        <div class="card-body">
            @forelse($report->versions as $v)
                <div class="border rounded p-3 mb-3">
                    <div class="d-flex flex-wrap justify-content-between gap-2">
                        <div><strong>Versi {{ $v->versi }}</strong> @if($v->versi === $report->versi_terbaru)<span class="badge text-bg-primary">terbaru</span>@endif
                            <div class="small text-muted">{{ $v->nama_asli }} &middot; {{ number_format($v->ukuran / 1024, 0) }} KB &middot; {{ $v->uploaded_at->translatedFormat('d M Y H:i') }}</div></div>
                        <div><a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('files.laporan', $v) }}">Buka</a>
                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('files.laporan', [$v, 'unduh' => 1]) }}">Unduh</a></div>
                    </div>
                    @foreach($v->reviews as $rv)
                        <div class="border-start border-3 border-{{ $rv->keputusan === 'disetujui' ? 'success' : 'danger' }} ps-3 mt-2">
                            <span class="badge text-bg-{{ $rv->keputusan === 'disetujui' ? 'success' : 'danger' }}">{{ $rv->keputusan === 'disetujui' ? 'Disetujui' : 'Perlu revisi' }}</span>
                            <small class="text-muted ms-1">{{ $rv->reviewer->name }} &middot; {{ $rv->created_at->translatedFormat('d M Y H:i') }}</small>
                            @if($rv->komentar)<div class="pre-wrap">{{ $rv->komentar }}</div>@endif
                        </div>
                    @endforeach
                </div>
            @empty
                <x-empty-state icon="file-earmark" text="Belum ada versi laporan." />
            @endforelse
        </div></div>
    </div>
    <div class="col-lg-4">
        @can('review', $report)
            @include('partials.review-form', ['action' => route('dosen.laporan.review', $report)])
        @else
            <div class="alert alert-info">Laporan berstatus <strong>{{ $report->status->label() }}</strong>, tidak ada yang perlu diperiksa saat ini.</div>
        @endcan
    </div>
</div>
@endsection
