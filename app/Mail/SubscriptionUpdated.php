<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserSubscriptions;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly UserSubscriptions $subscription,
        public readonly string $action, // 'renewed' | 'extended'
        public readonly ?int $days = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = match ($this->action) {
            'extended' => 'Your subscription has been extended',
            default    => 'Your subscription has been renewed',
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.subscription.updated');
    }
}
