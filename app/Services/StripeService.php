<?php

namespace App\Services;

use App\Models\Band;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Carbon\Carbon;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createSubscription(Band $band, User $user, array $paymentData)
    {
        try {
            // Generate idempotency key based on band and user
            $idempotencyKey = md5($band->id . '_' . $user->id . '_' . time());

            // Create or retrieve Stripe customer
            $customer = $this->getOrCreateCustomer($user);

            $priceId = config('services.stripe.price_id');
            if (!$priceId) {
                throw new \Exception('Stripe price ID is not configured');
            }

            // Create subscription with immediate payment and idempotency key
            $subscription = \Stripe\Subscription::create([
                'customer' => $customer->id,
                'items' => [
                    [
                        'price' => $priceId,
                    ],
                ],
                'payment_behavior' => 'default_incomplete',
                'expand' => ['latest_invoice.payment_intent'],
                'metadata' => [
                    'band_id' => $band->id,
                    'user_id' => $user->id,
                    'idempotency_key' => $idempotencyKey
                ],
            ], [
                'idempotency_key' => $idempotencyKey
            ]);

            // Validate subscription creation
            if (!$subscription->id || !$subscription->latest_invoice->payment_intent) {
                throw new \Exception('Invalid subscription response from Stripe');
            }

            // Store subscription ID and metadata immediately
            $band->update([
                'stripe_subscription_id' => $subscription->id,
                'subscription_status' => 'incomplete', // Will be updated to 'active' after payment success
                'idempotency_key' => $idempotencyKey
            ]);

            // Log subscription creation attempt
            \Log::info('Subscription creation initiated', [
                'band_id' => $band->id,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'idempotency_key' => $idempotencyKey
            ]);

            return [
                'subscriptionId' => $subscription->id,
                'clientSecret' => $subscription->latest_invoice->payment_intent->client_secret,
            ];

        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle Stripe-specific errors
            \Log::error('Stripe API error during subscription creation', [
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
                'user_id' => $user->id,
                'band_id' => $band->id,
                'price_id' => $priceId ?? 'not set',
                'stripe_error_code' => $e->getStripeCode(),
                'stripe_error_type' => $e->getStripeParam()
            ]);
            throw new \Exception('Payment service error: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Handle other errors
            \Log::error('Subscription creation failed', [
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
                'user_id' => $user->id,
                'band_id' => $band->id,
                'price_id' => $priceId ?? 'not set',
            ]);
            throw $e;
        }
    }

    protected function getOrCreateCustomer(User $user)
    {
        if ($user->stripe_customer_id) {
            return Customer::retrieve($user->stripe_customer_id);
        }

        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id
            ]
        ]);

        // Save Stripe customer ID to user
        $user->update(['stripe_customer_id' => $customer->id]);

        return $customer;
    }

    /**
     * Cancel a band's subscription in Stripe
     *
     * @param Band $band The band whose subscription to cancel
     * @return array Information about the cancelled subscription
     * @throws \Exception If subscription cancellation fails
     */
    public function cancelSubscription(Band $band): array
    {
        try {
            if (!$band->stripe_subscription_id) {
                throw new \Exception('No active subscription found for this band');
            }

            // Retrieve the subscription from Stripe
            $subscription = \Stripe\Subscription::retrieve($band->stripe_subscription_id);

            // Cancel the subscription at period end
            $cancelledSubscription = $subscription->cancel();

            // Log the cancellation
            \Log::info('Subscription cancelled successfully', [
                'band_id' => $band->id,
                'subscription_id' => $band->stripe_subscription_id,
                'cancellation_date' => now(),
                'end_date' => Carbon::createFromTimestamp($cancelledSubscription->current_period_end)
            ]);

            return [
                'status' => 'cancelled',
                'subscription_id' => $cancelledSubscription->id,
                'cancellation_effective_date' => Carbon::createFromTimestamp($cancelledSubscription->current_period_end)
            ];

        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('Stripe API error during subscription cancellation', [
                'error' => $e->getMessage(),
                'band_id' => $band->id,
                'subscription_id' => $band->stripe_subscription_id ?? 'not set'
            ]);
            throw new \Exception('Failed to cancel subscription: ' . $e->getMessage());
        }
    }
} 