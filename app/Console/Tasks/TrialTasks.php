<?php

namespace App\Console\Tasks;

use App\Models\User;
use App\Notifications\TrialExpirationNotification;

class TrialTasks
{
    /**
     * Send notifications to users whose trials are ending soon
     */
    public function sendExpirationNotifications(): void
    {
        User::query()
            ->where('trial_ends_at', '>', now())
            ->where('trial_ends_at', '<', now()->addDays(3))
            ->where('is_subscribed', false)
            ->each(function ($user) {
                $daysRemaining = now()->diffInDays($user->trial_ends_at);
                $user->notify(new TrialExpirationNotification($daysRemaining));
            });
    }

    /**
     * Clean up expired trial data
     */
    public function cleanupExpiredTrials(): void
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
                $user->bands()->forceDelete();
                $user->delete();
            });
    }
} 