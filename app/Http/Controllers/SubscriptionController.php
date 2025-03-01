<?php

namespace App\Http\Controllers;

use App\Models\Band;
use App\Services\StripeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Cashier\Exceptions\IncompletePayment;

class SubscriptionController extends Controller
{
    /**
     * Show the subscription expired page
     */
    public function expired(): Response
    {
        return Inertia::render('Subscription/Expired');
    }

    /**
     * Show the checkout page for a specific band
     */
    public function checkout(Band $band): Response|RedirectResponse
    {
        try {
            $user = auth()->user();

            if (!$band->isAdmin($user)) {
                return redirect()->route('dashboard')
                    ->with('error', 'You do not have permission to subscribe this band.');
            }

            // Create or get Stripe customer
            if (!$user->stripe_id) {
                $stripeCustomer = $user->createAsStripeCustomer();
                ray('Created customer:', $stripeCustomer->toArray());
            }

            // Create setup intent
            $intent = $user->createSetupIntent();
            ray('Created setup intent:', $intent->toArray());

            return Inertia::render('Subscription/Checkout', [
                'band' => $band->only(['id', 'name', 'created_at']),
                'stripeKey' => config('cashier.key'),
                'clientSecret' => $intent->client_secret,
            ]);

        } catch (\Exception $e) {
            ray('Error in checkout:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Unable to initialize checkout. Please try again.');
        }
    }

    /**
     * Create a new subscription
     */
    public function createSubscription(Request $request): JsonResponse
    {
        ray('Subscription creation request:', $request->all());

        $request->validate([
            'band_id' => 'required|exists:bands,id',
            'payment_method' => 'required|string',
        ]);

        try {
            $user = auth()->user();
            $band = Band::findOrFail($request->band_id);

            ray('User and Band:', [
                'user_id' => $user->id,
                'band_id' => $band->id,
                'stripe_id' => $user->stripe_id
            ]);

            if (!$band->isAdmin($user)) {
                return response()->json([
                    'message' => 'You are not authorized to manage this band\'s subscription.'
                ], 403);
            }

            // Ensure customer exists and update payment method
            if (!$user->stripe_id) {
                ray('Creating new customer for subscription');
                $user->createAsStripeCustomer();
            }

            ray('Updating payment method');
            $user->updateDefaultPaymentMethod($request->payment_method);

            // Create subscription using StripeService
            $stripeService = app(StripeService::class);
            $result = $stripeService->createSubscription($band, $user, [
                'payment_method' => $request->payment_method,
            ]);

            ray('Subscription created:', $result);

            return response()->json([
                'status' => 'success',
                'subscription' => $result,
            ]);

        } catch (\Laravel\Cashier\Exceptions\IncompletePayment $exception) {
            ray('Incomplete payment exception:', $exception->getMessage());
            return response()->json([
                'status' => 'requires_action',
                'payment_intent_client_secret' => $exception->payment->clientSecret(),
            ]);
        } catch (\Exception $e) {
            ray('Subscription creation error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Unable to process subscription. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display subscription management page
     * Shows all bands that the user is an admin of, along with their subscription status
     */
    public function index(): Response
    {
        $user = auth()->user();
        
        // Get all bands where user is admin, with their subscriptions
        $bands = $user->adminBands()
            ->with(['subscription.user'])
            ->get()
            ->map(function ($band) use ($user) {
                $subscription = $band->subscription;
                return [
                    'id' => $band->id,
                    'name' => $band->name,
                    'created_at' => $band->created_at,
                    'subscription' => $subscription ? [
                        'id' => $subscription->id,
                        'status' => $subscription->stripe_status,
                        'price' => config('subscription.monthly_price'), // Fixed price for now
                        'ends_at' => $subscription->ends_at,
                        'trial_ends_at' => $subscription->trial_ends_at,
                        'is_owner' => $subscription->user_id === $user->id,
                        'owner_name' => $subscription->user->name
                    ] : null
                ];
            });

        return Inertia::render('Subscriptions/Index', [
            'subscriptions' => $bands
        ]);
    }

    /**
     * Cancel a subscription for a band
     */
    public function cancel(Band $band): RedirectResponse
    {
        try {
            $user = auth()->user();

            if (!$band->isAdmin($user)) {
                return redirect()->back()
                    ->with('error', 'You do not have permission to cancel this subscription.');
            }

            $subscription = $band->subscription;
            if (!$subscription) {
                return redirect()->back()
                    ->with('error', 'No active subscription found.');
            }

            // Cancel the subscription at period end
            $stripeSubscription = $user->subscription("band_{$band->id}");
            if ($stripeSubscription) {
                $stripeSubscription->cancelAt(Carbon::now()->addMonths(1));
            }

            // Update local subscription record
            $subscription->update([
                'ends_at' => Carbon::now()->addMonths(1),
                'stripe_status' => 'cancelled'
            ]);

            // Update band subscription status
            $band->update([
                'subscription_status' => 'cancelled'
            ]);

            return redirect()->back()
                ->with('success', 'Your subscription has been cancelled and will end at the end of the billing period.');

        } catch (Exception $e) {
            ray('Subscription cancellation failed', [
                'error' => $e->getMessage(),
                'band_id' => $band->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->back()
                ->with('error', 'Unable to cancel subscription. Please try again or contact support.');
        }
    }

    /**
     * Update payment method
     */
    public function updatePaymentMethod(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        try {
            $user = auth()->user();
            $user->updateDefaultPaymentMethod($request->payment_method);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment method updated successfully.',
            ]);

        } catch (Exception $e) {
            ray('Payment method update failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'message' => 'Unable to update payment method. Please try again.'
            ], 500);
        }
    }
}
