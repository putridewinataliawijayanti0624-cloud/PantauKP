<?php

namespace App\Models;

use App\Enums\LogbookStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Logbook extends Model
{
    // status, submission_number, submitted_at hanya diubah oleh LogbookService.
    protected $fillable = [
        'internship_id', 'tanggal', 'judul', 'deskripsi', 'hasil', 'kendala', 'rencana_tindak_lanjut',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'submitted_at' => 'datetime',
            'status' => LogbookStatus::class,
        ];
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(LogbookAttachment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(LogbookReview::class)->latest('id');
    }

    public function scopeForLecturer(Builder $q, User $user): Builder
    {
        return $q->whereHas('internship.supervision', fn ($s) => $s->where('lecturer_id', $user->id));
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [LogbookStatus::Draft, LogbookStatus::PerluRevisi], true);
    }
}
