@extends('layouts.app')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('actions')<a href="{{ route('dosen.mahasiswa.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Tambah Mahasiswa</a>@endsection
@section('content')
<div class="row g-3 mb-3">
    <div class="col-6 col-md-4 col-xl"><x-stat-card icon="people" label="Mahasiswa bimbingan" :value="$stats['total']" :href="route('dosen.mahasiswa.index')" /></div>
    <div class="col-6 col-md-4 col-xl"><x-stat-card icon="briefcase" label="Sedang magang" :value="$stats['berjalan']" color="info" :href="route('dosen.mahasiswa.index', ['status' => 'berjalan'])" /></div>
    <div class="col-6 col-md-4 col-xl"><x-stat-card icon="hourglass-split" label="Logbook menunggu" :value="$stats['logbook_menunggu']" color="warning" :href="route('dosen.logbook.index', ['status' => 'menunggu'])" /></div>
    <div class="col-6 col-md-6 col-xl"><x-stat-card icon="arrow-repeat" label="Logbook perlu revisi" :value="$stats['logbook_revisi']" color="danger" :href="route('dosen.logbook.index', ['status' => 'perlu_revisi'])" /></div>
    <div class="col-12 col-md-6 col-xl"><x-stat-card icon="file-earmark-text" label="Laporan menunggu" :value="$stats['laporan_menunggu']" color="success" :href="route('dosen.laporan.index', ['status' => 'menunggu'])" /></div>
</div>
<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="card h-100"><div class="card-header"><i class="bi bi-bar-chart-line me-2"></i>Ringkasan progres mahasiswa (%)</div><div class="card-body">
            @if($students->isEmpty())
                <x-empty-state icon="people" text="Belum ada mahasiswa bimbingan."><a href="{{ route('dosen.mahasiswa.create') }}" class="btn btn-sm btn-primary mt-2">Tambah mahasiswa</a></x-empty-state>
            @else
                <div style="height: {{ max(180, $students->count() * 42) }}px"><canvas id="progressChart"></canvas></div>
            @endif
        </div></div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100"><div class="card-header"><i class="bi bi-bell me-2"></i>Perlu tindak lanjut</div>
            <div class="list-group list-group-flush">
                @forelse($followUp as $i)
                    <a href="{{ route('dosen.mahasiswa.show', $i) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span>{{ $i->student->name }}<br><small class="text-muted">{{ $i->nama_perusahaan }}</small></span>
                        <span>
                            @if($i->menunggu_count)<span class="badge text-bg-warning">{{ $i->menunggu_count }} logbook</span>@endif
                            @if($i->laporan_menunggu)<span class="badge text-bg-success">laporan</span>@endif
                        </span>
                    </a>
                @empty
                    <div class="list-group-item"><x-empty-state icon="check2-circle" text="Tidak ada yang perlu diperiksa." /></div>
                @endforelse
            </div>
        </div>
    </div>
</div>
<div class="card"><div class="card-header d-flex justify-content-between"><span><i class="bi bi-clock-history me-2"></i>Aktivitas terbaru</span><a href="{{ route('dosen.aktivitas') }}" class="small fw-normal">Lihat semua</a></div>
    <div class="card-body">@include('partials.activity-list', ['logs' => $recent, 'showStudent' => true])</div></div>
@endsection
@push('scripts')
@if($students->isNotEmpty())
<script>
(function () {
    var c = kpChartColors();
    var chart = new Chart(document.getElementById('progressChart'), {
        type: 'bar',
        data: { labels: @json($chart['labels']), datasets: [{ label: 'Progres (%)', data: @json($chart['data']), backgroundColor: c.primary, borderRadius: 4, barThickness: 18 }] },
        options: { indexAxis: 'y', maintainAspectRatio: false, plugins: { legend: { display: false } },
            scales: { x: { min: 0, max: 100, ticks: { color: c.text }, grid: { color: c.grid } }, y: { ticks: { color: c.text }, grid: { display: false } } } }
    });
    window.addEventListener('kp-theme-change', function () {
        var n = kpChartColors(); chart.data.datasets[0].backgroundColor = n.primary;
        chart.options.scales.x.ticks.color = n.text; chart.options.scales.y.ticks.color = n.text; chart.options.scales.x.grid.color = n.grid; chart.update();
    });
})();
</script>
@endif
@endpush
