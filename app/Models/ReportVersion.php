<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportVersion extends Model
{
    public $timestamps = false;

    protected $fillable = ['report_id', 'versi', 'path', 'nama_asli', 'ukuran', 'uploaded_at'];

    protected function casts(): array
    {
        return ['uploaded_at' => 'datetime'];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(InternshipReport::class, 'report_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ReportReview::class)->latest('id');
    }
}
