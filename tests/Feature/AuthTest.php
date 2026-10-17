<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\BuildsKpData;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use BuildsKpData, RefreshDatabase;

    public function test_dosen_login_diarahkan_ke_dashboard_dosen(): void
    {
        [$dosen] = $this->makeSetup();
        $this->post('/login', ['email' => $dosen->email, 'password' => 'password'])->assertRedirect(route('dosen.dashboard'));
        $this->assertAuthenticatedAs($dosen);
    }

    public function test_mahasiswa_login_diarahkan_ke_dashboard_mahasiswa(): void
    {
        [, $mhs] = $this->makeSetup();
        $this->post('/login', ['email' => $mhs->email, 'password' => 'password'])->assertRedirect(route('mahasiswa.dashboard'));
    }

    public function test_password_salah_ditolak(): void
    {
        [$dosen] = $this->makeSetup();
        $this->post('/login', ['email' => $dosen->email, 'password' => 'salah'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout(): void
    {
        [$dosen] = $this->makeSetup();
        $this->actingAs($dosen)->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_tamu_diarahkan_ke_login_dan_tidak_ada_registrasi_publik(): void
    {
        $this->get(route('dosen.dashboard'))->assertRedirect(route('login'));
        $this->get('/register')->assertNotFound();
    }
}
