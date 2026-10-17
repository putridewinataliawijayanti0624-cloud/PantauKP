@extends('layouts.app')
@section('title', 'Profil')
@section('heading', 'Profil & Password')
@section('content')
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card"><div class="card-header"><i class="bi bi-person me-2"></i>Data akun</div><div class="card-body">
            <form method="POST" action="{{ route('profile.update') }}">@csrf @method('PUT')
                <x-input name="name" label="Nama" :value="$user->name" required />
                <x-input name="email" label="Email" type="email" :value="$user->email" required />
                <div class="mb-3"><label class="form-label">Peran</label><input class="form-control text-capitalize" value="{{ $user->role->value }}" disabled></div>
                @if($user->isMahasiswa())
                    <div class="mb-3"><label class="form-label">NIM</label><input class="form-control" value="{{ $user->studentProfile?->nim }}" disabled></div>
                    <x-input name="no_hp" label="Nomor HP" :value="$user->studentProfile?->no_hp" />
                @elseif($user->isDosen())
                    <div class="mb-3"><label class="form-label">NIDN</label><input class="form-control" value="{{ $user->lecturerProfile?->nidn }}" disabled></div>
                @endif
                <button class="btn btn-primary">Simpan</button>
            </form>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card" id="password"><div class="card-header"><i class="bi bi-key me-2"></i>Ganti password</div><div class="card-body">
            <form method="POST" action="{{ route('profile.password') }}">@csrf @method('PUT')
                @php $pe = $errors->getBag('password'); @endphp
                @foreach(['current_password' => 'Password saat ini', 'password' => 'Password baru (min. 8 karakter)', 'password_confirmation' => 'Ulangi password baru'] as $n => $l)
                    <div class="mb-3"><label class="form-label">{{ $l }}</label>
                        <input type="password" name="{{ $n }}" class="form-control {{ $pe->has($n) ? 'is-invalid' : '' }}" required autocomplete="{{ $n === 'current_password' ? 'current-password' : 'new-password' }}">
                        @if($pe->has($n))<div class="invalid-feedback">{{ $pe->first($n) }}</div>@endif</div>
                @endforeach
                <button class="btn btn-primary">Ganti password</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
