<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'password_set',
        'stripe_customer_id',
        'trial_started_at',
        'trial_ends_at',
        'is_subscribed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'password' => 'hashed',
        'password_set' => 'boolean',
    ];

    /**
     * Get all bands that the user is a member of
     */
    public function bands(): BelongsToMany
    {
        return $this->belongsToMany(Band::class, 'band_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Get bands where the user is an admin
     */
    public function adminBands(): BelongsToMany
    {
        return $this->bands()->wherePivot('role', 'admin');
    }

    /**
     * Get all band subscriptions for the user
     */
    public function bandSubscriptions(): HasMany
    {
        return $this->hasMany(BandSubscription::class);
    }

    /**
     * Check if user is admin of a specific band
     */
    public function isAdminOf(Band $band): bool
    {
        return $this->adminBands()->where('band_id', $band->id)->exists();
    }

    /**
     * Check if a band has an active subscription
     */
    public function hasBandSubscription(Band $band): bool
    {
        return $this->bandSubscriptions()
            ->where('band_id', $band->id)
            ->whereNull('ends_at')
            ->exists();
    }

    /**
     * Create a subscription for a band
     */
    public function subscribeBand(Band $band, string $priceId, array $options = []): \Laravel\Cashier\Subscription
    {
        return $this->newSubscription("band_{$band->id}", $priceId)
            ->create(null, array_merge([
                'metadata' => [
                    'band_id' => $band->id,
                    'user_id' => $this->id,
                ]
            ], $options));
    }

    /**
     * Trial-related functionality for users
     */
    public function isInTrialPeriod(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function hasTrialExpired(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast() && !$this->is_subscribed;
    }

    public function getRemainingTrialDays(): int
    {
        if (!$this->trial_ends_at) {
            return 0;
        }
        return max(0, now()->diffInDays($this->trial_ends_at));
    }

    public function canCreateMoreBands(): bool
    {
        if ($this->is_subscribed) {
            return true;
        }
        return $this->bands()->count() < 1;
    }

    public function canAddMoreMembers(Band $band): bool
    {
        if ($this->hasBandSubscription($band)) {
            return true;
        }
        return $band->members()->count() < 3;
    }
}
