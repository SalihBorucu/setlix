<?php

namespace App\Services;

use App\Models\Band;
use App\Models\User;
use Carbon\Carbon;
use Stripe\PaymentIntent;
use Stripe\Subscription;

class SubscriptionManager
{
    /**
     * Calculate remaining trial days for a band
     */
    public function calculateTrialDays(Band $band): int
    {
        $bandCreatedAt = Carbon::parse($band->created_at);
        $trialEndsAt = $bandCreatedAt->copy()->addDays(14);
        
        return now()->lt($trialEndsAt) 
            ? (int) now()->diffInDays($trialEndsAt)
            : 0;
    }

    /**
     * Process a successful subscription payment
     * 
     * @throws \Exception If payment verification fails
     */
    public function processSubscription(Band $band, User $user, string $paymentIntentId): void
    {
        // Verify payment intent with Stripe
        $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
        
        if ($paymentIntent->status !== 'succeeded') {
            throw new \Exception('Payment was not successful');
        }

        if (!$band->stripe_subscription_id) {
            throw new \Exception('No subscription ID found for band');
        }

        $subscription = Subscription::retrieve($band->stripe_subscription_id);

        // Update band subscription status
        $band->update([
            'stripe_subscription_id' => $subscription->id,
            'subscription_status' => 'active',
            'subscription_ends_at' => null,
            'is_subscribed' => true
        ]);

        // Update user trial status and subscription
        $user->update([
            'trial_started_at' => null,
            'trial_ends_at' => null,
            'is_subscribed' => true
        ]);
    }
} 