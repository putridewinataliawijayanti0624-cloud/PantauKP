<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
            <div>
                <h2 class="h5 mb-1">{{ $logbook->judul }}</h2>
                <div class="text-muted small"><i class="bi bi-calendar-event me-1"></i>{{ $logbook->tanggal->translatedFormat('l, d F Y') }}
                    @if($logbook->submitted_at) &middot; dikirim {{ $logbook->submitted_at->translatedFormat('d M Y H:i') }} (pengiriman ke-{{ $logbook->submission_number }})@endif</div>
            </div>
            <div><x-status-badge :status="$logbook->status" /></div>
        </div>
        @foreach(['deskripsi' => 'Deskripsi kegiatan', 'hasil' => 'Hasil / capaian', 'kendala' => 'Kendala yang dihadapi', 'rencana_tindak_lanjut' => 'Rencana tindak lanjut'] as $f => $l)
            <div class="mb-3">
                <div class="fw-semibold small text-uppercase text-muted">{{ $l }}</div>
                <div class="pre-wrap">{{ $logbook->$f ?: '-' }}</div>
            </div>
        @endforeach
        <div class="fw-semibold small text-uppercase text-muted mb-2">Bukti kegiatan</div>
        @forelse($logbook->attachments as $a)
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <i class="bi bi-{{ $a->isImage() ? 'image' : 'file-earmark-text' }}"></i>
                <span>{{ $a->nama_asli }}</span> <small class="text-muted">({{ number_format($a->ukuran / 1024, 0) }} KB)</small>
                <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('files.logbook', $a) }}">Buka</a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('files.logbook', [$a, 'unduh' => 1]) }}">Unduh</a>
            </div>
            @if($a->isImage())
                <a href="{{ route('files.logbook', $a) }}" target="_blank" title="Klik untuk membuka ukuran penuh"><img src="{{ route('files.logbook', $a) }}" alt="{{ $a->nama_asli }}" class="img-thumbnail mb-3" style="max-height:220px"></a>
            @endif
        @empty
            <div class="text-muted">Tidak ada lampiran.</div>
        @endforelse
    </div>
</div>
