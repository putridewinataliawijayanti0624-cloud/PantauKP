<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supervision extends Model
{
    protected $fillable = ['lecturer_id', 'internship_id'];

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }
}
