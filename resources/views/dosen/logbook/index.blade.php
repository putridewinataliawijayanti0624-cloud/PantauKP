@extends('layouts.app')
@section('title', 'Logbook Mahasiswa')
@section('heading', 'Logbook Mahasiswa')
@section('content')
<form method="GET" class="card card-body mb-3">
    <div class="row g-2">
        <div class="col-md-3"><select name="internship_id" class="form-select"><option value="">Semua mahasiswa</option>
            @foreach($students as $s)<option value="{{ $s->id }}" @selected(request('internship_id') == $s->id)>{{ $s->student->name }}</option>@endforeach</select></div>
        <div class="col-md-3"><select name="status" class="form-select"><option value="">Semua status</option>
            @foreach(\App\Enums\LogbookStatus::cases() as $st)@continue($st->value === 'draft')<option value="{{ $st->value }}" @selected(request('status') === $st->value)>{{ $st->label() }}</option>@endforeach</select></div>
        <div class="col-md-2"><input type="date" name="dari" value="{{ request('dari') }}" class="form-control" title="Dari tanggal"></div>
        <div class="col-md-2"><input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control" title="Sampai tanggal"></div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary">Filter</button></div>
    </div>
</form>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead class="kp-thead"><tr><th>Tanggal</th><th>Mahasiswa</th><th>Judul</th><th>Lampiran</th><th>Status</th><th></th></tr></thead><tbody>
    @forelse($logbooks as $l)
        <tr><td class="text-nowrap">{{ $l->tanggal->format('d/m/Y') }}</td><td>{{ $l->internship->student->name }}</td><td>{{ $l->judul }}</td><td>{{ $l->attachments_count }}</td>
            <td><x-status-badge :status="$l->status" /></td>
            <td class="text-end"><a class="btn btn-sm btn-{{ $l->status->value === 'menunggu' ? 'primary' : 'outline-primary' }}" href="{{ route('dosen.logbook.show', $l) }}">{{ $l->status->value === 'menunggu' ? 'Periksa' : 'Lihat' }}</a></td></tr>
    @empty
        <tr><td colspan="6"><x-empty-state icon="journal" text="Tidak ada logbook yang cocok dengan filter." /></td></tr>
    @endforelse</tbody></table></div>
    @if($logbooks->hasPages())<div class="card-footer">{{ $logbooks->links('partials.pagination') }}</div>@endif
</div>
@endsection
