{{-- Variabel: $action --}}
<div class="card mb-3 border-primary">
    <div class="card-header bg-primary text-white fw-semibold">Pemeriksaan</div>
    <div class="card-body">
        <form method="POST" action="{{ $action }}" id="reviewForm">
            @csrf
            <x-input name="komentar" label="Komentar / alasan" type="textarea" rows="4" help="Wajib diisi jika meminta revisi." />
            @error('keputusan')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
            <div class="d-grid gap-2">
                <button type="submit" name="keputusan" value="disetujui" class="btn btn-success" data-confirm="Setujui? Setelah disetujui, data tidak dapat diubah lagi oleh mahasiswa."><i class="bi bi-check-lg me-1"></i>Setujui</button>
                <button type="submit" name="keputusan" value="perlu_revisi" class="btn btn-outline-danger" data-confirm="Minta mahasiswa merevisi?"><i class="bi bi-arrow-repeat me-1"></i>Minta revisi</button>
            </div>
        </form>
    </div>
</div>
