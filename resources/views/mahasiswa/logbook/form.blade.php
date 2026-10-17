@extends('layouts.app')
@php $isEdit = $logbook->exists; @endphp
@section('title', $isEdit ? 'Ubah Logbook' : 'Tambah Logbook')
@section('heading', $isEdit ? 'Ubah Logbook' : 'Tambah Logbook')
@section('content')
@if($isEdit && $logbook->status->value === 'perlu_revisi' && $logbook->reviews->isNotEmpty())
    <div class="alert alert-danger"><strong>Dosen meminta revisi:</strong><div class="pre-wrap">{{ $logbook->reviews->first()->komentar }}</div></div>
@endif
<form method="POST" enctype="multipart/form-data" action="{{ $isEdit ? route('mahasiswa.logbook.update', $logbook) : route('mahasiswa.logbook.store') }}">
    @csrf @if($isEdit) @method('PUT') @endif
    <div class="card mb-3"><div class="card-body">
        <div class="row"><div class="col-md-4"><x-input name="tanggal" label="Tanggal kegiatan" type="date" :value="optional($logbook->tanggal)->toDateString()" max="{{ $internship->maxLogbookDate() }}" min="{{ $internship->tanggal_mulai->toDateString() }}" required /></div>
        <div class="col-md-8"><x-input name="judul" label="Judul kegiatan" :value="$logbook->judul" required /></div></div>
        <x-input name="deskripsi" label="Deskripsi kegiatan" type="textarea" rows="5" :value="$logbook->deskripsi" required />
        <x-input name="hasil" label="Hasil / capaian kegiatan" type="textarea" :value="$logbook->hasil" help="Wajib diisi saat logbook dikirim." />
        <x-input name="kendala" label="Kendala yang dihadapi" type="textarea" :value="$logbook->kendala" />
        <x-input name="rencana_tindak_lanjut" label="Rencana tindak lanjut" type="textarea" :value="$logbook->rencana_tindak_lanjut" />

        @if($isEdit && $logbook->attachments->isNotEmpty())
            <div class="mb-3"><div class="form-label">Lampiran saat ini</div>
                @foreach($logbook->attachments as $a)
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="hapus_lampiran[]" value="{{ $a->id }}" id="h{{ $a->id }}">
                        <label class="form-check-label" for="h{{ $a->id }}">Hapus: {{ $a->nama_asli }}</label></div>
                @endforeach
            </div>
        @endif
        <div class="mb-3"><label class="form-label" for="lampiran">Bukti kegiatan (foto/dokumen)</label>
            <input type="file" class="form-control @error('lampiran') is-invalid @enderror @error('lampiran.*') is-invalid @enderror" id="lampiran" name="lampiran[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
            <div class="form-text">Maksimal 5 file, masing-masing 5 MB. Format: JPG, PNG, PDF, DOC, DOCX.</div>
            @error('lampiran')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            @error('lampiran.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
    </div></div>
    <button type="submit" name="aksi" value="draft" class="btn btn-outline-secondary"><i class="bi bi-save me-1"></i>Simpan draft</button>
    <button type="submit" name="aksi" value="kirim" class="btn btn-primary" data-confirm="Kirim logbook untuk diperiksa dosen? Setelah dikirim, logbook tidak dapat diubah sampai diperiksa."><i class="bi bi-send me-1"></i>Kirim</button>
    <a href="{{ route('mahasiswa.logbook.index') }}" class="btn btn-soft">Batal</a>
</form>
@endsection
