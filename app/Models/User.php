<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 'role' sengaja TIDAK fillable (proteksi mass assignment).
    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'role' => Role::class,
        ];
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function lecturerProfile(): HasOne
    {
        return $this->hasOne(LecturerProfile::class);
    }

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class, 'student_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    /** Inisial untuk avatar (gelar seperti "Dr." / ", M.Kom." dilewati). */
    public function getInitialsAttribute(): string
    {
        $words = [];
        foreach (preg_split('/\s+/', trim($this->name)) as $w) {
            $stop = str_ends_with($w, ',');
            $w = rtrim($w, ',');
            if ($w !== '' && ! str_ends_with($w, '.')) {
                $words[] = $w;
            }
            if ($stop) {
                break;
            }
        }
        $words = $words ?: [$this->name];

        return mb_strtoupper(implode('', array_map(fn ($w) => mb_substr($w, 0, 1), array_slice($words, 0, 2))));
    }

    public function isDosen(): bool
    {
        return $this->role === Role::Dosen;
    }

    public function isMahasiswa(): bool
    {
        return $this->role === Role::Mahasiswa;
    }

    /** Magang terbaru milik mahasiswa. */
    public function currentInternship(): ?Internship
    {
        return $this->internships()->latest('tanggal_mulai')->first();
    }
}
