<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class Song extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'band_id',
        'name',
        'duration_seconds',
        'notes',
        'url',
        'artist',
        'bpm',
        'document_path',
        'document_type',
    ];

    protected $appends = [
        'formatted_duration',
        'formatted_created_at'
    ];

    /**
     * Get formatted duration (MM:SS)
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Format the created_at date in a human-readable format
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * Get the band that owns the song
     */
    public function band(): BelongsTo
    {
        return $this->belongsTo(Band::class);
    }

    /**
     * Check if song has a document attached
     */
    public function hasDocument(): bool
    {
        return !is_null($this->document_path);
    }

    /**
     * Get the document URL if it exists
     */
    public function getDocumentUrl(): ?string
    {
        return $this->hasDocument() ? Storage::disk('public')->url($this->document_path) : null;
    }

    /**
     * Delete the document if it exists
     */
    public function deleteDocument(): void
    {
        if ($this->hasDocument()) {
            Storage::disk('public')->delete($this->document_path);
            $this->update([
                'document_path' => null,
                'document_type' => null,
            ]);
        }
    }

    /**
     * Get the setlists that contain this song.
     */
    public function setlists(): BelongsToMany
    {
        return $this->belongsToMany(Setlist::class)
            ->withPivot('order', 'notes')
            ->orderBy('pivot_order');
    }
}
