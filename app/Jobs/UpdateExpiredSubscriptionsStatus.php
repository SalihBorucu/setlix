<?php

namespace App\Jobs;

use App\Types\StripeStatusTypes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Cashier\Subscription;

class UpdateExpiredSubscriptionsStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Update all active subscriptions that have ended
            $updated = Subscription::query()
                ->where('stripe_status', StripeStatusTypes::_ACTIVE)
                ->where('ends_at', '<=', now())
                ->update(['stripe_status' => StripeStatusTypes::_CANCELLED]);

            if ($updated > 0) {
                report("Updated {$updated} expired subscriptions to cancelled status");
            }
        } catch (\Exception $e) {
            report($e);
        }
    }
}
