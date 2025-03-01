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

        $user = $request->user();

        // Get any subscription (including incomplete ones)
        $subscription = $band->subscriptions()
            ->whereNull('ends_at')
            ->where(function($query) {
                $query->where('stripe_status', 'active')
                      ->orWhere('stripe_status', 'incomplete');
            })
            ->latest()
            ->first();

        // Detailed debugging
        ray('Band Access Control Debug', [
            'band_id' => $band->id,
            'route' => $request->route()->getName(),
            'subscription_exists' => $subscription ? true : false,
            'subscription_details' => $subscription ? [
                'id' => $subscription->id,
                'stripe_status' => $subscription->stripe_status,
                'ends_at' => $subscription->ends_at,
                'is_active' => $subscription ? $subscription->isActive() : false
            ] : null,
            'band_has_active_subscription' => $band->hasActiveSubscription(),
            'can_perform_write_ops' => $band->canPerformWriteOperations()
        ]);

        // If no subscription at all, redirect to checkout
        if (!$subscription) {
            ray('No subscription found - redirecting to checkout');
            return redirect()->route('subscription.checkout', $band)
                ->with('error', 'Subscription required to access this band.');
        }

        // If subscription exists but is not active or incomplete, allow access
        // This ensures users can complete their subscription process
        if ($subscription->stripe_status === 'incomplete') {
            ray('Incomplete subscription found - allowing access');
            return $next($request);
        }

        // If subscription exists but is not active (e.g., cancelled, expired)
        if (!$subscription->isActive()) {
            ray('Subscription exists but not active', [
                'stripe_status' => $subscription->stripe_status,
                'ends_at' => $subscription->ends_at
            ]);
            return redirect()->route('subscription.checkout', $band)
                ->with('error', 'Your subscription has expired. Please renew to continue.');
        }

        // If band is in read-only mode (expired subscription), restrict write operations
        if ($this->isWriteOperation($request) && !$band->canPerformWriteOperations()) {
            ray('Write operation blocked - band in read-only mode');
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
