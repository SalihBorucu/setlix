<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShareTrialData
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if ($user) {
            Inertia::share([
                'trial' => [
                    'isInTrial' => $user->isInTrialPeriod(),
                    'remainingDays' => $user->getRemainingTrialDays(),
                    'limitReached' => session('trial_limit_reached', false),
                    'limitMessage' => session('trial_limit_message', ''),
                ]
            ]);
        }

        return $next($request);
    }
} 