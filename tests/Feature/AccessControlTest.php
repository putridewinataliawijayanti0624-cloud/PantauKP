<?php

namespace Tests\Feature;

use App\Enums\LogbookStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsKpData;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use BuildsKpData, RefreshDatabase;

    public function test_pembatasan_route_berdasarkan_role(): void
    {
        [$dosen, $mhs] = $this->makeSetup();
        $this->actingAs($mhs)->get(route('dosen.dashboard'))->assertForbidden();
        $this->actingAs($mhs)->get(route('dosen.mahasiswa.index'))->assertForbidden();
        $this->actingAs($dosen)->get(route('mahasiswa.dashboard'))->assertForbidden();
        $this->actingAs($dosen)->get(route('mahasiswa.logbook.create'))->assertForbidden();
    }

    public function test_dosen_hanya_melihat_mahasiswa_bimbingannya(): void
    {
        [$dosen, , $i] = $this->makeSetup();
        [, , $iLain] = $this->makeSetup();
        $logLain = $this->makeLogbook($iLain, LogbookStatus::Menunggu, 1);

        $this->actingAs($dosen)->get(route('dosen.mahasiswa.show', $i))->assertOk();
        $this->actingAs($dosen)->get(route('dosen.mahasiswa.show', $iLain))->assertForbidden();
        $this->actingAs($dosen)->get(route('dosen.logbook.show', $logLain))->assertForbidden();
        $this->actingAs($dosen)->get(route('dosen.laporan.show', $iLain->report))->assertForbidden();
    }

    public function test_daftar_mahasiswa_dosen_tidak_memuat_mahasiswa_dosen_lain(): void
    {
        [$dosen, $m] = $this->makeSetup();
        [, $mLain] = $this->makeSetup();

        $this->actingAs($dosen)->get(route('dosen.mahasiswa.index'))
            ->assertOk()->assertSee($m->name)->assertDontSee($mLain->name);
    }

    public function test_mahasiswa_tidak_dapat_membuka_data_mahasiswa_lain(): void
    {
        [, $mhs, $i] = $this->makeSetup();
        [, $mhsLain, $iLain] = $this->makeSetup();
        $logLain = $this->makeLogbook($iLain);
        $log = $this->makeLogbook($i);

        $this->actingAs($mhs)->get(route('mahasiswa.logbook.show', $logLain))->assertForbidden();
        $this->actingAs($mhs)->get(route('mahasiswa.logbook.edit', $logLain))->assertForbidden();
        $this->actingAs($mhs)->put(route('mahasiswa.logbook.update', $logLain), $this->logbookPayload())->assertForbidden();
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.submit', $logLain))->assertForbidden();
        $this->actingAs($mhs)->delete(route('mahasiswa.logbook.destroy', $logLain))->assertForbidden();
        $this->actingAs($mhs)->get(route('mahasiswa.logbook.show', $log))->assertOk();
        $this->assertDatabaseHas('logbooks', ['id' => $logLain->id]);
    }
}
