@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('actions')<a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Tambah Pengguna</a>@endsection
@section('content')
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3"><x-stat-card icon="people" label="Total pengguna" :value="$stats['total']" :href="route('admin.users.index')" /></div>
    <div class="col-6 col-lg-3"><x-stat-card icon="mortarboard" label="Dosen" :value="$stats['dosen']" color="info" :href="route('admin.users.index', ['role' => 'dosen'])" /></div>
    <div class="col-6 col-lg-3"><x-stat-card icon="person-badge" label="Mahasiswa" :value="$stats['mahasiswa']" color="success" :href="route('admin.users.index', ['role' => 'mahasiswa'])" /></div>
    <div class="col-6 col-lg-3"><x-stat-card icon="person-slash" label="Akun nonaktif" :value="$stats['nonaktif']" color="danger" :href="route('admin.users.index', ['status' => 'nonaktif'])" /></div>
</div>
<div class="row g-3 mb-3">
    <div class="col-lg-5"><div class="card h-100"><div class="card-header"><i class="bi bi-pie-chart me-2"></i>Komposisi pengguna</div>
        <div class="card-body d-flex justify-content-center"><div style="height:240px;width:100%;max-width:280px"><canvas id="roleChart"></canvas></div></div></div></div>
    <div class="col-lg-7"><div class="card h-100"><div class="card-header d-flex justify-content-between"><span><i class="bi bi-person-plus me-2"></i>Pengguna terbaru</span><a class="small fw-normal" href="{{ route('admin.users.index') }}">Kelola</a></div>
        <div class="list-group list-group-flush">
            @foreach($latestUsers as $lu)
                <a href="{{ route('admin.users.edit', $lu) }}" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
                    <span class="avatar" style="width:34px;height:34px">{{ $lu->initials }}</span>
                    <span class="flex-grow-1">{{ $lu->name }}<br><small class="text-muted">{{ $lu->email }}</small></span>
                    <span class="badge text-bg-secondary text-capitalize">{{ $lu->role->value }}</span>
                </a>
            @endforeach
        </div></div></div>
</div>
<div class="card"><div class="card-header d-flex justify-content-between"><span><i class="bi bi-clock-history me-2"></i>Aktivitas terbaru</span><a href="{{ route('admin.aktivitas') }}" class="small fw-normal">Lihat semua</a></div>
    <div class="card-body">@include('partials.activity-list', ['logs' => $recent, 'showStudent' => true])</div></div>
@endsection
@push('scripts')
<script>
(function () {
    var c = kpChartColors();
    var chart = new Chart(document.getElementById('roleChart'), {
        type: 'doughnut',
        data: { labels: @json($chart['labels']), datasets: [{ data: @json($chart['data']), backgroundColor: [c.warning, c.primary, c.success], borderColor: c.surface, borderWidth: 3 }] },
        options: { maintainAspectRatio: false, cutout: '66%', plugins: { legend: { position: 'bottom', labels: { color: c.text, usePointStyle: true } } } }
    });
    window.addEventListener('kp-theme-change', function () {
        var n = kpChartColors(); var d = chart.data.datasets[0];
        d.backgroundColor = [n.warning, n.primary, n.success]; d.borderColor = n.surface; chart.options.plugins.legend.labels.color = n.text; chart.update();
    });
})();
</script>
@endpush
