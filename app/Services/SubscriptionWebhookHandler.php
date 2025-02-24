<?php

namespace App\Services;

use App\Models\Band;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\Webhook;

class SubscriptionWebhookHandler
{
    /**
     * Process incoming Stripe webhook event
     * 
     * @param string $payload Raw webhook payload
     * @param string $sigHeader Stripe signature header
     * @return array Response data
     * @throws \Exception If webhook validation fails
     */
    public function handleWebhook(string $payload, string $sigHeader): array
    {
        if (empty($sigHeader)) {
            throw new \Exception('Missing Stripe signature header');
        }

        try {
            // Log incoming webhook attempt
            Log::info('Processing webhook payload', [
                'signature' => substr($sigHeader, 0, 10) . '...',  // Log only part of signature for security
                'payload_size' => strlen($payload)
            ]);

            // Verify webhook signature
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook')
            );

            // Validate event data
            if (!isset($event->data->object)) {
                throw new \Exception('Invalid event data structure');
            }

            // Process based on event type with proper error handling
            $result = match ($event->type) {
                'customer.subscription.created' => $this->handleSubscriptionCreated($event->data->object),
                'customer.subscription.deleted' => $this->handleSubscriptionCancelled($event->data->object),
                'invoice.payment_failed' => $this->handlePaymentFailed($event->data->object),
                'invoice.payment_succeeded' => $this->handlePaymentSucceeded($event->data->object),
                default => ['status' => 'ignored', 'message' => 'Event type not handled']
            };

            // Log successful processing
            Log::info('Webhook processed successfully', [
                'event_type' => $event->type,
                'result' => $result
            ]);

            return $result;

        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Handle signature verification failure
            Log::error('Webhook signature verification failed', [
                'error' => $e->getMessage(),
                'sig_header' => substr($sigHeader, 0, 10) . '...'
            ]);
            throw new \Exception('Invalid webhook signature');

        } catch (\Exception $e) {
            // Log any other errors
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'error_type' => get_class($e)
            ]);
            throw $e;
        }
    }

    /**
     * Handle subscription created event
     */
    protected function handleSubscriptionCreated($subscription): array
    {
        // Get IDs from metadata, with null coalescing operator for safety
        $bandId = $subscription->metadata['band_id'] ?? null;
        $userId = $subscription->metadata['user_id'] ?? null;
        
        if (!$bandId || !$userId) {
            Log::error('Missing required metadata in subscription webhook', [
                'band_id' => $bandId,
                'user_id' => $userId,
                'subscription_id' => $subscription->id
            ]);
            return ['status' => 'error', 'message' => 'Missing required metadata'];
        }

        $band = Band::find($bandId);
        $user = User::find($userId);
        
        if (!$band || !$user) {
            Log::error('Band or user not found for subscription webhook', [
                'band_id' => $bandId,
                'user_id' => $userId,
                'subscription_id' => $subscription->id
            ]);
            return ['status' => 'error', 'message' => 'Band or user not found'];
        }

        $band->update([
            'subscription_status' => 'active'
        ]);

        $user->update([
            'trial_started_at' => null,
            'trial_ends_at' => null,
            'is_subscribed' => true
        ]);

        return ['status' => 'success', 'message' => 'Subscription created'];
    }

    /**
     * Handle subscription cancelled event
     */
    protected function handleSubscriptionCancelled($subscription): array
    {
        // Get IDs from metadata, with null coalescing operator for safety
        $bandId = $subscription->metadata['band_id'] ?? null;
        
        if (!$bandId) {
            Log::error('Missing band_id in subscription cancellation webhook', [
                'subscription_id' => $subscription->id
            ]);
            return ['status' => 'error', 'message' => 'Missing band_id'];
        }

        $band = Band::find($bandId);
        
        if (!$band) {
            Log::error('Band not found for subscription cancellation webhook', [
                'band_id' => $bandId,
                'subscription_id' => $subscription->id
            ]);
            return ['status' => 'error', 'message' => 'Band not found'];
        }

        $band->update([
            'subscription_status' => 'cancelled',
            'subscription_ends_at' => Carbon::createFromTimestamp($subscription->current_period_end)
        ]);

        return ['status' => 'success', 'message' => 'Subscription cancelled'];
    }

    /**
     * Handle payment failed event
     */
    protected function handlePaymentFailed($invoice): array
    {
        $subscriptionId = $invoice->subscription ?? null;
        if (!$subscriptionId) {
            throw new \Exception('Missing subscription ID in failed payment webhook');
        }

        $band = Band::where('stripe_subscription_id', $subscriptionId)->first();
        if (!$band) {
            throw new \Exception('Band not found for subscription: ' . $subscriptionId);
        }

        // Update subscription status
        $band->update([
            'subscription_status' => 'payment_failed'
        ]);

        // TODO: Implement notification to band admin about payment failure

        return ['status' => 'success', 'message' => 'Payment failure handled'];
    }

    /**
     * Handle payment succeeded event
     */
    protected function handlePaymentSucceeded($invoice): array
    {
        $subscriptionId = $invoice->subscription ?? null;
        if (!$subscriptionId) {
            throw new \Exception('Missing subscription ID in payment success webhook');
        }

        $band = Band::where('stripe_subscription_id', $subscriptionId)->first();
        if (!$band) {
            throw new \Exception('Band not found for subscription: ' . $subscriptionId);
        }

        // Verify payment amount
        $expectedAmount = 1000; // $10.00 in cents
        if ($invoice->amount_paid !== $expectedAmount) {
            Log::error('Unexpected payment amount', [
                'expected' => $expectedAmount,
                'received' => $invoice->amount_paid,
                'subscription_id' => $subscriptionId
            ]);
            throw new \Exception('Invalid payment amount received');
        }

        // Update subscription status
        $band->update([
            'subscription_status' => 'active',
            'subscription_ends_at' => null
        ]);

        return ['status' => 'success', 'message' => 'Payment success handled'];
    }
} 