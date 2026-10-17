<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public function log(User $actor, string $aksi, ?Internship $internship = null, ?Model $subject = null, array $meta = []): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $actor->id,
            'internship_id' => $internship?->id,
            'aksi' => $aksi,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'metadata' => $meta ?: null,
        ]);
    }
}
