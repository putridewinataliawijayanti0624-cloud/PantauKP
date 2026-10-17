<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogbookAttachment extends Model
{
    protected $fillable = ['logbook_id', 'path', 'nama_asli', 'mime', 'ukuran'];

    public function logbook(): BelongsTo
    {
        return $this->belongsTo(Logbook::class);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime, 'image/');
    }
}
