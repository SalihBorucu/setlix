<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetlistItem extends Model
{
    protected $fillable = [
        'setlist_id',
        'type',
        'song_id',
        'title',
        'duration_seconds',
        'notes',
        'order'
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'order' => 'integer'
    ];

    public function setlist(): BelongsTo
    {
        return $this->belongsTo(Setlist::class);
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
} 