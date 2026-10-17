<?php

namespace Tests\Feature;

use App\Enums\ReportStatus;
use App\Support\PrivateStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsKpData;
use Tests\TestCase;

class ReportFlowTest extends TestCase
{
    use BuildsKpData, RefreshDatabase;

    public function test_upload_laporan_pertama(): void
    {
        Storage::fake(PrivateStorage::DISK);
        [, $mhs, $i] = $this->makeSetup();

        $this->actingAs($mhs)->post(route('mahasiswa.laporan.store'), ['file' => $this->pdf()])->assertRedirect(route('mahasiswa.laporan'));

        $r = $i->fresh()->report;
        $this->assertSame(ReportStatus::Menunggu, $r->status);
        $this->assertSame(1, $r->versi_terbaru);
        $this->assertDatabaseHas('report_versions', ['report_id' => $r->id, 'versi' => 1]);
        $this->assertDatabaseHas('activity_logs', ['aksi' => 'laporan.diunggah', 'user_id' => $mhs->id]);
        Storage::disk(PrivateStorage::DISK)->assertExists($r->versions()->first()->path);
    }

    public function test_hanya_pdf_yang_diterima(): void
    {
        Storage::fake(PrivateStorage::DISK);
        [, $mhs] = $this->makeSetup();

        $this->actingAs($mhs)->post(route('mahasiswa.laporan.store'), ['file' => $this->png('laporan.png')])->assertSessionHasErrors('file');
        $this->actingAs($mhs)->post(route('mahasiswa.laporan.store'), [])->assertSessionHasErrors('file');
        $this->assertDatabaseCount('report_versions', 0);
    }

    public function test_alur_revisi_laporan_menyimpan_semua_versi_dan_feedback(): void
    {
        Storage::fake(PrivateStorage::DISK);
        [$dosen, $mhs, $i] = $this->makeSetup();

        $this->actingAs($mhs)->post(route('mahasiswa.laporan.store'), ['file' => $this->pdf()]);
        $report = $i->fresh()->report;

        // tidak boleh unggah lagi saat menunggu
        $this->actingAs($mhs)->post(route('mahasiswa.laporan.store'), ['file' => $this->pdf()])->assertForbidden();

        // revisi tanpa alasan ditolak
        $this->actingAs($dosen)->post(route('dosen.laporan.review', $report), ['keputusan' => 'perlu_revisi'])->assertSessionHasErrors('komentar');

        $this->actingAs($dosen)->post(route('dosen.laporan.review', $report), ['keputusan' => 'perlu_revisi', 'komentar' => 'Perbaiki bab 3'])->assertRedirect();
        $this->assertSame(ReportStatus::PerluRevisi, $report->fresh()->status);

        $this->actingAs($mhs)->post(route('mahasiswa.laporan.store'), ['file' => $this->pdf('revisi.pdf')])->assertRedirect();
        $report->refresh();
        $this->assertSame(2, $report->versi_terbaru);
        $this->assertSame(ReportStatus::Menunggu, $report->status);
        $this->assertSame(2, $report->versions()->count());
        $this->assertDatabaseHas('report_reviews', ['komentar' => 'Perbaiki bab 3', 'keputusan' => 'perlu_revisi']);

        $this->actingAs($dosen)->post(route('dosen.laporan.review', $report), ['keputusan' => 'disetujui'])->assertRedirect();
        $this->assertSame(ReportStatus::Disetujui, $report->fresh()->status);

        // setelah disetujui tidak bisa unggah atau diperiksa lagi
        $this->actingAs($mhs)->post(route('mahasiswa.laporan.store'), ['file' => $this->pdf()])->assertForbidden();
        $this->actingAs($dosen)->post(route('dosen.laporan.review', $report), ['keputusan' => 'perlu_revisi', 'komentar' => 'x'])->assertForbidden();
    }

    public function test_akses_file_laporan_dan_review_oleh_dosen_lain_ditolak(): void
    {
        Storage::fake(PrivateStorage::DISK);
        [$dosen, $mhs, $i] = $this->makeSetup();
        [$dosenLain, $mhsLain] = $this->makeSetup();

        $this->actingAs($mhs)->post(route('mahasiswa.laporan.store'), ['file' => $this->pdf()]);
        $report = $i->fresh()->report;
        $version = $report->versions()->first();

        $this->actingAs($dosen)->get(route('files.laporan', $version))->assertOk();
        $this->actingAs($mhsLain)->get(route('files.laporan', $version))->assertForbidden();
        $this->actingAs($dosenLain)->get(route('files.laporan', $version))->assertForbidden();
        $this->actingAs($dosenLain)->post(route('dosen.laporan.review', $report), ['keputusan' => 'disetujui'])->assertForbidden();
        $this->assertSame(ReportStatus::Menunggu, $report->fresh()->status);
    }
}
