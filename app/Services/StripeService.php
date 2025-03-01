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

    public function createSubscription(Band $band, User $user, array $paymentData): array
    {
        try {
            ray('Creating subscription with data:', [
                'band_id' => $band->id,
                'user_id' => $user->id,
                'payment_data' => $paymentData
            ]);

            // Generate idempotency key based on band and user
            $idempotencyKey = md5($band->id . '_' . $user->id . '_' . time());

            // Create or retrieve Stripe customer
            $customer = $this->getOrCreateCustomer($user);
            ray('Customer:', $customer->toArray());

            $priceId = config('services.stripe.price_id');
            if (!$priceId) {
                throw new \Exception('Stripe price ID is not configured');
            }

            ray('Price ID:', $priceId);

            // Create subscription with immediate payment and idempotency key
            $subscriptionData = [
                'customer' => $customer->id,
                'items' => [
                    [
                        'price' => $priceId,
                    ],
                ],
                'payment_behavior' => 'error_if_incomplete',
                'expand' => ['latest_invoice.payment_intent'],
                'metadata' => [
                    'band_id' => $band->id,
                    'user_id' => $user->id,
                    'idempotency_key' => $idempotencyKey
                ],
            ];

            // Add payment method if provided
            if (!empty($paymentData['payment_method'])) {
                $subscriptionData['default_payment_method'] = $paymentData['payment_method'];
            }

            ray('Subscription data:', $subscriptionData);

            $subscription = \Stripe\Subscription::create($subscriptionData, [
                'idempotency_key' => $idempotencyKey
            ]);

            ray('Stripe response:', $subscription->toArray());

            // Validate subscription creation
            if (!$subscription->id || !$subscription->latest_invoice->payment_intent) {
                throw new \Exception('Invalid subscription response from Stripe');
            }

            // Create band subscription record
            \App\Models\BandSubscription::create([
                'band_id' => $band->id,
                'user_id' => $user->id,
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $priceId,
                'stripe_status' => 'incomplete'
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
        try {
            ray('Getting or creating customer for user:', [
                'user_id' => $user->id,
                'stripe_id' => $user->stripe_id
            ]);

            // If user already has a Stripe ID, retrieve the customer
            if ($user->stripe_id) {
                ray('Retrieving existing customer');
                $customer = Customer::retrieve($user->stripe_id);
                ray('Retrieved customer:', $customer->toArray());
                return $customer;
            }

            // Create a new customer
            ray('Creating new customer');
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => [
                    'user_id' => $user->id
                ]
            ]);

            // Update user with new Stripe ID
            $user->stripe_id = $customer->id;
            $user->save();

            ray('Created new customer:', $customer->toArray());
            return $customer;

        } catch (\Exception $e) {
            ray('Error in getOrCreateCustomer:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
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
            if (!$band->subscription->stripe_subscription_id) {
                throw new \Exception('No active subscription found for this band');
            }

            // Retrieve the subscription from Stripe
            $subscription = \Stripe\Subscription::retrieve($band->subscription->stripe_subscription_id);

            // Cancel the subscription at period end
            $cancelledSubscription = $subscription->cancel();

            // Log the cancellation
            ray('Subscription cancelled successfully', [
                'band_id' => $band->id,
                'subscription_id' => $band->subscription->stripe_subscription_id,
                'cancellation_date' => now(),
                'end_date' => Carbon::createFromTimestamp($cancelledSubscription->current_period_end)
            ]);

            return [
                'status' => 'cancelled',
                'subscription_id' => $cancelledSubscription->id,
                'cancellation_effective_date' => Carbon::createFromTimestamp($cancelledSubscription->current_period_end)
            ];

        } catch (\Stripe\Exception\ApiErrorException $e) {
            ray('Stripe API error during subscription cancellation', [
                'error' => $e->getMessage(),
                'band_id' => $band->id,
                'subscription_id' => $band->stripe_subscription_id ?? 'not set'
            ]);
            throw new \Exception('Failed to cancel subscription: ' . $e->getMessage());
        }
    }
}
