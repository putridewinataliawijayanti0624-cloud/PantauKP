@extends('layouts.app')
@php
    $isEdit = $user->exists;
    $role = $isEdit ? $user->role->value : old('role', 'mahasiswa');
    $sp = $user->studentProfile; $lp = $user->lecturerProfile;
@endphp
@section('title', $isEdit ? 'Ubah Pengguna' : 'Tambah Pengguna')
@section('heading', $isEdit ? 'Ubah Pengguna: '.$user->name : 'Tambah Pengguna')
@section('actions')<a href="{{ route('admin.users.index') }}" class="btn btn-soft"><i class="bi bi-arrow-left me-1"></i>Kembali</a>@endsection
@section('content')
<div class="row g-3">
    <div class="col-lg-{{ $isEdit ? 7 : 8 }}">
        <form method="POST" action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}" autocomplete="off">
            @csrf @if($isEdit) @method('PUT') @endif
            <div class="card mb-3"><div class="card-header">Akun</div><div class="card-body">
                @if(! $isEdit)
                    <div class="mb-3"><label class="form-label" for="role">Peran <span class="text-danger">*</span></label>
                        <select id="role" name="role" class="form-select @error('role') is-invalid @enderror">
                            @foreach(['mahasiswa' => 'Mahasiswa', 'dosen' => 'Dosen', 'admin' => 'Admin'] as $k => $l)<option value="{{ $k }}" @selected($role === $k)>{{ $l }}</option>@endforeach
                        </select>@error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                @else
                    <div class="mb-3">Peran: <span class="badge text-bg-primary text-capitalize">{{ $user->role->value }}</span> <small class="text-muted ms-1">(peran tidak dapat diubah)</small></div>
                @endif
                <x-input name="name" label="Nama lengkap" :value="$user->name" required />
                <x-input name="email" label="Email" type="email" :value="$user->email" required />
                @unless($isEdit)<x-input name="password" label="Password awal" type="password" required help="Minimal 8 karakter. Sampaikan ke pengguna dan minta diganti setelah login." />@endunless
                @if($isEdit)
                    <input type="hidden" name="is_active" value="0">
                    <div class="form-check form-switch mb-1"><input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $user->is_active ? '1' : '0') == '1') @disabled($user->is(auth()->user()))>
                        <label class="form-check-label" for="is_active">Akun aktif</label></div>
                    @if($user->is(auth()->user()))<input type="hidden" name="is_active" value="1">@endif
                    @error('is_active')<div class="text-danger small">{{ $message }}</div>@enderror
                @endif
            </div></div>

            <fieldset id="f-dosen" class="border-0 p-0 m-0 min-w-0" @if($role !== 'dosen') hidden disabled @endif><div class="card mb-3"><div class="card-header">Data dosen</div><div class="card-body">
                <x-input name="nidn" label="NIDN" :value="$lp?->nidn" />
                <x-input name="program_studi" id="dosen_program_studi" label="Program studi" :value="$lp?->program_studi" />
            </div></div></fieldset>

            <fieldset id="f-mahasiswa" class="border-0 p-0 m-0 min-w-0" @if($role !== 'mahasiswa') hidden disabled @endif>
                <div class="card mb-3"><div class="card-header">Data mahasiswa</div><div class="card-body">
                    <div class="row"><div class="col-md-6"><x-input name="nim" label="NIM" :value="$sp?->nim" /></div><div class="col-md-6"><x-input name="no_hp" label="Nomor HP" :value="$sp?->no_hp" /></div></div>
                    <x-input name="program_studi" id="mhs_program_studi" label="Program studi" :value="$sp?->program_studi" />
                    <div class="mb-1"><label class="form-label" for="dosen_id">Dosen pembimbing @unless($isEdit)<span class="text-danger">*</span>@endunless</label>
                        <select id="dosen_id" name="dosen_id" class="form-select @error('dosen_id') is-invalid @enderror">
                            <option value="">-- pilih dosen --</option>
                            @foreach($dosens as $d)<option value="{{ $d->id }}" @selected(old('dosen_id', $internship?->lecturer?->id ?? null) == $d->id)>{{ $d->name }}</option>@endforeach
                        </select>@error('dosen_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if($isEdit)<div class="form-text">Mengganti dosen akan memindahkan pembimbing pada magang terbaru mahasiswa ini.</div>@endif</div>
                </div></div>
                @unless($isEdit)
                <div class="card mb-3"><div class="card-header">Data magang</div><div class="card-body">
                    <x-input name="nama_perusahaan" label="Tempat magang / perusahaan" />
                    <x-input name="alamat" label="Alamat" />
                    <x-input name="pembimbing_lapangan" label="Pembimbing lapangan" />
                    <div class="row"><div class="col-6"><x-input name="tanggal_mulai" label="Tanggal mulai" type="date" /></div><div class="col-6"><x-input name="tanggal_selesai" label="Tanggal selesai" type="date" /></div></div>
                    <x-input name="target_logbook" label="Target jumlah logbook" type="number" min="1" help="Kosongkan untuk memakai jumlah hari kerja pada periode magang." />
                </div></div>
                @endunless
            </fieldset>
            <button class="btn btn-primary">{{ $isEdit ? 'Simpan perubahan' : 'Buat akun' }}</button>
        </form>
    </div>

    @if($isEdit)
    <div class="col-lg-5">
        <div class="card mb-3" id="reset"><div class="card-header"><i class="bi bi-key me-2"></i>Reset password</div><div class="card-body">
            <p class="text-muted small">Password baru langsung berlaku dan sesi aktif pengguna ini akan diakhiri.</p>
            <form method="POST" action="{{ route('admin.users.password', $user) }}" autocomplete="off">@csrf @method('PUT')
                @php $re = $errors->getBag('reset'); @endphp
                @foreach(['password' => 'Password baru', 'password_confirmation' => 'Ulangi password baru'] as $n => $l)
                    <div class="mb-3"><label class="form-label">{{ $l }}</label>
                        <input type="password" name="{{ $n }}" class="form-control {{ $re->has($n) ? 'is-invalid' : '' }}" required minlength="8" autocomplete="new-password">
                        @if($re->has($n))<div class="invalid-feedback">{{ $re->first($n) }}</div>@endif</div>
                @endforeach
                <button class="btn btn-outline-primary" data-confirm="Reset password {{ $user->name }}?">Reset password</button>
            </form>
        </div></div>
        @if($internship)
        <div class="card"><div class="card-header"><i class="bi bi-building me-2"></i>Magang terbaru</div><div class="card-body small">
            <div class="fw-semibold text-body-emphasis">{{ $internship->nama_perusahaan }}</div>
            <div class="text-muted">{{ $internship->tanggal_mulai->translatedFormat('d M Y') }} &ndash; {{ $internship->tanggal_selesai->translatedFormat('d M Y') }}</div>
            <div class="mt-1">Pembimbing saat ini: <strong>{{ $internship->lecturer?->name ?? '-' }}</strong></div>
        </div></div>
        @endif
    </div>
    @endif
</div>
@endsection
@push('scripts')
@unless($isEdit)
<script>
(function () {
    var sel = document.getElementById('role'), d = document.getElementById('f-dosen'), m = document.getElementById('f-mahasiswa');
    function show(group, on) { group.hidden = !on; group.disabled = !on; }   // fieldset disabled: isian tidak ikut terkirim
    function sync() { show(d, sel.value === 'dosen'); show(m, sel.value === 'mahasiswa'); }
    sel.addEventListener('change', sync); sync();
})();
</script>
@endunless
@endpush
