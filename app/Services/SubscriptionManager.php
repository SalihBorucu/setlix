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
        // Check for existing active subscription
        if ($band->is_subscribed && $band->subscription_status === 'active') {
            throw new \Exception('Band already has an active subscription');
        }

        // Verify payment intent with Stripe
        $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
        
        // Validate payment intent status
        if ($paymentIntent->status !== 'succeeded') {
            throw new \Exception('Payment was not successful');
        }

        // Validate payment amount matches expected subscription price
        $expectedAmount = 1000; // $10.00 in cents
        if ($paymentIntent->amount !== $expectedAmount) {
            throw new \Exception('Invalid payment amount');
        }

        // Verify subscription exists
        if (!$band->stripe_subscription_id) {
            throw new \Exception('No subscription ID found for band');
        }

        $subscription = Subscription::retrieve($band->stripe_subscription_id);

        // Verify subscription status
        if ($subscription->status !== 'active' && $subscription->status !== 'trialing') {
            throw new \Exception('Invalid subscription status');
        }

        // Use database transaction to prevent race conditions
        \DB::transaction(function () use ($band, $user, $subscription) {
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
        });

        // Log successful subscription activation
        \Log::info('Subscription activated successfully', [
            'band_id' => $band->id,
            'user_id' => $user->id,
            'subscription_id' => $subscription->id
        ]);
    }
} 