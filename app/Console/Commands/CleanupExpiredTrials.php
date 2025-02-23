<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanupExpiredTrials extends Command
{
    protected $signature = 'trials:cleanup';
    protected $description = 'Clean up expired trial data';

    public function handle()
    {
        // Soft delete data for trials expired more than 7 days ago
        User::query()
            ->where('trial_ends_at', '<', now()->subDays(7))
            ->where('is_subscribed', false)
            ->whereNull('soft_deleted_at')
            ->update(['soft_deleted_at' => now()]);

        // Permanently delete data soft-deleted more than 30 days ago
        User::query()
            ->where('soft_deleted_at', '<', now()->subDays(30))
            ->each(function ($user) {
                // Delete associated data
                $user->bands()->forceDelete();
                $user->delete();
            });
    }
} 