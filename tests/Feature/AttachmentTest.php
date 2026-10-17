<?php

namespace Tests\Feature;

use App\Models\LogbookAttachment;
use App\Support\PrivateStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\BuildsKpData;
use Tests\TestCase;

class AttachmentTest extends TestCase
{
    use BuildsKpData, RefreshDatabase;

    public function test_upload_dan_akses_lampiran_berdasarkan_otorisasi(): void
    {
        Storage::fake(PrivateStorage::DISK);
        [$dosen, $mhs, $i] = $this->makeSetup();
        [$dosenLain, $mhsLain] = $this->makeSetup();

        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['lampiran' => [$this->png()]]))->assertSessionDoesntHaveErrors();

        $a = LogbookAttachment::firstOrFail();
        Storage::disk(PrivateStorage::DISK)->assertExists($a->path);
        $this->assertStringStartsNotWith('public', $a->path);

        $url = route('files.logbook', $a);
        $this->actingAs($mhs)->get($url)->assertOk();
        $this->actingAs($dosen)->get($url)->assertOk();
        $this->actingAs($mhsLain)->get($url)->assertForbidden();
        $this->actingAs($dosenLain)->get($url)->assertForbidden();
    }

    public function test_tamu_tidak_bisa_mengunduh_lampiran(): void
    {
        Storage::fake(PrivateStorage::DISK);
        [, $mhs] = $this->makeSetup();
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['lampiran' => [$this->png()]]));
        $a = LogbookAttachment::firstOrFail();

        auth()->logout();
        $this->get(route('files.logbook', $a))->assertRedirect(route('login'));
    }

    public function test_tipe_dan_ukuran_file_dibatasi(): void
    {
        Storage::fake(PrivateStorage::DISK);
        [, $mhs] = $this->makeSetup();

        $exe = UploadedFile::fake()->createWithContent('virus.exe', "MZ\x90\x00\x03\x00\x00\x00");
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['lampiran' => [$exe]]))->assertSessionHasErrors('lampiran.0');

        $big = UploadedFile::fake()->create('besar.pdf', 6000, 'application/pdf');
        $this->actingAs($mhs)->post(route('mahasiswa.logbook.store'), $this->logbookPayload(['lampiran' => [$big]]))->assertSessionHasErrors('lampiran.0');

        $this->assertDatabaseCount('logbook_attachments', 0);
    }
}
