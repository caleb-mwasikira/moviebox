<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\UserSubscriptions;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class SubscriptionExpiryReminder extends Notification
{
    public function __construct(
        public readonly UserSubscriptions $subscription,
        private readonly ?User $user = null,
    ) {}

    public function via(object $_notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $user     = $this->user ?? $notifiable;
        $daysLeft = (int) now()->startOfDay()->diffInDays($this->subscription->expires_at->startOfDay());
        $when     = $daysLeft === 0 ? 'today' : "in {$daysLeft} " . Str::plural('day', $daysLeft);

        return (new MailMessage)
            ->subject("Your {$this->subscription->package?->name} subscription expires {$when}")
            ->view('emails.subscription.expiry-reminder', [
                'user'         => $user,
                'subscription' => $this->subscription,
                'daysLeft'     => $daysLeft,
            ]);
    }
}
