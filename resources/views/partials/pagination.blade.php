@if ($paginator->hasPages())
    @php $cur = $paginator->currentPage(); $last = $paginator->lastPage(); @endphp
    <nav class="kp-pager" role="navigation" aria-label="Navigasi halaman">
        <div class="kp-pager-group">
            @if ($paginator->onFirstPage())
                <span class="kp-pg is-disabled" aria-disabled="true"><i class="bi bi-chevron-left"></i></span>
            @else
                <a class="kp-pg" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya"><i class="bi bi-chevron-left"></i></a>
            @endif

            <span class="kp-pg is-current" aria-current="page">{{ $cur }}</span>
            <span class="kp-pager-text">dari</span>
            @if ($cur === $last)
                <span class="kp-pg is-last is-disabled" aria-disabled="true">{{ $last }}</span>
            @else
                <a class="kp-pg is-last" href="{{ $paginator->url($last) }}" aria-label="Halaman terakhir">{{ $last }}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a class="kp-pg" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya"><i class="bi bi-chevron-right"></i></a>
            @else
                <span class="kp-pg is-disabled" aria-disabled="true"><i class="bi bi-chevron-right"></i></span>
            @endif
        </div>

        <span class="kp-pager-text">Halaman {{ $cur }} dari {{ $last }}</span>

        <label class="kp-pager-jump">Halaman
            <select class="form-select form-select-sm" aria-label="Lompat ke halaman" onchange="if (this.value) window.location.href = this.value">
                @for ($p = 1; $p <= $last; $p++)
                    <option value="{{ $paginator->url($p) }}" @selected($p === $cur)>{{ $p }}</option>
                @endfor
            </select>
            dari {{ $last }}
        </label>
    </nav>
@endif
