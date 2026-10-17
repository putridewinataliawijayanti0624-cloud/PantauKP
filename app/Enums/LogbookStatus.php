<?php

namespace App\Enums;

enum LogbookStatus: string
{
    case Draft = 'draft';
    case Menunggu = 'menunggu';
    case Disetujui = 'disetujui';
    case PerluRevisi = 'perlu_revisi';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Menunggu => 'Menunggu pemeriksaan',
            self::Disetujui => 'Disetujui',
            self::PerluRevisi => 'Perlu revisi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'secondary',
            self::Menunggu => 'warning',
            self::Disetujui => 'success',
            self::PerluRevisi => 'danger',
        };
    }
}
