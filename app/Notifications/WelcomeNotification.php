<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    public function __construct(private readonly ?User $user = null) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . '!')
            ->view('emails.account.welcome', ['user' => $this->user ?? $notifiable]);
    }
}
