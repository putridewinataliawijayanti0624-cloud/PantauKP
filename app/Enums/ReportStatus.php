<?php

namespace App\Enums;

enum ReportStatus: string
{
    case BelumDiunggah = 'belum_diunggah';
    case Menunggu = 'menunggu';
    case PerluRevisi = 'perlu_revisi';
    case Disetujui = 'disetujui';

    public function label(): string
    {
        return match ($this) {
            self::BelumDiunggah => 'Belum diunggah',
            self::Menunggu => 'Menunggu pemeriksaan',
            self::PerluRevisi => 'Perlu revisi',
            self::Disetujui => 'Disetujui',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BelumDiunggah => 'secondary',
            self::Menunggu => 'warning',
            self::PerluRevisi => 'danger',
            self::Disetujui => 'success',
        };
    }
}
