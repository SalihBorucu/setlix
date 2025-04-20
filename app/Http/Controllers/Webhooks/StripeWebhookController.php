<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Band;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Cashier\Subscription;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhook events
     */
    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                config('cashier.webhook.secret')
            );

            ray('Webhook event received:', [
                'type' => $event->type,
                'data' => $event->data->object
            ]);

            switch ($event->type) {
                case 'customer.subscription.created':
                case 'customer.subscription.updated':
                case 'customer.subscription.deleted':
                    $this->handleSubscriptionUpdated($event->data->toArray());
                    break;
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            ray('Webhook error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    protected function handleSubscriptionUpdated(array $payload)
    {
        $stripeSubscription = $payload['object'];

        // Find subscription by Stripe ID
        $subscription = Subscription::where('stripe_id', $stripeSubscription['id'])->first();

        if ($subscription) {
            ray('Subscription updated', [
                'id' => $subscription->id,
                'status' => $stripeSubscription['status']
            ]);

            // If band_id is not set but exists in metadata, update it
            if (!$subscription->band_id && isset($stripeSubscription['metadata']['band_id'])) {
                $subscription->band_id = $stripeSubscription['metadata']['band_id'];
            }

            $subscription->update([
                'stripe_status' => $stripeSubscription['status'],
                'ends_at' => isset($stripeSubscription['cancel_at'])
                    ? Carbon::createFromTimestamp($stripeSubscription['cancel_at'])
                    : null
            ]);

            // Update band trial status if needed
            if ($subscription->band_id) {
                $band = Band::find($subscription->band_id);
                if ($band) {
                    $band->update([
                        'trial_ends_at' => null
                    ]);
                }
            }

            // Update user trial status if needed
            $user = $subscription->user;
            if ($user && $user->is_trial) {
                $user->update([
                    'trial_started_at' => null,
                    'trial_ends_at' => null,
                    'is_trial' => false
                ]);
            }
        }
    }
}
