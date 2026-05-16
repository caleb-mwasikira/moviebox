<?php

namespace App\Console\Commands;

use App\Models\UserSubscriptions;
use App\Notifications\SubscriptionExpiryReminder;
use Illuminate\Console\Command;

class SendSubscriptionReminders extends Command
{
    protected $signature   = 'subscriptions:remind';
    protected $description = 'Send expiry reminder emails for subscriptions expiring within 3 days';

    public function handle(): void
    {
        $expiring = UserSubscriptions::with(['user', 'package'])
            ->where('status', 'active')
            ->whereBetween('expires_at', [now()->startOfDay(), now()->addDays(3)->endOfDay()])
            ->get();

        foreach ($expiring as $subscription) {
            $subscription->user?->notify(new SubscriptionExpiryReminder($subscription));
        }

        $this->info("Sent {$expiring->count()} subscription reminder(s).");
    }
}
