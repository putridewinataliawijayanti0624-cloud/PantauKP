<?php

namespace Tests\Feature;

use App\Models\Logbook;
use App\Models\User;
use App\Support\PrivateStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsKpData;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use BuildsKpData, RefreshDatabase;

    private function mahasiswaPayload(User $dosen, array $o = []): array
    {
        return array_merge([
            'role' => 'mahasiswa', 'name' => 'Mahasiswa Baru', 'email' => 'baru@kp.test', 'password' => 'rahasia123',
            'nim' => '2288888', 'program_studi' => 'Teknik Informatika', 'dosen_id' => $dosen->id,
            'nama_perusahaan' => 'PT Baru', 'tanggal_mulai' => now()->toDateString(), 'tanggal_selesai' => now()->addDays(30)->toDateString(),
        ], $o);
    }

    public function test_admin_login_dan_akses_dibatasi_role(): void
    {
        $admin = $this->makeAdmin();
        [$dosen, $mhs] = $this->makeSetup();

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect(route('admin.dashboard'));
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk()->assertSee($mhs->name);

        foreach ([$dosen, $mhs] as $u) {
            $this->actingAs($u)->get(route('admin.dashboard'))->assertForbidden();
            $this->actingAs($u)->get(route('admin.users.index'))->assertForbidden();
            $this->actingAs($u)->post(route('admin.users.store'), [])->assertForbidden();
            $this->actingAs($u)->delete(route('admin.users.destroy', $admin))->assertForbidden();
        }
        // admin tidak punya akses ke area dosen/mahasiswa
        $this->actingAs($admin)->get(route('dosen.dashboard'))->assertForbidden();
        $this->actingAs($admin)->get(route('mahasiswa.dashboard'))->assertForbidden();
    }

    public function test_admin_membuat_dosen_dan_admin(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'role' => 'dosen', 'name' => 'Dosen Baru', 'email' => 'dosenbaru@kp.test', 'password' => 'rahasia123', 'nidn' => '0711223344', 'program_studi' => 'Sistem Informasi',
        ])->assertRedirect(route('admin.users.index'));
        $d = User::where('email', 'dosenbaru@kp.test')->firstOrFail();
        $this->assertTrue($d->isDosen());
        $this->assertTrue($d->is_active);
        $this->assertDatabaseHas('lecturer_profiles', ['user_id' => $d->id, 'nidn' => '0711223344']);
        $this->assertTrue(Hash::check('rahasia123', $d->password));

        $this->actingAs($admin)->post(route('admin.users.store'), ['role' => 'admin', 'name' => 'Admin Dua', 'email' => 'admin2@kp.test', 'password' => 'rahasia123'])->assertRedirect();
        $this->assertTrue(User::where('email', 'admin2@kp.test')->firstOrFail()->isAdmin());
        $this->assertDatabaseHas('activity_logs', ['aksi' => 'user.dibuat', 'user_id' => $admin->id]);
    }

    public function test_admin_membuat_mahasiswa_lengkap_dengan_dosen_pembimbing(): void
    {
        $admin = $this->makeAdmin();
        [$dosen] = $this->makeSetup();

        $this->actingAs($admin)->post(route('admin.users.store'), $this->mahasiswaPayload($dosen))->assertRedirect(route('admin.users.index'));

        $u = User::where('email', 'baru@kp.test')->firstOrFail();
        $this->assertTrue($u->isMahasiswa());
        $i = $u->currentInternship();
        $this->assertNotNull($i);
        $this->assertTrue($i->isSupervisedBy($dosen));
        $this->assertDatabaseHas('internship_reports', ['internship_id' => $i->id, 'status' => 'belum_diunggah']);
    }

    public function test_validasi_pembuatan_pengguna(): void
    {
        $admin = $this->makeAdmin();
        [$dosen, $mhs] = $this->makeSetup();

        $this->actingAs($admin)->post(route('admin.users.store'), $this->mahasiswaPayload($dosen, ['email' => $mhs->email]))->assertSessionHasErrors('email');
        $this->actingAs($admin)->post(route('admin.users.store'), $this->mahasiswaPayload($dosen, ['password' => 'pendek']))->assertSessionHasErrors('password');
        $this->actingAs($admin)->post(route('admin.users.store'), $this->mahasiswaPayload($dosen, ['dosen_id' => $mhs->id]))->assertSessionHasErrors('dosen_id');
        $this->actingAs($admin)->post(route('admin.users.store'), ['role' => 'superuser', 'name' => 'X', 'email' => 'x@kp.test', 'password' => 'rahasia123'])->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'baru@kp.test']);
    }

    public function test_admin_mengubah_data_dan_memindahkan_dosen_pembimbing(): void
    {
        $admin = $this->makeAdmin();
        [$dosen, $mhs, $i] = $this->makeSetup();
        [$dosenBaru] = $this->makeSetup();

        $this->actingAs($admin)->put(route('admin.users.update', $mhs), [
            'name' => 'Nama Diubah', 'email' => $mhs->email, 'is_active' => '1', 'nim' => '2200001', 'program_studi' => 'Sistem Informasi', 'dosen_id' => $dosenBaru->id,
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame('Nama Diubah', $mhs->fresh()->name);
        $this->assertTrue($i->fresh()->isSupervisedBy($dosenBaru));
        $this->assertFalse($i->fresh()->isSupervisedBy($dosen));

        // dosen lama tidak lagi bisa melihat mahasiswa tersebut
        $this->actingAs($dosen)->get(route('dosen.mahasiswa.show', $i))->assertForbidden();
        $this->actingAs($dosenBaru)->get(route('dosen.mahasiswa.show', $i))->assertOk();
    }

    public function test_admin_mereset_password_pengguna(): void
    {
        $admin = $this->makeAdmin();
        [, $mhs] = $this->makeSetup();

        $this->actingAs($admin)->put(route('admin.users.password', $mhs), ['password' => 'PasswordBaru1', 'password_confirmation' => 'PasswordBaru1'])->assertSessionHasNoErrors();
        $this->actingAs($admin)->put(route('admin.users.password', $mhs), ['password' => 'PasswordBaru1', 'password_confirmation' => 'beda'])->assertSessionHasErrorsIn('reset', 'password');

        auth()->logout();
        $this->post('/login', ['email' => $mhs->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => $mhs->email, 'password' => 'PasswordBaru1'])->assertRedirect(route('mahasiswa.dashboard'));
        $this->assertDatabaseHas('activity_logs', ['aksi' => 'user.password_direset', 'user_id' => $admin->id]);
    }

    public function test_akun_nonaktif_tidak_bisa_login_dan_langsung_dikeluarkan(): void
    {
        $admin = $this->makeAdmin();
        [, $mhs] = $this->makeSetup();

        $this->actingAs($admin)->post(route('admin.users.toggle', $mhs))->assertRedirect();
        $this->assertFalse($mhs->fresh()->is_active);

        auth()->logout();
        $this->post('/login', ['email' => $mhs->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();

        // sesi yang masih terbuka ikut diputus pada request berikutnya
        $this->actingAs($mhs->fresh())->get(route('mahasiswa.dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();

        // aktifkan kembali
        $this->actingAs($admin)->post(route('admin.users.toggle', $mhs))->assertRedirect();
        $this->assertTrue($mhs->fresh()->is_active);
    }

    public function test_admin_tidak_bisa_menghapus_atau_menonaktifkan_diri_sendiri(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertSessionHas('error');
        $this->actingAs($admin)->post(route('admin.users.toggle', $admin))->assertSessionHas('error');
        $this->actingAs($admin)->put(route('admin.users.update', $admin), ['name' => $admin->name, 'email' => $admin->email, 'is_active' => '0'])->assertSessionHasErrors('is_active');

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'is_active' => 1]);
    }

    public function test_menghapus_dosen_yang_masih_membimbing_ditolak_dan_mahasiswa_terhapus_beserta_data(): void
    {
        Storage::fake(PrivateStorage::DISK);
        $admin = $this->makeAdmin();
        [$dosen, $mhs, $i] = $this->makeSetup();

        $this->actingAs($admin)->delete(route('admin.users.destroy', $dosen))->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $dosen->id]);

        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['lampiran' => [$this->png()]]));
        $this->actingAs($mhs)->post(route('mahasiswa.laporan.store'), ['file' => $this->pdf()]);
        $paths = \App\Models\LogbookAttachment::pluck('path')->all();
        Storage::disk(PrivateStorage::DISK)->assertExists($paths[0]);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $mhs))->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $mhs->id]);
        $this->assertSame(0, Logbook::count());
        $this->assertDatabaseCount('logbook_attachments', 0);
        $this->assertDatabaseCount('report_versions', 0);
        Storage::disk(PrivateStorage::DISK)->assertMissing($paths[0]);
        $this->assertDatabaseHas('users', ['id' => $dosen->id]);
        $this->assertDatabaseHas('activity_logs', ['aksi' => 'user.dihapus', 'user_id' => $admin->id]);
    }

    public function test_pencarian_dan_filter_pengguna(): void
    {
        $admin = $this->makeAdmin();
        [$dosen, $mhs] = $this->makeSetup();

        $this->actingAs($admin)->get(route('admin.users.index', ['q' => $mhs->name]))->assertOk()->assertSee($mhs->name)->assertDontSee($dosen->email);
        $this->actingAs($admin)->get(route('admin.users.index', ['role' => 'dosen']))->assertOk()->assertSee($dosen->email)->assertDontSee($mhs->email);
        $this->actingAs($admin)->get(route('admin.users.index', ['status' => 'nonaktif']))->assertOk()->assertDontSee($mhs->email);
    }

    public function test_dashboard_admin_memakai_data_database(): void
    {
        $admin = $this->makeAdmin();
        $this->makeSetup();
        $this->makeSetup();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk()->assertViewHas('stats', fn ($s) => $s['total'] === 5
            && $s['admin'] === 1 && $s['dosen'] === 2 && $s['mahasiswa'] === 2 && $s['nonaktif'] === 0);
    }

    public function test_setiap_role_dapat_mengganti_password_sendiri(): void
    {
        [$dosen, $mhs] = $this->makeSetup();
        $admin = $this->makeAdmin();

        foreach ([$admin, $dosen, $mhs] as $u) {
            $this->actingAs($u)->put(route('profile.password'), [
                'current_password' => 'password', 'password' => 'PasswordBaru1', 'password_confirmation' => 'PasswordBaru1',
            ])->assertSessionHasNoErrors();
            $this->assertTrue(Hash::check('PasswordBaru1', $u->fresh()->password), "gagal untuk {$u->role->value}");

            $this->actingAs($u->fresh())->put(route('profile.password'), [
                'current_password' => 'salah', 'password' => 'LagiBaru123', 'password_confirmation' => 'LagiBaru123',
            ])->assertSessionHasErrorsIn('password', 'current_password');
            $this->assertTrue(Hash::check('PasswordBaru1', $u->fresh()->password));

            $this->actingAs($u)->get(route('profile.edit'))->assertOk();
        }
    }
}
