<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetOtp extends Notification
{
    public function __construct(private readonly string $otp) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Password Reset Code — ' . config('app.name'))
            ->greeting('Hello!')
            ->line('Use the code below to reset your password. It expires in **15 minutes**.')
            ->line('## ' . $this->otp)
            ->line('If you did not request a password reset, no action is needed.');
    }
}
