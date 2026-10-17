<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportReview extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['report_version_id', 'reviewer_id', 'keputusan', 'komentar'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(ReportVersion::class, 'report_version_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
