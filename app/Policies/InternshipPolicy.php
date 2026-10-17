<?php

namespace App\Policies;

use App\Enums\ReportStatus;
use App\Models\Internship;
use App\Models\User;

class InternshipPolicy
{
    public function view(User $user, Internship $internship): bool
    {
        return $internship->isOwnedBy($user) || $internship->isSupervisedBy($user);
    }

    public function createLogbook(User $user, Internship $internship): bool
    {
        return $internship->isOwnedBy($user);
    }

    public function uploadReport(User $user, Internship $internship): bool
    {
        return $internship->isOwnedBy($user)
            && in_array($internship->reportStatus(), [ReportStatus::BelumDiunggah, ReportStatus::PerluRevisi], true);
    }
}
