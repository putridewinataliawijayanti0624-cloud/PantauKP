@extends('layouts.app')
@section('title', 'Riwayat Aktivitas')
@section('heading', 'Riwayat Aktivitas')
@section('content')
<form method="GET" class="card card-body mb-3">
    <div class="row g-2">
        @if(auth()->user()->isDosen())
        <div class="col-md-5">
            <select name="internship_id" class="form-select">
                <option value="">Semua mahasiswa</option>
                @foreach($students as $s)<option value="{{ $s->id }}" @selected(request('internship_id') == $s->id)>{{ $s->student->name }}</option>@endforeach
            </select>
        </div>
        @endif
        <div class="col-md-5">
            <select name="aksi" class="form-select">
                <option value="">Semua jenis aktivitas</option>
                @foreach(\App\Models\ActivityLog::LABELS as $k => $v)<option value="{{ $k }}" @selected(request('aksi') === $k)>{{ $v[0] }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary">Filter</button></div>
    </div>
</form>
<div class="card"><div class="card-body">
    @include('partials.activity-list', ['logs' => $logs, 'showStudent' => auth()->user()->isDosen()])
    <div class="mt-3">{{ $logs->links('partials.pagination') }}</div>
</div></div>
@endsection
