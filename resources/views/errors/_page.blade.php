<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} | {{ $title }}</title>
    @include('layouts._theme')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 text-center p-3">
<div>
    <div class="display-1 fw-bold" style="color:var(--kp-primary)">{{ $code }}</div>
    <h1 class="h4">{{ $title }}</h1>
    <p class="text-muted">{{ $msg }}</p>
    @if(config('app.debug') && isset($exception) && $exception->getMessage() !== '')
        <p class="small text-muted" style="max-width:560px;word-break:break-all">Detail (hanya tampil saat APP_DEBUG=true):<br><code>{{ $exception->getMessage() }}</code></p>
    @endif
    <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke beranda</a>
</div>
</body>
</html>
