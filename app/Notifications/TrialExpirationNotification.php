<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TrialExpirationNotification extends Notification
{
    protected $daysRemaining;

    public function __construct(int $daysRemaining)
    {
        $this->daysRemaining = $daysRemaining;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Your Setlix Trial is Ending Soon")
            ->line("Your free trial will expire in {$this->daysRemaining} days.")
            ->line("Subscribe now to keep access to all your band's data and features.")
            ->action('Subscribe Now', route('subscription.checkout'));
    }
} 