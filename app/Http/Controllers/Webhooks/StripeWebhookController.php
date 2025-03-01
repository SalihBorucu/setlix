<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\BandSubscription;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
                    $subscription = $event->data->object;
                    $bandSubscription = BandSubscription::where('stripe_subscription_id', $subscription->id)->first();

                    if ($bandSubscription) {
                        ray('Updating band subscription:', [
                            'id' => $bandSubscription->id,
                            'new_status' => $subscription->status
                        ]);

                        $bandSubscription->update([
                            'stripe_status' => $subscription->status,
                            'ends_at' => $subscription->cancel_at ? Carbon::createFromTimestamp($subscription->cancel_at) : null,
                        ]);

                        // Update band status as well
                        $bandSubscription->band->update([
                            'subscription_status' => $subscription->status
                        ]);
                    } else {
                        $exception = new Exception('Band subscription not found for Stripe subscription: ' . $subscription->id);
                        report($exception);
                    }
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
} 