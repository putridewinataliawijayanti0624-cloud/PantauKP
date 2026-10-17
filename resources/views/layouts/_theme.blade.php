{{-- Dipakai di <head> semua halaman: skrip tema (anti-kedip), font, dan palet warna light/dark. --}}
<script>
(function () {
    var KEY = 'kp-theme', root = document.documentElement, mq = window.matchMedia('(prefers-color-scheme: dark)');
    function mode() { try { return localStorage.getItem(KEY) || 'system'; } catch (e) { return 'system'; } }
    function resolved(m) { return m === 'system' ? (mq.matches ? 'dark' : 'light') : m; }
    function apply() { var r = resolved(mode()); root.setAttribute('data-bs-theme', r); return r; }
    window.kpTheme = {
        mode: mode,
        resolved: function () { return resolved(mode()); },
        set: function (m) { try { localStorage.setItem(KEY, m); } catch (e) {} apply(); window.dispatchEvent(new CustomEvent('kp-theme-change', { detail: { mode: m, resolved: resolved(m) } })); }
    };
    apply();
    mq.addEventListener && mq.addEventListener('change', function () { if (mode() === 'system') { apply(); window.dispatchEvent(new CustomEvent('kp-theme-change', { detail: { mode: 'system', resolved: resolved('system') } })); } });
    try { if (localStorage.getItem('kp-sb') === '1') root.classList.add('sb-collapsed'); } catch (e) {}
})();
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ===== Palet LIGHT ===== */
:root, [data-bs-theme="light"] {
    --kp-bg: #f6f7fb; --kp-surface: #ffffff; --kp-surface-2: #f1f3f9; --kp-sidebar: #ffffff;
    --kp-border: #e5e8f0; --kp-text: #4b5367; --kp-heading: #2c3345; --kp-muted: #8a92a6;
    --kp-primary: #2f7bff; --kp-primary-600: #1f6af2;
    --kp-grad: linear-gradient(72deg, #2f7bff 0%, #62a0ff 100%); --kp-glow: rgba(47, 123, 255, .38);
    --kp-success: #22a06b; --kp-warning: #e8960f; --kp-danger: #e5484d; --kp-info: #1596b5; --kp-secondary: #7b8498;
    --kp-shadow: 0 2px 8px rgba(33, 46, 90, .08); --kp-card-border: transparent;
    --kp-body-glow: none;
    --bs-body-bg: var(--kp-bg); --bs-body-color: var(--kp-text); --bs-emphasis-color: var(--kp-heading);
    --bs-border-color: var(--kp-border); --bs-secondary-color: var(--kp-muted); --bs-tertiary-bg: var(--kp-surface-2);
    --bs-primary: #2f7bff; --bs-primary-rgb: 47, 123, 255;
    --bs-success-rgb: 34, 160, 107; --bs-warning-rgb: 232, 150, 15; --bs-danger-rgb: 229, 72, 77;
    --bs-info-rgb: 21, 150, 181; --bs-secondary-rgb: 123, 132, 152;
    --bs-link-color: #2f7bff; --bs-link-color-rgb: 47, 123, 255; --bs-link-hover-color-rgb: 31, 106, 242;
}
/* ===== Palet DARK: ink navy yang tenang, kontras lembut, aksen biru langit ===== */
[data-bs-theme="dark"] {
    --kp-bg: #0d111a; --kp-surface: #151b28; --kp-surface-2: #1b2333; --kp-sidebar: #111723;
    --kp-border: #252e42; --kp-text: #c4cbdb; --kp-heading: #eef1f8; --kp-muted: #8993a9;
    --kp-primary: #6aa3ff; --kp-primary-600: #3f7be6;
    --kp-grad: linear-gradient(72deg, #3b72dc 0%, #5f9cf8 100%); --kp-glow: rgba(95, 156, 248, .30);
    --kp-success: #4fc9a0; --kp-warning: #e8b04b; --kp-danger: #ef7d86; --kp-info: #5fc4dd; --kp-secondary: #8993a9;
    --kp-shadow: none; --kp-card-border: #222b3f;
    --kp-body-glow: radial-gradient(900px 420px at 88% -8%, rgba(95, 156, 248, .09), transparent 65%), radial-gradient(700px 380px at 0% 100%, rgba(120, 110, 220, .06), transparent 70%);
    --bs-body-bg: var(--kp-bg); --bs-body-color: var(--kp-text); --bs-emphasis-color: var(--kp-heading);
    --bs-border-color: var(--kp-border); --bs-secondary-color: var(--kp-muted); --bs-tertiary-bg: var(--kp-surface-2);
    --bs-secondary-bg: var(--kp-surface-2);
    --bs-primary: #6aa3ff; --bs-primary-rgb: 106, 163, 255;
    --bs-success-rgb: 79, 201, 160; --bs-warning-rgb: 232, 176, 75; --bs-danger-rgb: 239, 125, 134;
    --bs-info-rgb: 95, 196, 221; --bs-secondary-rgb: 137, 147, 169;
    --bs-link-color: #7fb0ff; --bs-link-color-rgb: 127, 176, 255; --bs-link-hover-color-rgb: 170, 203, 255;
}

html { color-scheme: light; } html[data-bs-theme="dark"] { color-scheme: dark; }
body { font-family: 'Public Sans', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; font-size: .925rem; background: var(--kp-bg) var(--kp-body-glow); background-attachment: fixed; color: var(--kp-text); }
h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 { color: var(--kp-heading); font-weight: 600; }
.text-muted { color: var(--kp-muted) !important; }
.c-primary { --c: var(--kp-primary); } .c-success { --c: var(--kp-success); } .c-warning { --c: var(--kp-warning); }
.c-danger { --c: var(--kp-danger); } .c-info { --c: var(--kp-info); } .c-secondary { --c: var(--kp-secondary); }
.stat-icon { width: 46px; height: 46px; flex: 0 0 auto; display: flex; align-items: center; justify-content: center; border-radius: .45rem; font-size: 1.3rem;
    color: var(--c, var(--kp-primary)); background: color-mix(in srgb, var(--c, var(--kp-primary)) 14%, transparent); }

/* Kartu */
.card { --bs-card-bg: var(--kp-surface); --bs-card-cap-bg: transparent; --bs-card-border-color: var(--kp-card-border); --bs-card-border-radius: .4rem; --bs-card-inner-border-radius: .4rem;
    box-shadow: var(--kp-shadow); border: 1px solid var(--kp-card-border); color: var(--kp-text); }
.card-header, .card-footer { background: transparent; border-color: var(--kp-border); }
.card-header { font-weight: 600; color: var(--kp-heading); padding: 1rem 1.25rem; }
.card-body { padding: 1.25rem; }
.stat-value { font-size: 1.35rem; font-weight: 700; line-height: 1.1; color: var(--kp-heading); }
.stat-label { font-size: .8rem; color: var(--kp-muted); margin-top: .15rem; }

/* Tabel */
.table { --bs-table-bg: transparent; --bs-table-color: var(--kp-text); --bs-table-border-color: var(--kp-border); --bs-table-hover-bg: var(--kp-surface-2); --bs-table-hover-color: var(--kp-text); }
.table > :not(caption) > * > * { padding: .8rem .95rem; }
.kp-thead th { font-size: .72rem; letter-spacing: .06em; text-transform: uppercase; color: var(--kp-muted) !important; font-weight: 600; background: color-mix(in srgb, var(--kp-surface-2) 70%, transparent) !important; white-space: nowrap; }
th a { color: inherit; text-decoration: none; }
.pre-wrap { white-space: pre-wrap; }

/* Form */
.form-control, .form-select { background-color: var(--kp-surface); border-color: var(--kp-border); color: var(--kp-text); }
.form-control::placeholder { color: var(--kp-muted); opacity: .8; }
.form-control:focus, .form-select:focus { background-color: var(--kp-surface); color: var(--kp-text); border-color: var(--kp-primary); box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--kp-primary) 22%, transparent); }
.form-control:disabled { background-color: var(--kp-surface-2); }
.form-check-input { background-color: var(--kp-surface); border-color: var(--kp-border); }
.form-check-input:checked { background-color: var(--kp-primary); border-color: var(--kp-primary); }
.form-switch .form-check-input:focus { box-shadow: 0 0 0 .2rem color-mix(in srgb, var(--kp-primary) 22%, transparent); }

