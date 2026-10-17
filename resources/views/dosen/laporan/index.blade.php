@extends('layouts.app')
@section('title', 'Laporan KP')
@section('heading', 'Laporan KP Mahasiswa')
@section('content')
<form method="GET" class="card card-body mb-3"><div class="row g-2">
    <div class="col-md-4"><select name="status" class="form-select"><option value="">Semua status</option>
        @foreach(\App\Enums\ReportStatus::cases() as $st)<option value="{{ $st->value }}" @selected(request('status') === $st->value)>{{ $st->label() }}</option>@endforeach</select></div>
    <div class="col-md-2 d-grid"><button class="btn btn-primary">Filter</button></div>
</div></form>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead class="kp-thead"><tr><th>Mahasiswa</th><th>Versi terbaru</th><th>Diunggah</th><th>Status</th><th></th></tr></thead><tbody>
    @forelse($reports as $r)
        <tr><td>{{ $r->internship->student->name }}</td>
            <td>{{ $r->versi_terbaru ? 'v'.$r->versi_terbaru : '-' }}</td>
            <td>{{ $r->latestVersion?->uploaded_at?->translatedFormat('d M Y H:i') ?? '-' }}</td>
            <td><x-status-badge :status="$r->status" /></td>
            <td class="text-end">@if($r->versi_terbaru)<a class="btn btn-sm btn-{{ $r->status->value === 'menunggu' ? 'primary' : 'outline-primary' }}" href="{{ route('dosen.laporan.show', $r) }}">{{ $r->status->value === 'menunggu' ? 'Periksa' : 'Lihat' }}</a>@endif</td></tr>
    @empty
        <tr><td colspan="5"><x-empty-state icon="file-earmark" text="Belum ada laporan." /></td></tr>
    @endforelse</tbody></table></div>
    @if($reports->hasPages())<div class="card-footer">{{ $reports->links('partials.pagination') }}</div>@endif
</div>
@endsection
