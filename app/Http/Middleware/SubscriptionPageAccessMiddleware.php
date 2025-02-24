<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Band;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionPageAccessMiddleware
{
    /**
     * Handle subscription page access control
     * 
     * Checks:
     * 1. If band is already subscribed -> redirect to band page
     * 2. If user is not band admin -> redirect to dashboard
     * 3. If band is in trial -> redirect to band page
     */
    public function handle(Request $request, Closure $next): Response
    {
        $band = $request->route('band');
        
        if (!$band instanceof Band) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid band specified.');
        }

        $user = $request->user();

        // Check if user is band admin
        if (!$band->isAdmin($user)) {
            return redirect()->route('dashboard')
                ->with('error', 'Only band administrators can manage subscriptions.');
        }

        // Check if band is already subscribed
        if ($band->subscription_status === 'active') {
            return redirect()->route('bands.show', $band)
                ->with('info', 'This band already has an active subscription.');
        }

        // Check if band is in trial period
        if ($band->trial_ends_at && now()->lt($band->trial_ends_at)) {
            return redirect()->route('bands.show', $band)
                ->with('info', 'This band is currently in trial period.');
        }

        return $next($request);
    }
} 