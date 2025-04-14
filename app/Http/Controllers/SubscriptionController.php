<?php

namespace App\Http\Controllers;

use App\Models\Band;
use App\Services\PricingService;
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
    private PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

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
    public function checkout(Request $request, Band $band): Response|RedirectResponse
    {
        try {
            $user = $request->user();

            if (!$band->isAdmin($user)) {
                return redirect()->route('dashboard')
                    ->with('error', 'You do not have permission to subscribe this band.');
            }

            // Get pricing based on user's location
            $countryCode = $user->country_code ?? session('country_code');
            $pricing = $this->pricingService->getPricing($countryCode);

            // Get the appropriate Stripe Price ID based on currency
            $priceId = config("subscription.stripe_prices.{$pricing['currency']}");

            // Create or get Stripe customer
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer();
            }

            // Create setup intent
            $intent = $user->createSetupIntent();

            return Inertia::render('Subscription/Checkout', [
                'band' => $band->only(['id', 'name', 'created_at']),
                'trialDaysLeft' => $user->getRemainingTrialDays(),
                'stripeKey' => config('cashier.key'),
                'clientSecret' => $intent->client_secret,
                'pricing' => $pricing,
                'formattedPrice' => $this->pricingService->formatPrice($pricing),
                'priceId' => $priceId
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
            'price_id' => 'required|string'
        ]);

        try {
            $user = auth()->user();
            $band = Band::findOrFail($request->band_id);

            if (!$band->isAdmin($user)) {
                return response()->json([
                    'message' => 'You are not authorized to manage this band\'s subscription.'
                ], 403);
            }

            // Get pricing based on user's location
            $countryCode = $user->country_code ?? session('country_code');
            $pricing = app(PricingService::class)->getPricing($countryCode);

            // Update payment method
            $user->updateDefaultPaymentMethod($request->payment_method);

            // Create subscription
            $subscription = $user->subscribeBand($band, $request->price_id, [
                'currency' => $pricing['currency'],
                'amount' => $pricing['amount']
            ]);

            return response()->json([
                'status' => 'success',
                'subscription' => $subscription
            ]);

        } catch (IncompletePayment $exception) {
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
        
        // Get user's pricing based on location
        $countryCode = $user->country_code ?? session('country_code');
        $pricing = $this->pricingService->getPricing($countryCode);

        // Get all bands where user is admin, with their subscriptions
        $bands = $user->adminBands()
            ->with(['subscription.user'])
            ->get()
            ->map(function ($band) use ($user, $pricing) {
                $subscription = $band->subscription;
                return [
                    'id' => $band->id,
                    'name' => $band->name,
                    'created_at' => $band->created_at,
                    'subscription' => $subscription ? [
                        'id' => $subscription->id,
                        'status' => $subscription->stripe_status,
                        'price' => $pricing['amount'],
                        'currency' => $pricing['currency'],
                        'symbol' => $pricing['symbol'],
                        'formatted_price' => $this->pricingService->formatPrice($pricing),
                        'ends_at' => $subscription->ends_at,
                        'trial_ends_at' => $subscription->trial_ends_at,
                        'is_owner' => $subscription->user_id === $user->id,
                        'owner_name' => $subscription->user->name
                    ] : null
                ];
            });

        return Inertia::render('Subscriptions/Index', [
            'subscriptions' => $bands,
            'pricing' => $pricing,
            'formattedPrice' => $this->pricingService->formatPrice($pricing)
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

            // Get the subscription
            $subscription = $user->getBandSubscription($band);

            if (!$subscription) {
                return redirect()->back()
                    ->with('error', 'No active subscription found.');
            }

            // Cancel the subscription at period end
            $service = new StripeService();
            $service->cancelSubscription($band);

            // Calculate end date (1 month from now)
            $endDate = Carbon::now()->addMonths(1);

            // Update subscription record
            $subscription->update([
                'ends_at' => $endDate,
                'stripe_status' => 'cancelled'
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

    public function subscribe(Request $request, Band $band)
    {
        $user = $request->user();
        $countryCode = $user->country_code ?? session('country_code');
        $pricing = $this->pricingService->getPricing($countryCode);
        
        // Get the appropriate Stripe Price ID based on currency
        $priceId = config("subscription.stripe_prices.{$pricing['currency']}");

        try {
            // Create the subscription
            $subscription = $user->subscribeBand($band, $priceId, [
                'currency' => $pricing['currency'],
                'amount' => $pricing['amount']
            ]);

            return redirect()->route('dashboard')->with('success', 'Subscription created successfully!');
        } catch (IncompletePayment $exception) {
            return redirect()->route('cashier.payment', [
                $exception->payment->id,
                'redirect' => route('dashboard')
            ]);
        }
    }
}
