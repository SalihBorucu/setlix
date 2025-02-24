<?php

namespace App\Services;

use App\Models\Band;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;

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
} 