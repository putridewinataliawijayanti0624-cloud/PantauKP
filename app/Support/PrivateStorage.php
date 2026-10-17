<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Penyimpanan file privat aplikasi (bukti kegiatan & laporan KP).
 *
 * Lokasi SELALU storage/app/private, tidak bergantung pada versi Laravel
 * (Laravel 11 memakai storage/app, Laravel 12 memakai storage/app/private)
 * dan tidak membutuhkan perubahan config/filesystems.php.
 */
class PrivateStorage
{
    public const DISK = 'kp_private';

    /** @return \Illuminate\Filesystem\FilesystemAdapter */
    public static function disk()
    {
        if (config('filesystems.disks.'.self::DISK) === null) {
            config(['filesystems.disks.'.self::DISK => [
                'driver' => 'local',
                'root' => storage_path('app/private'),
                'throw' => false,
            ]]);
        }

        return Storage::disk(self::DISK);
    }
}
