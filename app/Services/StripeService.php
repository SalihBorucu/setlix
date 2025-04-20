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
            if (!$band->subscription->stripe_id) {
                throw new \Exception('No active subscription found for this band');
            }

            // Retrieve the subscription from Stripe
            $subscription = \Stripe\Subscription::retrieve($band->subscription->stripe_id);

            // Cancel the subscription at period end
            $cancelledSubscription = $subscription->cancel();

            return [
                'status' => 'cancelled',
                'subscription_id' => $cancelledSubscription->id,
                'cancellation_effective_date' => Carbon::createFromTimestamp($cancelledSubscription->current_period_end)
            ];

        } catch (\Stripe\Exception\ApiErrorException $e) {

            throw new \Exception('Failed to cancel subscription: ' . $e->getMessage());
        }
    }
}
