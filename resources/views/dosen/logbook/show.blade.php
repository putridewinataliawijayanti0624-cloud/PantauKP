@extends('layouts.app')
@section('title', 'Periksa Logbook')
@section('heading', 'Logbook: '.$logbook->internship->student->name)
@section('actions')<a href="{{ route('dosen.logbook.index') }}" class="btn btn-soft"><i class="bi bi-arrow-left me-1"></i>Kembali</a>@endsection
@section('content')
<div class="row g-3">
    <div class="col-lg-8">@include('partials.logbook-detail')</div>
    <div class="col-lg-4">
        @can('review', $logbook)
            @include('partials.review-form', ['action' => route('dosen.logbook.review', $logbook)])
        @else
            <div class="alert alert-info">Logbook ini berstatus <strong>{{ $logbook->status->label() }}</strong>, sehingga tidak dapat diperiksa saat ini.</div>
        @endcan
        @include('partials.logbook-reviews')
    </div>
</div>
@endsection
