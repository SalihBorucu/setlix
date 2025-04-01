<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Band extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'cover_image_path',
        'cover_image_thumbnail_path',
        'cover_image_small_path',
    ];

    /**
     * Get all members of the band (both admins and regular members)
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'band_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get admin members of the band
     */
    public function admins(): BelongsToMany
    {
        return $this->members()->wherePivot('role', 'admin');
    }

    /**
     * Get regular (non-admin) members of the band
     */
    public function regularMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('role', 'member');
    }

    /**
     * Check if a user is a member of the band
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if a user is an admin of the band
     */
    public function isAdmin(User $user): bool
    {
        return $this->admins()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the songs for the band
     */
    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    /**
     * Get the setlists for the band
     */
    public function setlists(): HasMany
    {
        return $this->hasMany(Setlist::class);
    }

    public function invitations()
    {
        return $this->hasMany(BandInvitation::class);
    }

    /**
     * Get the URL for the original cover image
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->cover_image_path
            ? Storage::disk('public')->url($this->cover_image_path)
            : null;
    }

    /**
     * Get the URL for the thumbnail cover image
     */
    public function getCoverImageThumbnailUrlAttribute(): ?string
    {
        return $this->cover_image_thumbnail_path
            ? Storage::disk('public')->url($this->cover_image_thumbnail_path)
            : null;
    }

    /**
     * Get the URL for the small cover image
     */
    public function getCoverImageSmallUrlAttribute(): ?string
    {
        return $this->cover_image_small_path
            ? Storage::disk('public')->url($this->cover_image_small_path)
            : null;
    }

    /**
     * Get the active subscription for the band
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(BandSubscription::class)
            ->whereNull('ends_at')
            ->where('stripe_status', 'active');
    }

    /**
     * Get all subscriptions for the band
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(BandSubscription::class);
    }

    /**
     * Check if band is in trial period
     */
    public function isInTrial(): bool
    {
        $subscription = $this->subscription;

        return $subscription && $subscription->stripe_status === 'active';
    }

    /**
     * Check if band has active subscription
     */
    public function hasActiveSubscription(): bool
    {
        $subscription = $this->subscription;
        return $subscription ? $subscription->isActive() : false;
    }

    /**
     * Check if band is in read-only mode
     */
    public function isReadOnly(): bool
    {
        return !$this->hasActiveSubscription() && !$this->isInTrial();
    }

    /**
     * Check if band can perform write operations
     */
    public function canPerformWriteOperations(): bool
    {
        return $this->hasActiveSubscription() || $this->isInTrial();
    }

    /**
     * Check if band requires subscription
     */
    public function requiresSubscription(): bool
    {
        return !$this->hasActiveSubscription() && (!$this->trial_ends_at || now()->gt($this->trial_ends_at));
    }
}
