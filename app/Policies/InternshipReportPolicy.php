<?php

namespace App\Policies;

use App\Enums\ReportStatus;
use App\Models\InternshipReport;
use App\Models\User;

class InternshipReportPolicy
{
    public function view(User $user, InternshipReport $report): bool
    {
        $i = $report->internship;

        return $i->isOwnedBy($user) || $i->isSupervisedBy($user);
    }

    public function review(User $user, InternshipReport $report): bool
    {
        return $report->internship->isSupervisedBy($user) && $report->status === ReportStatus::Menunggu;
    }
}
