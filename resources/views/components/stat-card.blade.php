@props(['icon', 'label', 'value', 'color' => 'primary', 'href' => null])
<div class="card h-100 position-relative">
    <div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon c-{{ $color }}"><i class="bi bi-{{ $icon }}"></i></div>
        <div class="min-w-0">
            <div class="stat-value">{{ $value }}</div>
            <div class="stat-label">{{ $label }}</div>
        </div>
        @if($href)<a href="{{ $href }}" class="stretched-link" aria-label="{{ $label }}"></a>@endif
    </div>
</div>
