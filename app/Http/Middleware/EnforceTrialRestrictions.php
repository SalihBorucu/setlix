<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceTrialRestrictions
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user->is_trial) {
            return $next($request);
        }

        // Check if trial has expired
        if ($user->trial_ends_at && $user->trial_ends_at->isPast()) {
            $user->update(['is_trial' => false]);
            
            // Update any bands owned by this user
            $user->adminBands()->update([
                'trial_ends_at' => null
            ]);

            return redirect()->route('subscription.expired')
                ->with('error', 'Your trial period has expired.');
        }

        return $next($request);
    }
} 