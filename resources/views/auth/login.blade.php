<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk | PantauKP</title>
    @include('layouts._theme')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;
            background: radial-gradient(900px 500px at 15% 0%, color-mix(in srgb, var(--kp-primary) 14%, transparent), transparent 60%), radial-gradient(800px 480px at 100% 100%, color-mix(in srgb, var(--kp-primary) 10%, transparent), transparent 60%), var(--kp-bg); }
        .login-card { width: 100%; max-width: 420px; }
        .theme-fab { position: fixed; top: 1rem; right: 1rem; }
    </style>
</head>
<body>
<button class="btn-icon-circle theme-fab" id="themeBtn" aria-label="Ganti tema" title="Ganti tema"><i class="bi bi-sun" id="themeIcon"></i></button>

<div class="card login-card">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="d-inline-block mb-2">@include('partials.logo', ['size' => 56])</div>
            <h1 class="h4 fw-bold mb-1">Pantau<span style="color:var(--kp-primary)">KP</span></h1>
            <p class="text-muted mb-0 small">Sistem Pemantauan Magang &amp; Kerja Praktik</p>
        </div>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="username">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <button class="btn btn-primary w-100 py-2" type="submit">Masuk</button>
        </form>
    </div>
</div>
<script>
(function () {
    var order = ['light', 'dark', 'system'], icon = { light: 'bi-sun', dark: 'bi-moon-stars', system: 'bi-display' };
    function sync() { document.getElementById('themeIcon').className = 'bi ' + icon[kpTheme.mode()]; }
    document.getElementById('themeBtn').addEventListener('click', function () { kpTheme.set(order[(order.indexOf(kpTheme.mode()) + 1) % 3]); sync(); });
    sync();
    document.querySelector('form').addEventListener('submit', function () {
        var b = this.querySelector('button[type=submit]'); setTimeout(function () { b.disabled = true; b.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...'; }, 0);
    });
})();
</script>
</body>
</html>
