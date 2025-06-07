<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SongFile extends Model
{
    protected $fillable = [
        'song_id',
        'type',
        'file_path',
        'original_filename',
        'file_size',
        'instrument',
    ];

    /**
     * Get the song that owns the file.
     */
    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
}
