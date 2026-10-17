@extends('layouts.app')
@section('title', 'Tambah Mahasiswa')
@section('heading', 'Tambah Mahasiswa Bimbingan')
@section('content')
<form method="POST" action="{{ route('dosen.mahasiswa.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-lg-6"><div class="card"><div class="card-header fw-semibold">Akun & data akademik</div><div class="card-body">
            <x-input name="name" label="Nama lengkap" required />
            <x-input name="email" label="Email" type="email" required />
            <x-input name="password" label="Password awal" type="password" required help="Minimal 8 karakter. Sampaikan ke mahasiswa dan minta diganti saat login pertama." />
            <x-input name="nim" label="NIM" required />
            <x-input name="program_studi" label="Program studi" :value="auth()->user()->lecturerProfile?->program_studi" required />
            <x-input name="no_hp" label="Nomor HP" />
        </div></div></div>
        <div class="col-lg-6"><div class="card"><div class="card-header fw-semibold">Data magang</div><div class="card-body">
            <x-input name="nama_perusahaan" label="Tempat magang / perusahaan" required />
            <x-input name="alamat" label="Alamat" />
            <x-input name="pembimbing_lapangan" label="Pembimbing lapangan" />
            <div class="row">
                <div class="col-6"><x-input name="tanggal_mulai" label="Tanggal mulai" type="date" required /></div>
                <div class="col-6"><x-input name="tanggal_selesai" label="Tanggal selesai" type="date" required /></div>
            </div>
            <x-input name="target_logbook" label="Target jumlah logbook" type="number" min="1" help="Kosongkan untuk memakai jumlah hari kerja pada periode magang. Dipakai untuk menghitung progres." />
        </div></div></div>
    </div>
    <div class="mt-3"><button class="btn btn-primary">Simpan</button> <a href="{{ route('dosen.mahasiswa.index') }}" class="btn btn-soft">Batal</a></div>
</form>
@endsection
