<?php

namespace App\Http\Controllers;

use App\Models\Band;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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

        // Calculate trial days based on band creation date
        // Trial period is 14 days from band creation
        $bandCreatedAt = Carbon::parse($band->created_at);
        $trialEndsAt = $bandCreatedAt->copy()->addDays(14);
        $trialDaysLeft = now()->lt($trialEndsAt) 
            ? (int) now()->diffInDays($trialEndsAt)
            : 0;

        return Inertia::render('Subscription/Checkout', [
            'band' => [
                'id' => $band->id,
                'name' => $band->name,
                'created_at' => $band->created_at,
            ],
            'trialDaysLeft' => $trialDaysLeft,
            'stripeKey' => config('services.stripe.key')
        ]);
    }

    /**
     * Handle the subscription process for a specific band
     */
    public function process(): RedirectResponse
    {
        $bandId = request()->input('bandId');
        
        // Validate band ownership
        $band = Band::where('id', $bandId)
            ->whereHas('users', function ($query) {
                $query->where('users.id', auth()->id())
                    ->where('role', 'admin');
            })
            ->firstOrFail();

        Log::info('Subscription process initiated', [
            'user_id' => auth()->id(),
            'band_id' => $bandId,
            'band_name' => $band->name,
            'timestamp' => now()
        ]);

        // For now, we'll just redirect with a success message
        // Later this will integrate with Stripe or another payment processor
        
        return redirect()->route('dashboard')->with('success', 
            "Subscription process initiated for {$band->name}. Payment processing will be implemented soon."
        );
        
        // When implementing payment processing, it will look something like this:
        /*
        try {
            // Create payment intent/session with Stripe
            $session = Stripe::checkout()->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => config('services.stripe.price_id'),
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('subscription.success', ['band' => $band->id]),
                'cancel_url' => route('subscription.checkout'),
                'metadata' => [
                    'band_id' => $band->id,
                    'user_id' => auth()->id()
                ]
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Stripe checkout creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'band_id' => $band->id
            ]);
            return back()->with('error', 'Unable to initiate checkout. Please try again.');
        }
        */
    }
} 