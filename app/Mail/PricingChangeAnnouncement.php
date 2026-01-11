<?php

namespace App\Mail;

use App\Models\User;
use App\Services\PricingService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class PricingChangeAnnouncement extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Big News: Simpler Pricing, Better Value for Your Band",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $pricingService = app(PricingService::class);
        $pricing = $pricingService->getPricing($this->user->country_code);

        // Old monthly prices by currency
        $oldMonthlyPrices = [
            'GBP' => 10,
            'USD' => 15,
            'EUR' => 12,
        ];
        $oldMonthlyPrice = $oldMonthlyPrices[$pricing['currency']] ?? 15;

        // Generate signed unsubscribe URL (valid for 30 days)
        $unsubscribeUrl = URL::signedRoute('unsubscribe', ['user' => $this->user->id], now()->addDays(30));

        return new Content(
            view: 'emails.pricing-change-announcement',
            with: [
                'userName' => $this->user->name,
                'loginUrl' => route('login'),
                'price' => $pricing['amount'],
                'originalPrice' => $pricing['amount'] * 2,
                'symbol' => $pricing['symbol'],
                'currency' => $pricing['currency'],
                'oldMonthlyPrice' => $oldMonthlyPrice,
                'unsubscribeUrl' => $unsubscribeUrl,
            ],
        );
    }
}
