<div class="card mb-3">
    <div class="card-header fw-semibold">Riwayat pemeriksaan</div>
    <div class="card-body">
        @forelse($logbook->reviews as $r)
            <div class="border-start border-3 border-{{ $r->keputusan === 'disetujui' ? 'success' : 'danger' }} ps-3 mb-3">
                <div class="d-flex justify-content-between flex-wrap">
                    <span class="badge text-bg-{{ $r->keputusan === 'disetujui' ? 'success' : 'danger' }}">{{ $r->keputusan === 'disetujui' ? 'Disetujui' : 'Perlu revisi' }}</span>
                    <small class="text-muted">{{ $r->created_at->translatedFormat('d M Y H:i') }}</small>
                </div>
                <div class="small text-muted mt-1">{{ $r->reviewer->name }} &middot; pengiriman ke-{{ $r->submission_number }}</div>
                @if($r->komentar)<div class="pre-wrap mt-1">{{ $r->komentar }}</div>@endif
                @if($r->snapshot)
                    <a class="small" data-bs-toggle="collapse" href="#snap{{ $r->id }}">Lihat isi saat diperiksa</a>
                    <div class="collapse mt-2" id="snap{{ $r->id }}">
                        <div class="bg-body-tertiary rounded p-2 small">
                            <div class="fw-semibold">{{ $r->snapshot['judul'] ?? '' }}</div>
                            <div class="pre-wrap">{{ $r->snapshot['deskripsi'] ?? '' }}</div>
                            @if(!empty($r->snapshot['hasil']))<div class="mt-1"><b>Hasil:</b> <span class="pre-wrap">{{ $r->snapshot['hasil'] }}</span></div>@endif
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-muted">Belum ada pemeriksaan.</div>
        @endforelse
    </div>
</div>
