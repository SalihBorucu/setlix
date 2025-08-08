<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class TrialService
{
    /**
     * Start trial for a user when they create their first band
     */
    public function startTrial(User $user): void
    {
        if (!$user->trial_started_at) {
            $user->trial_started_at = now();
            $user->trial_ends_at = now()->addDays(30);
            $user->save();
        }
    }

    /**
     * Check if user is in trial period
     */
    public function isInTrial(User $user): bool
    {
        return $user->trial_ends_at && $user->trial_ends_at->isFuture();
    }

    /**
     * Check if trial has expired
     */
    public function hasTrialExpired(User $user): bool
    {
        return $user->trial_ends_at && $user->trial_ends_at->isPast() && !$user->is_subscribed;
    }

    /**
     * Get remaining trial days
     */
    public function getRemainingDays(User $user): int
    {
        if (!$user->trial_ends_at) {
            return 30;
        }
        return max(0, now()->diffInDays($user->trial_ends_at));
    }
}
