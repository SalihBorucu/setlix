<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Settings
    |--------------------------------------------------------------------------
    |
    | These settings control various aspects of the subscription system
    |
    */

    // Trial period in days (if applicable)
    'trial_days' => 30,

    // Grace period after subscription ends (in days)
    'grace_period_days' => 3,

    // Stripe yearly price ID (multi-currency)
    'stripe_price_id' => env('STRIPE_YEARLY_PRICE_ID'),
];
