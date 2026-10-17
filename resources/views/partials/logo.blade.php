{{-- Logo asli "PantauKP": cincin progres yang hampir penuh dengan tanda centang. --}}
@php $size = $size ?? 38; $uid = 'lg'.uniqid(); @endphp
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="PantauKP">
    <defs>
        <linearGradient id="{{ $uid }}" x1="4" y1="2" x2="36" y2="38" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#5aa0ff"/><stop offset="1" stop-color="#2a63e0"/>
        </linearGradient>
    </defs>
    <rect width="40" height="40" rx="11" fill="url(#{{ $uid }})"/>
    <circle cx="20" cy="20" r="10.5" stroke="#fff" stroke-opacity=".28" stroke-width="3.2"/>
    <path d="M20 9.5a10.5 10.5 0 1 1-9.9 7" stroke="#fff" stroke-width="3.2" stroke-linecap="round"/>
    <path d="M15.6 20.4l3.2 3.2 5.8-6.4" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
    <circle cx="10.4" cy="14.2" r="2" fill="#bfe0ff"/>
</svg>
