<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Types\StripeStatusTypes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;
use App\Notifications\VerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
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
        'is_trial',
        'country_code',
        'location_detected_at',
        'detected_ip',
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
        'location_detected_at' => 'datetime',
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

    public function isMemberOf(int $bandId): bool
    {
        return $this->bands()->where('band_id', $bandId)->exists();
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
        return $this->hasMany(\Laravel\Cashier\Subscription::class);
    }

    /**
     * Check if user is admin of a specific band
     */
    public function isAdminOf(Band $band): bool
    {
        return $this->adminBands()->where('band_id', $band->id)->exists();
    }

    /**
     * Check if user has an active subscription for a band
     */
    public function hasBandSubscription(Band $band): bool
    {
        return $this->bandSubscriptions()
            ->where('band_id', $band->id)
            ->where('stripe_status', StripeStatusTypes::_ACTIVE)
            ->whereNull('ends_at')
            ->exists();
    }

    /**
     * Get a specific band's subscription
     */
    public function getBandSubscription(Band $band): ?\Laravel\Cashier\Subscription
    {
        return $this->bandSubscriptions()
            ->where('band_id', $band->id)
            ->latest()
            ->first();
    }

    /**
     * Create a subscription for a band
     */
    public function subscribeBand(Band $band, string $priceId, array $options = []): \Laravel\Cashier\Subscription
    {
        $subscription = $this->newSubscription("band_{$band->id}", $priceId)
            ->create(null, array_merge([
                'metadata' => [
                    'band_id' => $band->id,
                    'user_id' => $this->id,
                ]
            ], $options));

        // Update the local subscription with band_id
        $subscription->band_id = $band->id;
        $subscription->save();

        return $subscription;
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
        return $this->trial_ends_at && $this->trial_ends_at->isPast() && !$this->is_trial;
    }

    public function finishTrialPeriod(): void
    {
        $this->update([
            'is_trial' => false,
        ]);
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
        if (!$this->is_trial) {
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

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }
}
