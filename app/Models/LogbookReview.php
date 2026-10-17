<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookReview extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['logbook_id', 'reviewer_id', 'submission_number', 'keputusan', 'komentar', 'snapshot'];

    protected function casts(): array
    {
        return ['snapshot' => 'array', 'created_at' => 'datetime'];
    }

    public function logbook(): BelongsTo
    {
        return $this->belongsTo(Logbook::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
