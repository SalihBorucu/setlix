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
            // Create or retrieve Stripe customer
            $customer = $this->getOrCreateCustomer($user);

            $priceId = config('services.stripe.price_id');
            if (!$priceId) {
                throw new \Exception('Stripe price ID is not configured');
            }

            // Create subscription with immediate payment
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
                ],
            ]);

            // Store subscription ID immediately
            $band->update([
                'stripe_subscription_id' => $subscription->id,
                'subscription_status' => 'incomplete' // Will be updated to 'active' after payment success
            ]);

            return [
                'subscriptionId' => $subscription->id,
                'clientSecret' => $subscription->latest_invoice->payment_intent->client_secret,
            ];

        } catch (ApiErrorException $e) {
            \Log::error('Stripe subscription creation failed', [
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