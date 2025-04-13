<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Band;
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

        // Allow access during trial period
        if ($request->user()->is_trial) {
            return $next($request);
        }

        // Get active subscription
        $subscription = $band->subscription;

        // If no active subscription, redirect to checkout
        if (!$subscription) {
            return redirect()->route('subscription.checkout', $band)
                ->with('error', 'Subscription required to access this band.');
        }

        // If subscription is incomplete, allow access to complete the process
        if ($subscription->stripe_status === 'incomplete') {
            return $next($request);
        }

        // If subscription exists but is not active, redirect to checkout
        if (!$subscription->active()) {
            return redirect()->route('subscription.checkout', $band)
                ->with('error', 'Your subscription has expired. Please renew to continue.');
        }

        // If attempting write operation without active subscription or trial
        if ($this->isWriteOperation($request) && !$band->hasActiveSubscription()) {
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
