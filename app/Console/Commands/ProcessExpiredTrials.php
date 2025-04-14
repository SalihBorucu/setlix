<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessExpiredTrials extends Command
{
    protected $signature = 'trials:process-expired';
    protected $description = 'Process expired trials and update user statuses';

    public function handle()
    {
        $expiredTrialUsers = User::where('is_trial', true)
            ->where('trial_ends_at', '<', now())
            ->get();

        foreach ($expiredTrialUsers as $user) {
            $user->update([
                'is_trial' => false
            ]);

            // Update any bands owned by this user
            $user->adminBands()->update([
                'trial_ends_at' => null
            ]);
        }

        $this->info("Processed {$expiredTrialUsers->count()} expired trials.");
    }
} 