<?php

namespace Tests\Feature;

use App\Enums\LogbookStatus;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsKpData;
use Tests\TestCase;

class ActivityAndStudentTest extends TestCase
{
    use BuildsKpData, RefreshDatabase;

    public function test_riwayat_aktivitas_tidak_dapat_diubah_atau_dihapus(): void
    {
        [$dosen, $mhs, $i] = $this->makeSetup();
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['aksi' => 'kirim']));

        $log = ActivityLog::firstOrFail();
        $this->assertFalse($log->update(['aksi' => 'diubah']));
        $this->assertFalse($log->delete());
        $this->assertDatabaseHas('activity_logs', ['id' => $log->id, 'aksi' => 'logbook.dikirim']);
    }

    public function test_dosen_hanya_melihat_aktivitas_mahasiswanya(): void
    {
        [$dosen, $mhs, $i] = $this->makeSetup();
        [, $mhsLain, $iLain] = $this->makeSetup();
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['aksi' => 'kirim', 'judul' => 'Kegiatan Milik Saya']));
        $this->actingAs($mhsLain)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['aksi' => 'kirim', 'judul' => 'Kegiatan Orang Lain']));

        $this->actingAs($dosen)->get(route('dosen.aktivitas'))->assertOk()->assertSee('Kegiatan Milik Saya')->assertDontSee('Kegiatan Orang Lain');
    }

    public function test_dosen_menambah_mahasiswa_dan_otomatis_menjadi_pembimbing(): void
    {
        [$dosen] = $this->makeSetup();
        $payload = [
            'name' => 'Mahasiswa Baru', 'email' => 'baru@kp.test', 'password' => 'rahasia123', 'nim' => '2299999',
            'program_studi' => 'Teknik Informatika', 'nama_perusahaan' => 'PT Baru',
            'tanggal_mulai' => now()->toDateString(), 'tanggal_selesai' => now()->addDays(30)->toDateString(),
            'role' => 'dosen', // upaya menyisipkan role harus diabaikan
        ];

        $this->actingAs($dosen)->post(route('dosen.mahasiswa.store'), $payload)->assertRedirect();

        $u = User::where('email', 'baru@kp.test')->firstOrFail();
        $this->assertTrue($u->isMahasiswa());
        $this->assertDatabaseHas('supervisions', ['lecturer_id' => $dosen->id]);
        $this->assertDatabaseHas('student_profiles', ['user_id' => $u->id, 'nim' => '2299999']);
        $this->assertNotSame('rahasia123', $u->password);
    }

    public function test_mahasiswa_tidak_bisa_menambah_mahasiswa(): void
    {
        [, $mhs] = $this->makeSetup();
        $this->actingAs($mhs)->post(route('dosen.mahasiswa.store'), [])->assertForbidden();
    }

    public function test_pencarian_dan_filter_daftar_mahasiswa(): void
    {
        [$dosen, $m] = $this->makeSetup();
        $this->actingAs($dosen)->get(route('dosen.mahasiswa.index', ['q' => $m->name]))->assertOk()->assertSee($m->name);
        $this->actingAs($dosen)->get(route('dosen.mahasiswa.index', ['q' => 'tidak-ada-xyz']))->assertOk()->assertDontSee($m->name);
        $this->actingAs($dosen)->get(route('dosen.mahasiswa.index', ['status' => 'selesai', 'sort' => 'progres', 'dir' => 'desc']))->assertOk();
    }
}
