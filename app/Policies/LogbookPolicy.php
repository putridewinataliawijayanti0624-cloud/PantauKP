<?php

namespace App\Policies;

use App\Enums\LogbookStatus;
use App\Models\Logbook;
use App\Models\User;

class LogbookPolicy
{
    public function view(User $user, Logbook $logbook): bool
    {
        $i = $logbook->internship;

        return $i->isOwnedBy($user) || $i->isSupervisedBy($user);
    }

    /** Mahasiswa pemilik, hanya saat Draft atau Perlu revisi. Berlaku juga untuk submit. */
    public function update(User $user, Logbook $logbook): bool
    {
        return $logbook->internship->isOwnedBy($user) && $logbook->isEditable();
    }

    public function delete(User $user, Logbook $logbook): bool
    {
        return $logbook->internship->isOwnedBy($user) && $logbook->status === LogbookStatus::Draft;
    }

    /** Dosen pembimbing, hanya saat status Menunggu pemeriksaan. */
    public function review(User $user, Logbook $logbook): bool
    {
        return $logbook->internship->isSupervisedBy($user) && $logbook->status === LogbookStatus::Menunggu;
    }
}
