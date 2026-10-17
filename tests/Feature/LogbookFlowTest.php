<?php

namespace Tests\Feature;

use App\Enums\LogbookStatus;
use App\Models\Logbook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsKpData;
use Tests\TestCase;

class LogbookFlowTest extends TestCase
{
    use BuildsKpData, RefreshDatabase;

    public function test_membuat_draft(): void
    {
        [, $mhs, $i] = $this->makeSetup();
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload())->assertRedirect();

        $this->assertDatabaseHas('logbooks', ['internship_id' => $i->id, 'judul' => 'Belajar Laravel', 'status' => 'draft', 'submission_number' => 0]);
    }

    public function test_membuat_dan_mengirim_logbook_mencatat_aktivitas(): void
    {
        [, $mhs, $i] = $this->makeSetup();
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['aksi' => 'kirim']))->assertRedirect();

        $this->assertDatabaseHas('logbooks', ['internship_id' => $i->id, 'status' => 'menunggu', 'submission_number' => 1]);
        $this->assertDatabaseHas('activity_logs', ['aksi' => 'logbook.dikirim', 'user_id' => $mhs->id, 'internship_id' => $i->id]);
    }

    public function test_validasi_input_logbook(): void
    {
        [, $mhs] = $this->makeSetup();
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['aksi' => 'kirim', 'hasil' => '']))->assertSessionHasErrors('hasil');
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['judul' => '']))->assertSessionHasErrors('judul');
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['tanggal' => now()->addDays(3)->toDateString()]))->assertSessionHasErrors('tanggal');
        $this->assertSame(0, Logbook::count());
    }

    public function test_logbook_tidak_bisa_dibuat_sebelum_magang_dimulai(): void
    {
        [, $mhs, $i] = $this->makeSetup();
        $i->update(['tanggal_mulai' => now()->addDays(2)->toDateString(), 'tanggal_selesai' => now()->addDays(40)->toDateString()]);

        $this->actingAs($mhs)->get(route('mahasiswa.logbook.create'))->assertRedirect(route('mahasiswa.logbook.index'))->assertSessionMissing('error');
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload())->assertRedirect(route('mahasiswa.logbook.index'));
        $this->assertSame(0, Logbook::count());
        $this->actingAs($mhs)->get(route('mahasiswa.logbook.index'))->assertOk()->assertSee('dimulai pada');
    }

    public function test_logbook_tetap_bisa_dibuat_setelah_periode_magang_berakhir(): void
    {
        [, $mhs, $i] = $this->makeSetup();
        $i->update(['tanggal_mulai' => now()->subDays(30)->toDateString(), 'tanggal_selesai' => now()->subDays(5)->toDateString()]);

        // halaman tambah logbook tetap terbuka walau magang sudah lewat batas akhir
        $this->actingAs($mhs)->get(route('mahasiswa.logbook.create'))->assertOk();
        // tanggal di dalam periode maupun sesudahnya (sampai hari ini) diterima
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['tanggal' => now()->subDays(10)->toDateString()]))->assertSessionDoesntHaveErrors();
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['tanggal' => now()->toDateString()]))->assertSessionDoesntHaveErrors();
        $this->assertSame(2, Logbook::count());
        // sebelum tanggal mulai dan melewati hari ini tetap ditolak
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['tanggal' => now()->subDays(40)->toDateString()]))->assertSessionHasErrors('tanggal');
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['tanggal' => now()->addDay()->toDateString()]))->assertSessionHasErrors('tanggal');
    }

    public function test_logbook_terkirim_tidak_bisa_diubah(): void
    {
        [, $mhs, $i] = $this->makeSetup();
        $l = $this->makeLogbook($i, LogbookStatus::Menunggu, 1);

        $this->actingAs($mhs)->get(route('mahasiswa.logbook.edit', $l))->assertForbidden();
        $this->actingAs($mhs)->put(route('mahasiswa.logbook.update', $l), $this->logbookPayload(['judul' => 'Diubah']))->assertForbidden();
        $this->assertSame('Judul asli', $l->fresh()->judul);

        $l2 = $this->makeLogbook($i, LogbookStatus::Disetujui, 1);
        $this->actingAs($mhs)->put(route('mahasiswa.logbook.update', $l2), $this->logbookPayload())->assertForbidden();
    }

    public function test_dosen_menyetujui_logbook_dan_riwayat_tersimpan(): void
    {
        [$dosen, , $i] = $this->makeSetup();
        $l = $this->makeLogbook($i, LogbookStatus::Menunggu, 1);

        $this->actingAs($dosen)->post(route('dosen.logbook.review', $l), ['keputusan' => 'disetujui', 'komentar' => 'Bagus'])->assertRedirect();

        $this->assertSame(LogbookStatus::Disetujui, $l->fresh()->status);
        $this->assertDatabaseHas('logbook_reviews', ['logbook_id' => $l->id, 'reviewer_id' => $dosen->id, 'keputusan' => 'disetujui', 'komentar' => 'Bagus', 'submission_number' => 1]);
        $this->assertDatabaseHas('activity_logs', ['aksi' => 'logbook.disetujui', 'user_id' => $dosen->id]);
    }

    public function test_revisi_wajib_disertai_alasan(): void
    {
        [$dosen, , $i] = $this->makeSetup();
        $l = $this->makeLogbook($i, LogbookStatus::Menunggu, 1);

        $this->actingAs($dosen)->post(route('dosen.logbook.review', $l), ['keputusan' => 'perlu_revisi', 'komentar' => ''])->assertSessionHasErrors('komentar');
        $this->assertSame(LogbookStatus::Menunggu, $l->fresh()->status);
        $this->assertDatabaseCount('logbook_reviews', 0);
    }

    public function test_alur_revisi_menjaga_riwayat_pemeriksaan(): void
    {
        [$dosen, $mhs, $i] = $this->makeSetup();
        $l = $this->makeLogbook($i, LogbookStatus::Menunggu, 1);

        $this->actingAs($dosen)->post(route('dosen.logbook.review', $l), ['keputusan' => 'perlu_revisi', 'komentar' => 'Tambahkan bukti'])->assertRedirect();
        $this->assertSame(LogbookStatus::PerluRevisi, $l->fresh()->status);

        // mahasiswa merevisi dan mengirim ulang
        $this->actingAs($mhs)->put(route('mahasiswa.logbook.update', $l), $this->logbookPayload(['aksi' => 'kirim', 'judul' => 'Judul revisi']))->assertRedirect();

        $l->refresh();
        $this->assertSame(LogbookStatus::Menunggu, $l->status);
        $this->assertSame(2, $l->submission_number);
        $this->assertSame('Judul revisi', $l->judul);

        // review lama tetap ada dan snapshot-nya masih berisi isi lama
        $old = $l->reviews()->first();
        $this->assertSame('Tambahkan bukti', $old->komentar);
        $this->assertSame('Judul asli', $old->snapshot['judul']);
        $this->assertSame(1, $old->submission_number);

        $this->actingAs($dosen)->post(route('dosen.logbook.review', $l), ['keputusan' => 'disetujui'])->assertRedirect();
        $this->assertSame(2, $l->reviews()->count());
        $this->assertSame(LogbookStatus::Disetujui, $l->fresh()->status);
    }

    public function test_perubahan_status_tidak_sah_ditolak(): void
    {
        [$dosen, $mhs, $i] = $this->makeSetup();
        [$dosenLain] = $this->makeSetup();
        $draft = $this->makeLogbook($i);
        $approved = $this->makeLogbook($i, LogbookStatus::Disetujui, 1);
        $waiting = $this->makeLogbook($i, LogbookStatus::Menunggu, 1);

        $ok = ['keputusan' => 'disetujui'];
        $this->actingAs($dosen)->post(route('dosen.logbook.review', $draft), $ok)->assertForbidden();
        $this->actingAs($dosen)->post(route('dosen.logbook.review', $approved), $ok)->assertForbidden();
        $this->actingAs($dosenLain)->post(route('dosen.logbook.review', $waiting), $ok)->assertForbidden();
        $this->actingAs($mhs)->post(route('dosen.logbook.review', $waiting), $ok)->assertForbidden();   // mahasiswa tidak bisa menyetujui sendiri
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.submit', $waiting))->assertForbidden();     // kirim ulang saat menunggu

        $this->assertSame(LogbookStatus::Draft, $draft->fresh()->status);
        $this->assertSame(LogbookStatus::Menunggu, $waiting->fresh()->status);
        $this->assertDatabaseCount('logbook_reviews', 0);
    }

    public function test_dashboard_dosen_memakai_data_database(): void
    {
        [$dosen, , $i] = $this->makeSetup();
        $this->makeLogbook($i, LogbookStatus::Menunggu, 1);
        $this->makeLogbook($i, LogbookStatus::Menunggu, 1);
        $this->makeLogbook($i, LogbookStatus::PerluRevisi, 1);
        $this->makeLogbook($i, LogbookStatus::Disetujui, 1);

        $this->actingAs($dosen)->get(route('dosen.dashboard'))->assertOk()->assertViewHas('stats', fn ($s) => $s['total'] === 1
            && $s['logbook_menunggu'] === 2 && $s['logbook_revisi'] === 1 && $s['laporan_menunggu'] === 0);
    }

    public function test_progres_dihitung_dari_logbook_disetujui(): void
    {
        [, , $i] = $this->makeSetup(); // target 10
        foreach (range(1, 3) as $_) {
            $this->makeLogbook($i, LogbookStatus::Disetujui, 1);
        }
        $this->makeLogbook($i, LogbookStatus::Menunggu, 1);

        $this->assertSame(30, $i->fresh()->progress);
    }
}
