@extends('layouts.app')
@section('title', 'Info Magang')
@section('heading', 'Informasi Magang')
@section('content')
@if(! $internship)
    <div class="card"><div class="card-body"><x-empty-state icon="building" text="Data magang belum tersedia." /></div></div>
@else
<div class="card"><div class="card-body"><dl class="row mb-0">
    <dt class="col-sm-3">Tempat magang</dt><dd class="col-sm-9">{{ $internship->nama_perusahaan }}</dd>
    <dt class="col-sm-3">Alamat</dt><dd class="col-sm-9">{{ $internship->alamat ?: '-' }}</dd>
    <dt class="col-sm-3">Pembimbing lapangan</dt><dd class="col-sm-9">{{ $internship->pembimbing_lapangan ?: '-' }}</dd>
    <dt class="col-sm-3">Periode</dt><dd class="col-sm-9">{{ $internship->tanggal_mulai->translatedFormat('d F Y') }} &ndash; {{ $internship->tanggal_selesai->translatedFormat('d F Y') }}</dd>
    <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><span class="badge text-bg-{{ $internship->status_color }}">{{ $internship->status_label }}</span></dd>
    <dt class="col-sm-3">Dosen pembimbing</dt><dd class="col-sm-9">{{ $internship->lecturer?->name }} <small class="text-muted">{{ $internship->lecturer?->email }}</small></dd>
    <dt class="col-sm-3">Target logbook</dt><dd class="col-sm-9">{{ $internship->target_logbook }}</dd>
</dl></div></div>
@endif
@endsection
