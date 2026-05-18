<?php

namespace App\Mail;

use App\Models\GiftSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GiftReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly GiftSubscription $gift,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'You\'ve received a gift subscription — ' . config('app.name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.gift.received');
    }
}
