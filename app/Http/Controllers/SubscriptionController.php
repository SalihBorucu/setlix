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
     * Show the checkout page for the user's band
     */
    public function checkout(): Response
    {
        $user = auth()->user();
        $band = $user->bands()->first();

        if (!$band) {
            return redirect()->route('bands.create')
                ->with('error', 'Please create a band first.');
        }

        return Inertia::render('Subscription/Checkout', [
            'band' => [
                'id' => $band->id,
                'name' => $band->name,
                'created_at' => $band->created_at,
            ],
            'trialDaysLeft' => $this->subscriptionManager->calculateTrialDays($band),
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
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $result = $this->stripeService->createSubscription(
                $band,
                $user,
                $request->all()
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Unable to process subscription. Please try again.'],
                500
            );
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
} 