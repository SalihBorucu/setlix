<?php

namespace App\Console\Commands;

use App\Models\Band;
use App\Services\BandSubscriptionService;
use Illuminate\Console\Command;

class ProcessExpiredTrials extends Command
{
    protected $signature = 'bands:process-expired-trials';
    protected $description = 'Process expired trial bands for read-only mode and deletion';

    public function handle(BandSubscriptionService $subscriptionService)
    {
        $bands = Band::withTrashed()->get();

        foreach ($bands as $band) {
            $status = $subscriptionService->getStatus($band);

            if ($status['isReadOnly'] && !$band->trashed()) {
                // Move to soft-deleted state
                $band->delete();
                // Notify users
                // TODO: Add notification
            }

            if ($status['isSoftDeleted'] && $band->trashed()) {
                // Permanently delete
                $band->forceDelete();
                // Notify users
                // TODO: Add notification
            }
        }
    }
} 