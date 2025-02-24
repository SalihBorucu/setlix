<?php

namespace App\Http\Controllers;

use App\Models\Band;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\SubscriptionWebhookHandler;
use App\Services\SubscriptionManager;

class SubscriptionController extends Controller
{
    protected $stripeService;
    protected $subscriptionManager;
    protected $webhookHandler;

    public function __construct(
        StripeService $stripeService,
        SubscriptionManager $subscriptionManager,
        SubscriptionWebhookHandler $webhookHandler
    ) {
        $this->stripeService = $stripeService;
        $this->subscriptionManager = $subscriptionManager;
        $this->webhookHandler = $webhookHandler;
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
     *
     * @param Band $band The band being subscribed
     * @return RedirectResponse|Response
     */
    public function checkout(Band $band): Response|RedirectResponse
    {
        $user = auth()->user();

        if (!$band->isAdmin($user)) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to subscribe this band.');
        }

        $trialDaysLeft = $this->subscriptionManager->calculateTrialDays($band);

        if ($user->is_subscribed) {
            $trialDaysLeft = 0;
        }

        return Inertia::render('Subscription/Checkout', [
            'band' => $band->only(['id', 'name', 'created_at']),
            'trialDaysLeft' => $trialDaysLeft,
            'stripeKey' => config('services.stripe.key')
        ]);
    }

    /**
     * Handle the successful subscription process for a band
     */
    public function process(Request $request): RedirectResponse
    {
        $request->validate([
            'band_id' => 'required|exists:bands,id',
            'payment_intent' => 'required|string',
        ]);

        try {
            $user = auth()->user();
            $band = Band::findOrFail($request->band_id);

            if (!$band->isAdmin($user)) {
                return back()->with('error', 'Unauthorized');
            }

            $this->subscriptionManager->processSubscription($band, $user, $request->payment_intent);

            return redirect()
                ->route('bands.show', $band)
                ->with('success', "Subscription activated for {$band->name}!");

        } catch (\Exception $e) {
            Log::error('Subscription processing failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Unable to process subscription. Please try again.');
        }
    }

    public function createSubscription(Request $request): JsonResponse
    {
        $request->validate([
            'band_id' => 'required|exists:bands,id',
            'card_name' => 'required|string|max:255',
        ]);

        try {
            $user = auth()->user();
            $band = Band::findOrFail($request->band_id);

            if (!$band->isAdmin($user)) {
                return response()->json([
                    'message' => 'You are not authorized to manage this band\'s subscription.'
                ], 403);
            }

            $result = $this->stripeService->createSubscription(
                $band,
                $user,
                $request->all()
            );

            return response()->json([
                'clientSecret' => $result['clientSecret'],
                'subscriptionId' => $result['subscriptionId']
            ]);

        } catch (\Exception $e) {
            Log::error('Subscription creation failed', [
                'error' => $e->getMessage(),
                'band_id' => $request->band_id
            ]);

            return response()->json([
                'message' => 'Unable to process subscription. Please try again.'
            ], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        try {
            $result = $this->webhookHandler->handleWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature')
            );

            return response()->json(['status' => 'success', 'result' => $result]);
        } catch (\Exception $e) {
            Log::error('Webhook error', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function index()
    {
        $user = auth()->user();

        // Get only bands where the user is an admin
        $subscriptions = $user->adminBands()
            ->get()
            ->map(function ($band) {
                // Get subscription status based on trial and subscription state
                $status = match($band->subscription_status) {
                    'active' => 'active',
                    'payment_failed' => 'payment_failed',
                    'cancelled' => 'cancelled',
                    default => $band->trial_ends_at && Carbon::now()->lt($band->trial_ends_at) 
                        ? 'trialing' 
                        : 'expired'
                };

                return [
                    'id' => $band->id,
                    'band' => [
                        'id' => $band->id,
                        'name' => $band->name,
                    ],
                    'status' => $status,
                    'price' => $status === 'active' ? 10.00 : 0, // $10/month for active subscriptions
                ];
            });

        // Calculate total monthly fee
        $totalMonthlyFee = $subscriptions
            ->where('status', 'active')
            ->sum('price');

        return Inertia::render('Subscriptions/Index', [
            'subscriptions' => $subscriptions,
            'totalMonthlyFee' => $totalMonthlyFee,
        ]);
    }

    public function cancel($id)
    {
        $band = Band::findOrFail($id);

        // Ensure user has permission to cancel this subscription
        $this->authorize('update', $band);

        // Cancel the subscription using the subscription manager
        $this->subscriptionManager->cancelSubscription($band);

        return redirect()->back()->with('success', 'Subscription cancelled successfully');
    }
}
