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

    // Monthly subscription price in GBP
    'monthly_price' => 10.00,

    // Trial period in days (if applicable)
    'trial_days' => 30,

    // Grace period after subscription ends (in days)
    'grace_period_days' => 3,

    // Stripe price IDs for each currency
    'stripe_prices' => [
        'GBP' => env('STRIPE_PRICE_GBP_ID'),
        'EUR' => env('STRIPE_PRICE_EUR_ID'),
        'USD' => env('STRIPE_PRICE_USD_ID'),
    ],
];
