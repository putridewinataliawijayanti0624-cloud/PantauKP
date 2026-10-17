<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Catatan audit: hanya boleh di-insert. Update/delete lewat Eloquent diblokir. */
class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    public const LABELS = [
        'mahasiswa.ditambahkan' => ['Menambahkan mahasiswa bimbingan', 'person-plus', 'primary'],
        'user.dibuat' => ['Membuat akun pengguna', 'person-plus', 'primary'],
        'user.diubah' => ['Mengubah data pengguna', 'pencil-square', 'info'],
        'user.password_direset' => ['Mereset password pengguna', 'key', 'warning'],
        'user.diaktifkan' => ['Mengaktifkan akun', 'toggle-on', 'success'],
        'user.dinonaktifkan' => ['Menonaktifkan akun', 'toggle-off', 'secondary'],
        'user.dihapus' => ['Menghapus akun pengguna', 'person-x', 'danger'],
        'logbook.dikirim' => ['Mengirim logbook', 'send', 'warning'],
        'logbook.disetujui' => ['Menyetujui logbook', 'check-circle', 'success'],
        'logbook.perlu_revisi' => ['Meminta revisi logbook', 'arrow-repeat', 'danger'],
        'laporan.diunggah' => ['Mengunggah laporan KP', 'file-earmark-arrow-up', 'warning'],
        'laporan.disetujui' => ['Menyetujui laporan KP', 'check-circle', 'success'],
        'laporan.perlu_revisi' => ['Meminta revisi laporan KP', 'arrow-repeat', 'danger'],
    ];

    protected $fillable = ['user_id', 'internship_id', 'aksi', 'subject_type', 'subject_id', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'array', 'created_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::updating(fn () => false);
        static::deleting(fn () => false);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function scopeForLecturer(Builder $q, User $user): Builder
    {
        return $q->whereIn('internship_id', Supervision::where('lecturer_id', $user->id)->select('internship_id'));
    }

    public function scopeForStudent(Builder $q, User $user): Builder
    {
        return $q->whereIn('internship_id', Internship::where('student_id', $user->id)->select('id'));
    }

    public function getLabelAttribute(): string
    {
        return self::LABELS[$this->aksi][0] ?? $this->aksi;
    }

    public function getIconAttribute(): string
    {
        return self::LABELS[$this->aksi][1] ?? 'activity';
    }

    public function getColorAttribute(): string
    {
        return self::LABELS[$this->aksi][2] ?? 'secondary';
    }
}