/* Tombol */
.btn { font-weight: 500; border-radius: .4rem; }
.btn-primary { --bs-btn-bg: var(--kp-primary-600); --bs-btn-border-color: var(--kp-primary-600); --bs-btn-color: #fff; --bs-btn-hover-bg: var(--kp-primary); --bs-btn-hover-border-color: var(--kp-primary); --bs-btn-hover-color: #fff; --bs-btn-active-bg: var(--kp-primary); --bs-btn-active-border-color: var(--kp-primary); --bs-btn-disabled-bg: var(--kp-primary-600); --bs-btn-disabled-border-color: var(--kp-primary-600); box-shadow: 0 2px 6px -1px var(--kp-glow); }
.btn-outline-primary { --bs-btn-color: var(--kp-primary); --bs-btn-border-color: color-mix(in srgb, var(--kp-primary) 55%, transparent); --bs-btn-hover-bg: var(--kp-primary-600); --bs-btn-hover-border-color: var(--kp-primary-600); --bs-btn-hover-color: #fff; --bs-btn-active-bg: var(--kp-primary-600); --bs-btn-active-border-color: var(--kp-primary-600); --bs-btn-active-color: #fff; }
.btn-success { --bs-btn-bg: var(--kp-success); --bs-btn-border-color: var(--kp-success); --bs-btn-color: #fff; --bs-btn-hover-bg: color-mix(in srgb, var(--kp-success) 88%, #000); --bs-btn-hover-border-color: var(--kp-success); --bs-btn-hover-color: #fff; --bs-btn-active-bg: var(--kp-success); --bs-btn-active-color: #fff; }
[data-bs-theme="dark"] .btn-success { --bs-btn-color: #08261d; --bs-btn-hover-color: #08261d; --bs-btn-active-color: #08261d; }
.btn-outline-danger { --bs-btn-color: var(--kp-danger); --bs-btn-border-color: color-mix(in srgb, var(--kp-danger) 55%, transparent); --bs-btn-hover-bg: var(--kp-danger); --bs-btn-hover-border-color: var(--kp-danger); --bs-btn-hover-color: #fff; --bs-btn-active-bg: var(--kp-danger); --bs-btn-active-color: #fff; }
.btn-outline-secondary { --bs-btn-color: var(--kp-text); --bs-btn-border-color: var(--kp-border); --bs-btn-hover-bg: var(--kp-surface-2); --bs-btn-hover-border-color: var(--kp-border); --bs-btn-hover-color: var(--kp-heading); --bs-btn-active-bg: var(--kp-surface-2); --bs-btn-active-color: var(--kp-heading); }
.btn-soft { --bs-btn-bg: var(--kp-surface-2); --bs-btn-border-color: transparent; --bs-btn-color: var(--kp-text); --bs-btn-hover-bg: color-mix(in srgb, var(--kp-surface-2) 80%, var(--kp-primary)); --bs-btn-hover-border-color: transparent; --bs-btn-hover-color: var(--kp-heading); --bs-btn-active-bg: var(--kp-surface-2); --bs-btn-active-color: var(--kp-heading); --bs-btn-active-border-color: transparent; }
.btn-close { filter: none; } [data-bs-theme="dark"] .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

/* Komponen lain */
.dropdown-menu { --bs-dropdown-bg: var(--kp-surface); --bs-dropdown-border-color: var(--kp-border); --bs-dropdown-color: var(--kp-text); --bs-dropdown-link-color: var(--kp-text); --bs-dropdown-link-hover-bg: var(--kp-surface-2); --bs-dropdown-link-hover-color: var(--kp-heading); --bs-dropdown-divider-bg: var(--kp-border); --bs-dropdown-border-radius: .5rem; box-shadow: 0 8px 24px rgba(10, 16, 40, .18); }
.modal { --bs-modal-bg: var(--kp-surface); --bs-modal-border-color: var(--kp-border); --bs-modal-header-border-color: var(--kp-border); --bs-modal-footer-border-color: var(--kp-border); }
.nav-tabs { --bs-nav-tabs-border-color: var(--kp-border); --bs-nav-tabs-link-active-bg: var(--kp-surface); --bs-nav-tabs-link-active-border-color: var(--kp-border) var(--kp-border) var(--kp-surface); --bs-nav-tabs-link-active-color: var(--kp-primary); --bs-nav-link-color: var(--kp-muted); --bs-nav-link-hover-color: var(--kp-heading); }
.pagination { --bs-pagination-bg: var(--kp-surface); --bs-pagination-border-color: var(--kp-border); --bs-pagination-color: var(--kp-text); --bs-pagination-hover-bg: var(--kp-surface-2); --bs-pagination-hover-color: var(--kp-heading); --bs-pagination-hover-border-color: var(--kp-border); --bs-pagination-active-bg: var(--kp-primary-600); --bs-pagination-active-border-color: var(--kp-primary-600); --bs-pagination-active-color: #fff; --bs-pagination-disabled-bg: var(--kp-surface); --bs-pagination-disabled-border-color: var(--kp-border); --bs-pagination-focus-bg: var(--kp-surface-2); }
.list-group { --bs-list-group-bg: transparent; --bs-list-group-border-color: var(--kp-border); --bs-list-group-action-hover-bg: var(--kp-surface-2); --bs-list-group-color: var(--kp-text); }
.progress { --bs-progress-bg: var(--kp-surface-2); }
.progress-bar { background: var(--kp-grad); }
.alert { border: 0; border-left: 4px solid currentColor; }
.badge { font-weight: 600; }
.img-thumbnail { background: var(--kp-surface-2); border-color: var(--kp-border); }
.btn-icon-circle { width: 38px; height: 38px; border-radius: 50%; padding: 0; display: inline-flex; align-items: center; justify-content: center; background: var(--kp-surface-2); color: var(--kp-text); border: 0; }
.btn-icon-circle:hover, .btn-icon-circle.show { background: color-mix(in srgb, var(--kp-surface-2) 75%, var(--kp-primary)); color: var(--kp-primary); }
.avatar { width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: var(--kp-grad); color: #fff; font-weight: 700; font-size: .85rem; letter-spacing: .02em; flex: 0 0 auto; }
.avatar-lg { width: 110px; height: 164px; border-radius: .4rem; font-size: 2.4rem; }
.chip { display: inline-block; padding: .2rem .7rem; border-radius: .25rem; background: var(--kp-surface-2); color: var(--kp-muted); font-weight: 700; font-size: .78rem; letter-spacing: .02em; }
.theme-item.active { background: color-mix(in srgb, var(--kp-primary) 16%, transparent); color: var(--kp-primary); }
/* Pagination (komponen halaman) */
.kp-pager { display: flex; flex-wrap: wrap; align-items: center; justify-content: flex-end; gap: .8rem 1.5rem; }
.kp-pager-group { display: flex; align-items: center; gap: .6rem; }
.kp-pg { min-width: 38px; height: 38px; padding: 0 .55rem; display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--kp-border); border-radius: .4rem; background: var(--kp-surface); color: var(--kp-text); text-decoration: none; font-weight: 500; transition: border-color .15s, color .15s, background .15s; }
a.kp-pg:hover { border-color: var(--kp-primary); color: var(--kp-primary); }
.kp-pg.is-current { background: var(--kp-primary-600); border-color: var(--kp-primary-600); color: #fff; box-shadow: 0 2px 6px -1px var(--kp-glow); }
.kp-pg.is-last { border-color: color-mix(in srgb, var(--kp-primary) 60%, transparent); color: var(--kp-primary); }
.kp-pg.is-disabled { opacity: .45; pointer-events: none; }
.kp-pager-text { color: var(--kp-muted); font-size: .88rem; white-space: nowrap; }
.kp-pager-jump { display: flex; align-items: center; gap: .5rem; margin: 0; color: var(--kp-muted); font-size: .88rem; white-space: nowrap; }
.kp-pager-jump .form-select { width: auto; min-width: 4.2rem; }
</style>
