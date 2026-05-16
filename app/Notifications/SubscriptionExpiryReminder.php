<?php

namespace App\Notifications;

use App\Models\UserSubscriptions;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class SubscriptionExpiryReminder extends Notification
{
    public function __construct(public readonly UserSubscriptions $subscription) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $days    = (int) now()->startOfDay()->diffInDays($this->subscription->expires_at->startOfDay());
        $when    = $days === 0 ? 'today' : 'in ' . $days . ' ' . Str::plural('day', $days);
        $package = $this->subscription->package;

        return (new MailMessage)
            ->subject('Your ' . $package->name . ' subscription expires ' . $when)
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('Your **' . $package->name . '** subscription expires on **' . $this->subscription->expires_at->format('M j, Y') . '**.')
            ->line('Renew now to keep enjoying uninterrupted access to all our content.')
            ->action('Manage Subscription', route('account.billing'))
            ->salutation('— ' . config('app.name'));
    }
}
