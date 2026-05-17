<?php

namespace App\Mail;

use App\Models\MpesaPayment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly MpesaPayment $payment,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Payment confirmed — ' . config('app.name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment.confirmed');
    }
}
