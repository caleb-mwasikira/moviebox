<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationOtp extends Notification
{
    public function __construct(public readonly string $otp) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify your email — ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Use the code below to verify your email address. It expires in **15 minutes**.')
            ->line('# ' . $this->otp)
            ->line('If you did not create an account, no further action is required.')
            ->salutation('— ' . config('app.name'));
    }
}
