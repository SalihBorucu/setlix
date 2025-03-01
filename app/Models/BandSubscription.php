<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BandSubscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'band_id',
        'user_id',
        'stripe_subscription_id',
        'stripe_price_id',
        'stripe_status',
        'trial_ends_at',
        'ends_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Get the band that owns the subscription.
     */
    public function band(): BelongsTo
    {
        return $this->belongsTo(Band::class);
    }

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if the subscription is active.
     */
    public function isActive(): bool
    {
        return $this->stripe_status === 'active' && 
               (!$this->ends_at || $this->ends_at->isFuture());
    }

    /**
     * Determine if the subscription is within its trial period.
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Determine if the subscription is cancelled.
     */
    public function cancelled(): bool
    {
        return !is_null($this->ends_at);
    }

    /**
     * Determine if the subscription is cancelled and ended.
     */
    public function ended(): bool
    {
        return $this->cancelled() && $this->ends_at->isPast();
    }
}
