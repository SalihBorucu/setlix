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
        Log::info('Received webhook payload', [
            'payload' => json_decode($payload, true),
            'sig_header' => $sigHeader
        ]);

        $event = Webhook::constructEvent(
            $payload,
            $sigHeader,
            config('services.stripe.webhook')
        );

        return match ($event->type) {
            'customer.subscription.created' => $this->handleSubscriptionCreated($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionCancelled($event->data->object),
            default => ['status' => 'ignored', 'message' => 'Event type not handled']
        };
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
} 