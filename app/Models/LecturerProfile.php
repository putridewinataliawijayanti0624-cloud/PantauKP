<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LecturerProfile extends Model
{
    protected $fillable = ['user_id', 'nidn', 'program_studi'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
