<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Setlist extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'band_id',
        'name',
        'description',
        'total_duration',
        'song_order',
        'is_public',
        'public_slug',
        'target_duration',
        'client_name',
        'client_email',
        'submitted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'song_order' => 'array',
        'total_duration' => 'integer',
        'is_public' => 'boolean',
        'submitted_at' => 'datetime'
    ];

    /**
     * Get the band that owns the setlist.
     */
    public function band(): BelongsTo
    {
        return $this->belongsTo(Band::class);
    }

    /**
     * Get the songs in this setlist.
     */
    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'setlist_song');
    }

    /**
     * Calculate and update the total duration of the setlist.
     */
    public function updateTotalDuration(): void
    {
        // Reload the songs relationship to get fresh data
        $this->load('songs');
        $this->total_duration = $this->songs->sum('duration_seconds');
        $this->save();
    }

    /**
     * Format the total duration as a human-readable string.
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = floor($this->total_duration / 60);
        $seconds = $this->total_duration % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function items()
    {
        return $this->hasMany(SetlistItem::class)->orderBy('order');
    }

    public function calculateTotalDuration(): int
    {
        return $this->items()->sum('duration_seconds');
    }
}
