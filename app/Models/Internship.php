<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Internship extends Model
{
    public const STATUS_LABEL = [
        'belum_mulai' => 'Belum mulai',
        'berjalan' => 'Sedang berjalan',
        'selesai' => 'Selesai',
    ];

    public const STATUS_COLOR = [
        'belum_mulai' => 'secondary',
        'berjalan' => 'primary',
        'selesai' => 'success',
    ];

    protected $fillable = [
        'student_id', 'nama_perusahaan', 'alamat', 'pembimbing_lapangan',
        'tanggal_mulai', 'tanggal_selesai', 'status', 'target_logbook',
    ];

    protected function casts(): array
    {
        return ['tanggal_mulai' => 'date', 'tanggal_selesai' => 'date'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function supervision(): HasOne
    {
        return $this->hasOne(Supervision::class);
    }

    public function lecturer(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Supervision::class, 'internship_id', 'id', 'id', 'lecturer_id');
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(Logbook::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(InternshipReport::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function scopeForLecturer(Builder $q, User $user): Builder
    {
        return $q->whereHas('supervision', fn ($s) => $s->where('lecturer_id', $user->id));
    }

    public function scopeForStudent(Builder $q, User $user): Builder
    {
        return $q->where('student_id', $user->id);
    }

    public function hasStarted(): bool
    {
        return now()->toDateString() >= $this->tanggal_mulai->toDateString();
    }

    /** Tanggal terakhir yang boleh diisi pada logbook: hari ini. Logbook tetap bisa dibuat meski periode magang sudah lewat. */
    public function maxLogbookDate(): string
    {
        return now()->toDateString();
    }

    public function isOwnedBy(User $user): bool
    {
        return $user->isMahasiswa() && $this->student_id === $user->id;
    }

    public function isSupervisedBy(User $user): bool
    {
        return $user->isDosen() && $this->supervision()->where('lecturer_id', $user->id)->exists();
    }

    public function reportStatus(): ReportStatus
    {
        return $this->report?->status ?? ReportStatus::BelumDiunggah;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABEL[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLOR[$this->status] ?? 'secondary';
    }

    /** Progres (%) = logbook disetujui / target logbook, maksimum 100. */
    public function getProgressAttribute(): int
    {
        $approved = $this->approved_count ?? $this->logbooks()->where('status', 'disetujui')->count();

        return min(100, (int) round($approved / max(1, $this->target_logbook) * 100));
    }

    /** Target default: jumlah hari kerja (Senin-Jumat) dalam periode magang. */
    public static function hitungTarget(string|Carbon $mulai, string|Carbon $selesai): int
    {
        $d = Carbon::parse($mulai)->startOfDay();
        $end = Carbon::parse($selesai)->startOfDay();
        $n = 0;
        while ($d->lte($end)) {
            if (! $d->isWeekend()) {
                $n++;
            }
            $d->addDay();
        }

        return max(1, $n);
    }

    /** Menambahkan withCount logbook disetujui sebagai approved_count. */
    public function scopeWithApproved(Builder $q): Builder
    {
        return $q->withCount(['logbooks as approved_count' => fn ($l) => $l->where('status', 'disetujui')]);
    }
}
