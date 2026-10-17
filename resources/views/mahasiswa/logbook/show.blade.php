@extends('layouts.app')
@section('title', 'Detail Logbook')
@section('heading', 'Detail Logbook')
@section('actions')
    <a href="{{ route('mahasiswa.logbook.index') }}" class="btn btn-soft"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    @can('update', $logbook)
        <a href="{{ route('mahasiswa.logbook.edit', $logbook) }}" class="btn btn-outline-secondary">{{ $logbook->status->value === 'perlu_revisi' ? 'Revisi' : 'Ubah' }}</a>
        <form method="POST" action="{{ route('mahasiswa.logbook.submit', $logbook) }}" class="d-inline" data-confirm="Kirim logbook untuk diperiksa?">@csrf
            <button class="btn btn-primary"><i class="bi bi-send me-1"></i>Kirim</button></form>
    @endcan
    @can('delete', $logbook)
        <form method="POST" action="{{ route('mahasiswa.logbook.destroy', $logbook) }}" class="d-inline" data-confirm="Hapus draft ini?">@csrf @method('DELETE')
            <button class="btn btn-outline-danger"><i class="bi bi-trash"></i></button></form>
    @endcan
@endsection
@section('content')
<div class="row g-3">
    <div class="col-lg-8">@include('partials.logbook-detail')</div>
    <div class="col-lg-4">@include('partials.logbook-reviews')</div>
</div>
@endsection
