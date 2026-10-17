<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InternshipReport extends Model
{
    protected $fillable = ['internship_id'];

    protected function casts(): array
    {
        return ['status' => ReportStatus::class];
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ReportVersion::class, 'report_id')->orderByDesc('versi');
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(ReportVersion::class, 'report_id')->ofMany('versi', 'max');
    }

    public function scopeForLecturer(Builder $q, User $user): Builder
    {
        return $q->whereHas('internship.supervision', fn ($s) => $s->where('lecturer_id', $user->id));
    }
}
