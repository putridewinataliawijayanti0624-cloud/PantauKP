<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | PantauKP</title>
    @include('layouts._theme')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sb-w: 260px; --sb-w-min: 72px; }
        /* ---------- Sidebar ---------- */
        .sidebar { background: var(--kp-sidebar) !important; border-right: 1px solid var(--kp-border); --bs-offcanvas-bg: var(--kp-sidebar); --bs-offcanvas-width: 270px; --bs-offcanvas-color: var(--kp-text); }
        .sidebar-inner { display: flex; flex-direction: column; height: 100%; width: 100%; padding: 1rem .75rem; overflow-y: auto; overflow-x: hidden; }
        .sb-head { display: flex; align-items: center; gap: .7rem; padding: .2rem .55rem 1rem; min-height: 56px; }
        .sb-brand { font-size: 1.3rem; font-weight: 700; color: var(--kp-heading); letter-spacing: -.01em; white-space: nowrap; flex: 1; line-height: 1; }
        .sb-brand b { color: var(--kp-primary); }
        .sb-toggle { display: none; background: none; border: 0; color: var(--kp-muted); font-size: 1.15rem; padding: .2rem; line-height: 1; }
        .sb-toggle:hover { color: var(--kp-primary); }
        .menu-heading { display: flex; align-items: center; padding: 1.1rem .75rem .45rem; font-size: .72rem; letter-spacing: .08em; text-transform: uppercase; color: var(--kp-muted); font-weight: 600; white-space: nowrap; }
        .menu-heading .line { display: none; height: 1px; background: var(--kp-border); width: 100%; margin: .2rem 0; }
        .menu-link { display: flex; align-items: center; gap: .85rem; padding: .62rem .8rem; margin-bottom: .2rem; border-radius: .4rem; color: var(--kp-text); text-decoration: none; white-space: nowrap; font-weight: 500; transition: background .15s, color .15s; }
        .menu-link i { font-size: 1.15rem; width: 1.3rem; text-align: center; color: var(--kp-muted); flex: 0 0 auto; transition: color .15s; }
        .menu-link:hover { background: var(--kp-surface-2); color: var(--kp-heading); }
        .menu-link:hover i { color: var(--kp-primary); }
        .menu-link.active { background: var(--kp-grad); color: #fff; box-shadow: 0 4px 12px -2px var(--kp-glow); }
        .menu-link.active i { color: #fff; }

        /* ---------- Layout utama ---------- */
        .main { min-height: 100vh; display: flex; flex-direction: column; }
        .content { width: 100%; max-width: 1480px; margin: 0 auto; padding: 0 1.5rem; flex: 1; }
        .topbar { position: sticky; top: .75rem; z-index: 1020; margin-top: .9rem; background: var(--kp-surface); border: 1px solid var(--kp-card-border); border-radius: .4rem; box-shadow: var(--kp-shadow); padding: .55rem .9rem; display: flex; align-items: center; gap: .6rem; }
        .content-footer { padding: 2rem 1.5rem 1.2rem; color: var(--kp-muted); font-size: .88rem; }
        .content-footer strong { color: var(--kp-primary); }
        .page-head { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: .75rem; margin: 1.4rem 0 1.1rem; }
        .page-head h1 { font-size: 1.35rem; margin: 0; }
        .avatar-btn { background: none; border: 0; padding: 0; border-radius: 50%; }
        .avatar-btn:focus-visible { outline: 2px solid var(--kp-primary); outline-offset: 2px; }

        @media (min-width: 992px) {
            .sidebar { position: fixed; top: 0; left: 0; bottom: 0; width: var(--sb-w); z-index: 1030; transition: width .22s ease, box-shadow .22s; }
            .main { margin-left: var(--sb-w); transition: margin-left .22s ease; }
            .sb-close { display: none !important; }
            .sb-toggle { display: inline-block; }

            /* collapsed: hanya ikon; hover = melebar sementara (overlay) */
            html.sb-collapsed { --sb-w: var(--sb-w-min); }
            html.sb-collapsed .sidebar .label, html.sb-collapsed .sidebar .sb-brand, html.sb-collapsed .sidebar .sb-toggle, html.sb-collapsed .sidebar .menu-heading .text { display: none; }
            html.sb-collapsed .sidebar .menu-heading { padding: .6rem .4rem; }
            html.sb-collapsed .sidebar .menu-heading .line { display: block; }
            html.sb-collapsed .sidebar .menu-link { justify-content: center; padding: .62rem; }
            html.sb-collapsed .sidebar .sb-head { justify-content: center; padding-inline: 0; }
            html.sb-collapsed .sidebar:hover { width: 260px; box-shadow: 8px 0 28px rgba(10, 16, 40, .18); }
            html.sb-collapsed .sidebar:hover .label, html.sb-collapsed .sidebar:hover .sb-brand { display: inline; }
            html.sb-collapsed .sidebar:hover .sb-toggle { display: inline-block; }
            html.sb-collapsed .sidebar:hover .menu-heading .text { display: inline; }
            html.sb-collapsed .sidebar:hover .menu-heading .line { display: none; }
            html.sb-collapsed .sidebar:hover .menu-heading { padding: 1.1rem .75rem .45rem; }
            html.sb-collapsed .sidebar:hover .menu-link { justify-content: flex-start; padding: .62rem .8rem; }
            html.sb-collapsed .sidebar:hover .sb-head { justify-content: flex-start; padding-inline: .55rem; }
        }
        @media (max-width: 991.98px) { .content { padding: 0 .85rem; } .topbar { top: .5rem; } }
    </style>
    @stack('head')
</head>
<body>
@php
    $u = auth()->user();
    $p = $u->role->value;
    $menus = [
        'admin' => [
            ['Dashboard', [['admin.dashboard', 'admin.dashboard', 'grid-1x2-fill', 'Dashboard']]],
            ['Manajemen', [
                ['admin.users.index', 'admin.users.*', 'people-fill', 'Pengguna'],
                ['admin.aktivitas', 'admin.aktivitas', 'clock-history', 'Riwayat Aktivitas'],
            ]],
        ],
        'dosen' => [
            ['Dashboard', [['dosen.dashboard', 'dosen.dashboard', 'grid-1x2-fill', 'Dashboard']]],
            ['Pemantauan', [
                ['dosen.mahasiswa.index', 'dosen.mahasiswa.*', 'people-fill', 'Mahasiswa Bimbingan'],
                ['dosen.logbook.index', 'dosen.logbook.*', 'journal-check', 'Logbook'],
                ['dosen.laporan.index', 'dosen.laporan.*', 'file-earmark-text-fill', 'Laporan KP'],
            ]],
            ['Lainnya', [['dosen.aktivitas', 'dosen.aktivitas', 'clock-history', 'Riwayat Aktivitas']]],
        ],
        'mahasiswa' => [
            ['Dashboard', [['mahasiswa.dashboard', 'mahasiswa.dashboard', 'grid-1x2-fill', 'Dashboard']]],
            ['Magang Saya', [
                ['mahasiswa.magang', 'mahasiswa.magang', 'building-fill', 'Info Magang'],
                ['mahasiswa.logbook.index', 'mahasiswa.logbook.*', 'journal-text', 'Logbook'],
                ['mahasiswa.laporan', 'mahasiswa.laporan*', 'file-earmark-arrow-up-fill', 'Laporan KP'],
            ]],
            ['Lainnya', [['mahasiswa.aktivitas', 'mahasiswa.aktivitas', 'clock-history', 'Riwayat Aktivitas']]],
        ],
    ][$p];
@endphp

<aside class="offcanvas-lg offcanvas-start sidebar" tabindex="-1" id="sidebar" aria-label="Menu utama">
    <div class="sidebar-inner">
        <div class="sb-head">
            <a href="{{ route('dashboard') }}" class="d-flex" aria-label="Beranda">@include('partials.logo', ['size' => 38])</a>
            <span class="sb-brand">Pantau<b>KP</b></span>
            <button type="button" class="sb-toggle" id="sbToggle" title="Ciutkan / perluas menu" aria-label="Ciutkan atau perluas menu"><i class="bi bi-record-circle"></i></button>
            <button type="button" class="btn-close sb-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Tutup"></button>
        </div>

        @foreach($menus as [$title, $items])
            <div class="menu-heading"><span class="text">{{ $title }}</span><span class="line"></span></div>
            @foreach($items as [$route, $pattern, $icon, $label])
                <a class="menu-link {{ request()->routeIs($pattern) ? 'active' : '' }}" href="{{ route($route) }}" title="{{ $label }}">
                    <i class="bi bi-{{ $icon }}"></i><span class="label">{{ $label }}</span>
                </a>
            @endforeach
        @endforeach
        <div class="menu-heading"><span class="text">Akun</span><span class="line"></span></div>
        <a class="menu-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}" title="Profil &amp; Password">
            <i class="bi bi-person-gear"></i><span class="label">Profil &amp; Password</span>
        </a>
    </div>
</aside>

<div class="main">
    <div class="content w-100">
        <header class="topbar">
            <button class="btn-icon-circle d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Buka menu"><i class="bi bi-list fs-5"></i></button>
            <div class="d-none d-md-block text-muted small ms-1">{{ now()->translatedFormat('l, d F Y') }}</div>

            <div class="ms-auto d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button class="btn-icon-circle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Pilih tema" title="Tema"><i class="bi bi-sun" id="themeIcon"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end p-2" style="min-width:10rem">
                        <li><button class="dropdown-item theme-item rounded d-flex align-items-center gap-2 mb-1" data-theme="light"><i class="bi bi-sun"></i> Light</button></li>
                        <li><button class="dropdown-item theme-item rounded d-flex align-items-center gap-2 mb-1" data-theme="dark"><i class="bi bi-moon-stars"></i> Dark</button></li>
                        <li><button class="dropdown-item theme-item rounded d-flex align-items-center gap-2" data-theme="system"><i class="bi bi-display"></i> System</button></li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="avatar-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu akun"><span class="avatar">{{ $u->initials }}</span></button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width:14rem">
                        <li class="px-3 py-2"><div class="fw-semibold text-body-emphasis">{{ $u->name }}</div><div class="small text-muted">{{ $u->email }}</div><span class="badge text-bg-primary mt-1 text-capitalize">{{ $u->role->value }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profil</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}#password"><i class="bi bi-key me-2"></i>Ganti password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button></form></li>
                    </ul>
                </div>
            </div>
        </header>

        <main>
            <div class="page-head">
                <h1>@yield('heading')</h1>
                <div class="d-flex flex-wrap gap-2">@yield('actions')</div>
            </div>

            @if(session('success'))<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if(session('error'))<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if($errors->any() && ! $errors->hasBag('password') && ! $errors->hasBag('reset'))<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Periksa kembali isian Anda. Ada {{ $errors->count() }} kesalahan.</div>@endif

            @yield('content')
        </main>
    </div>
    <footer class="content-footer">Copyright &copy; {{ date('Y') }} | <strong>Sistem Pemantauan Magang &amp; KP</strong></footer>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Konfirmasi</h5><button class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
        <div class="modal-body" id="confirmText">Lanjutkan?</div>
        <div class="modal-footer"><button class="btn btn-soft" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" id="confirmOk">Ya, lanjutkan</button></div>
    </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    /* ----- Tema ----- */
    var icons = { light: 'bi-sun', dark: 'bi-moon-stars' };
    function syncTheme() {
        var m = kpTheme.mode(), r = kpTheme.resolved();
        var ic = document.getElementById('themeIcon');
        ic.className = 'bi ' + (m === 'system' ? 'bi-display' : icons[r]);
        document.querySelectorAll('.theme-item').forEach(function (b) { b.classList.toggle('active', b.dataset.theme === m); });
    }
    document.querySelectorAll('.theme-item').forEach(function (b) { b.addEventListener('click', function () { kpTheme.set(b.dataset.theme); }); });
    window.addEventListener('kp-theme-change', syncTheme); syncTheme();

    /* ----- Sidebar collapse/expand (desktop). Status tersimpan di localStorage ----- */
    var root = document.documentElement, tg = document.getElementById('sbToggle');
    function syncSb() { var c = root.classList.contains('sb-collapsed'); tg.firstElementChild.className = 'bi ' + (c ? 'bi-circle' : 'bi-record-circle'); }
    tg.addEventListener('click', function () {
        root.classList.toggle('sb-collapsed');
        try { localStorage.setItem('kp-sb', root.classList.contains('sb-collapsed') ? '1' : '0'); } catch (e) {}
        syncSb();
    });
    syncSb();

    /* ----- Warna Chart.js mengikuti tema ----- */
    window.kpChartColors = function () {
        var s = getComputedStyle(document.documentElement);
        return { text: s.getPropertyValue('--kp-muted').trim(), grid: s.getPropertyValue('--kp-border').trim(), primary: s.getPropertyValue('--kp-primary').trim(),
                 success: s.getPropertyValue('--kp-success').trim(), warning: s.getPropertyValue('--kp-warning').trim(), surface: s.getPropertyValue('--kp-surface').trim() };
    };

    /* ----- Modal konfirmasi + state loading pada form ----- */
    var modal = new bootstrap.Modal(document.getElementById('confirmModal')), pending = null;
    function lock(form, submitter) {
        form.dataset.locked = '1';
        form.querySelectorAll('button[type=submit]').forEach(function (b) { b.disabled = true; });
        if (submitter) submitter.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    }
    document.addEventListener('submit', function (e) {
        var form = e.target; if (form.dataset.locked) return;
        var submitter = e.submitter, msg = (submitter && submitter.dataset.confirm) || form.dataset.confirm;
        if (msg && !form.dataset.confirmed) {
            e.preventDefault();
            document.getElementById('confirmText').textContent = msg;
            pending = { form: form, submitter: submitter }; modal.show(); return;
        }
        setTimeout(function () { lock(form, submitter); }, 0);
    });
    document.getElementById('confirmOk').addEventListener('click', function () {
        if (!pending) return; var f = pending.form, s = pending.submitter;
        f.dataset.confirmed = '1'; modal.hide(); f.requestSubmit(s || undefined);
    });
})();
</script>
@stack('scripts')
</body>
</html>
