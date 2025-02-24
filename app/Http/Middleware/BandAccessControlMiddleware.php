<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Band;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BandAccessControlMiddleware
{
    /**
     * Handle band access control and restrictions
     * 
     * Checks:
     * 1. If band has no active subscription and not in trial -> redirect to subscription page
     * 2. If band is in read-only mode -> restrict write operations
     * 3. If trying to access band features after trial/subscription expiry -> redirect
     */
    public function handle(Request $request, Closure $next): Response
    {
        $band = $request->route('band');
        
        if (!$band instanceof Band) {
            return $next($request);
        }

        // Skip checks for subscription-related routes to prevent redirect loops
        if ($this->isSubscriptionRoute($request)) {
            return $next($request);
        }

        // Log band status for debugging
        Log::info('Band access check', [
            'band_id' => $band->id,
            'subscription_status' => $band->subscription_status,
            'trial_ends_at' => $band->trial_ends_at,
            'route' => $request->route()->getName()
        ]);

        // Check if band has no active subscription and no trial
        if (!$band->hasActiveSubscription() && !$band->trial_ends_at) {
            return redirect()->route('bands.subscribe', $band)
                ->with('error', 'Subscription required to access this band.');
        }

        // Check if band is in trial but trial has expired
        if (!$band->hasActiveSubscription() && $band->trial_ends_at && now()->gt($band->trial_ends_at)) {
            return redirect()->route('bands.subscribe', $band)
                ->with('error', 'Trial period has expired. Please subscribe to continue.');
        }

        // If band is in read-only mode (expired subscription), restrict write operations
        if ($this->isWriteOperation($request) && !$band->canPerformWriteOperations()) {
            return redirect()->back()
                ->with('error', 'Band is in read-only mode. Please subscribe to make changes.');
        }

        return $next($request);
    }

    /**
     * Determine if the request is a write operation
     */
    private function isWriteOperation(Request $request): bool
    {
        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
    }

    /**
     * Check if the current route is subscription-related
     */
    private function isSubscriptionRoute(Request $request): bool
    {
        $routeName = $request->route()->getName();
        return str_contains($routeName, 'subscribe') || 
               str_contains($routeName, 'subscription') ||
               str_contains($routeName, 'checkout');
    }
} 