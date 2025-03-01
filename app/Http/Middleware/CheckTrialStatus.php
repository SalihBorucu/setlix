<?php

namespace App\Http\Middleware;

use App\Services\TrialService;
use Closure;
use Illuminate\Http\Request;

class CheckTrialStatus
{
    protected $trialService;

    public function __construct(TrialService $trialService)
    {
        $this->trialService = $trialService;
    }

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user) {
            return $next($request);
        }

        // Skip check for subscribed users
        if (!$user->is_trial) {
            return $next($request);
        }

        // Check trial limits
        if ($this->trialService->hasTrialExpired($user)) {
            return redirect()->route('subscription.expired');
        }

        // Add trial data to shared Inertia data
        \Inertia::share([
            'trial' => [
                'isInTrial' => $this->trialService->isInTrial($user),
                'remainingDays' => $this->trialService->getRemainingDays($user),
                'trialEndsAt' => $user->trial_ends_at?->toDateTimeString(),
            ]
        ]);

        return $next($request);
    }
} 