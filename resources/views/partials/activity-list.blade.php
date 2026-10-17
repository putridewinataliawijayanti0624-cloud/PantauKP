{{-- Variabel: $logs (collection), $showStudent (bool) --}}
@forelse($logs as $log)
    <div class="d-flex gap-3 py-2 border-bottom">
        <div class="stat-icon c-{{ $log->color }} flex-shrink-0" style="width:36px;height:36px;font-size:1rem"><i class="bi bi-{{ $log->icon }}"></i></div>
        <div class="flex-grow-1">
            <div><strong>{{ $log->user?->name ?? 'Pengguna dihapus' }}</strong> {{ lcfirst($log->label) }}
                @if(!empty($log->metadata['judul']))<em>&ldquo;{{ $log->metadata['judul'] }}&rdquo;</em>@endif
                @if(!empty($log->metadata['versi'])) <span class="text-muted">(versi {{ $log->metadata['versi'] }})</span>@endif</div>
            @if(!empty($showStudent) && $log->internship?->student)<div class="small text-muted">Mahasiswa: {{ $log->internship->student->name }}</div>@endif
            @if(!empty($log->metadata['komentar']))<div class="small text-muted pre-wrap">&ldquo;{{ \Illuminate\Support\Str::limit($log->metadata['komentar'], 140) }}&rdquo;</div>@endif
        </div>
        <small class="text-muted text-nowrap">{{ $log->created_at->diffForHumans() }}</small>
    </div>
@empty
    <x-empty-state icon="clock-history" text="Belum ada aktivitas." />
@endforelse
