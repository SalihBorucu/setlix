<?php

namespace App\Services;

use App\Models\Band;
use Carbon\Carbon;

class BandSubscriptionService
{
    const TRIAL_DAYS = 14;
    const READ_ONLY_DAYS = 7;
    const DELETION_DAYS = 30;
    
    const TRIAL_LIMITS = [
        'members' => 3,
        'songs' => 10,
        'setlists' => 3
    ];

    public function getStatus(Band $band): array
    {
        if (!$band) {
            throw new \InvalidArgumentException('Band not found');
        }

        try {
            $createdAt = Carbon::parse($band->created_at);
            $trialEndsAt = $createdAt->copy()->addDays(self::TRIAL_DAYS);
            $readOnlyEndsAt = $trialEndsAt->copy()->addDays(self::READ_ONLY_DAYS);
            $deletionDate = $readOnlyEndsAt->copy()->addDays(self::DELETION_DAYS);
            
            $now = now();
            
            return [
                'isInTrial' => $now->lt($trialEndsAt),
                'isReadOnly' => $now->gte($trialEndsAt) && $now->lt($readOnlyEndsAt),
                'isSoftDeleted' => $now->gte($readOnlyEndsAt) && $now->lt($deletionDate),
                'trialDaysLeft' => $now->lt($trialEndsAt) ? (int) $now->diffInDays($trialEndsAt) : 0,
                'readOnlyDaysLeft' => $now->gte($trialEndsAt) && $now->lt($readOnlyEndsAt) 
                    ? (int) $now->diffInDays($readOnlyEndsAt) 
                    : 0,
                'willBeDeletedAt' => $deletionDate,
                'subscriptionStatus' => $band->subscription_status,
                'isSubscribed' => !$band->is_trial,
                'hasPaymentMethod' => $band->hasPaymentMethod(),
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting band subscription status', [
                'band_id' => $band->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function checkTrialLimits(Band $band): array
    {
        $limits = [
            'members' => [
                'current' => $band->users()->count(),
                'limit' => self::TRIAL_LIMITS['members'],
                'reached' => $band->users()->count() >= self::TRIAL_LIMITS['members']
            ],
            'songs' => [
                'current' => $band->songs()->count(),
                'limit' => self::TRIAL_LIMITS['songs'],
                'reached' => $band->songs()->count() >= self::TRIAL_LIMITS['songs']
            ],
            'setlists' => [
                'current' => $band->setlists()->count(),
                'limit' => self::TRIAL_LIMITS['setlists'],
                'reached' => $band->setlists()->count() >= self::TRIAL_LIMITS['setlists']
            ]
        ];

        return [
            'limits' => $limits,
            'anyLimitReached' => collect($limits)->contains('reached', true)
        ];
    }
} 