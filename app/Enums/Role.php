<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Dosen = 'dosen';
    case Mahasiswa = 'mahasiswa';
}
