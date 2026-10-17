@extends('layouts.app')
@section('title', 'Kelola Pengguna')
@section('heading', 'Kelola Pengguna')
@section('actions')<a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Tambah Pengguna</a>@endsection
@section('content')
<form method="GET" class="card card-body mb-3">
    <div class="row g-2">
        <div class="col-md-5"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama, email, NIM, atau NIDN..."></div>
        <div class="col-md-3"><select name="role" class="form-select"><option value="">Semua peran</option>
            @foreach(['admin' => 'Admin', 'dosen' => 'Dosen', 'mahasiswa' => 'Mahasiswa'] as $k => $l)<option value="{{ $k }}" @selected(request('role') === $k)>{{ $l }}</option>@endforeach</select></div>
        <div class="col-md-2"><select name="status" class="form-select"><option value="">Semua status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option><option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option></select></div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary">Cari</button></div>
    </div>
</form>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead class="kp-thead"><tr><th>Pengguna</th><th>Peran</th><th>NIM / NIDN</th><th>Dosen pembimbing</th><th>Status</th><th></th></tr></thead><tbody>
    @forelse($users as $u)
        @php $isSelf = $u->is(auth()->user()); $ints = $u->internships->sortByDesc('tanggal_mulai')->first(); @endphp
        <tr>
            <td><div class="d-flex align-items-center gap-3"><span class="avatar" style="width:36px;height:36px">{{ $u->initials }}</span>
                <div><div class="fw-semibold text-body-emphasis">{{ $u->name }} @if($isSelf)<span class="badge text-bg-primary">Anda</span>@endif</div><small class="text-muted">{{ $u->email }}</small></div></div></td>
            <td><span class="badge text-bg-{{ ['admin' => 'warning', 'dosen' => 'info', 'mahasiswa' => 'success'][$u->role->value] }} text-capitalize">{{ $u->role->value }}</span></td>
            <td>{{ $u->studentProfile?->nim ?? $u->lecturerProfile?->nidn ?? '-' }}<br><small class="text-muted">{{ $u->studentProfile?->program_studi ?? $u->lecturerProfile?->program_studi }}</small></td>
            <td>{{ $u->isMahasiswa() ? ($ints?->lecturer?->name ?? '-') : '' }}</td>
            <td>@if($u->is_active)<span class="badge text-bg-success">Aktif</span>@else<span class="badge text-bg-secondary">Nonaktif</span>@endif</td>
            <td class="text-end text-nowrap">
                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary" title="Ubah / reset password"><i class="bi bi-pencil-square"></i></a>
                @unless($isSelf)
                    <form method="POST" action="{{ route('admin.users.toggle', $u) }}" class="d-inline" data-confirm="{{ $u->is_active ? 'Nonaktifkan akun ini? Pengguna tidak dapat masuk lagi.' : 'Aktifkan kembali akun ini?' }}">@csrf
                        <button class="btn btn-sm btn-outline-secondary" title="{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"><i class="bi bi-toggle-{{ $u->is_active ? 'on' : 'off' }}"></i></button></form>
                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="d-inline" data-confirm="Hapus akun {{ $u->name }}? @if($u->isMahasiswa())Seluruh logbook, lampiran, dan laporannya ikut terhapus permanen.@else Tindakan ini tidak dapat dibatalkan.@endif">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button></form>
                @endunless
            </td>
        </tr>
    @empty
        <tr><td colspan="6"><x-empty-state icon="people" text="Tidak ada pengguna yang cocok." /></td></tr>
    @endforelse</tbody></table></div>
    @if($users->hasPages())<div class="card-footer">{{ $users->links('partials.pagination') }}</div>@endif
</div>
@endsection
