<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . '!')
            ->greeting('Welcome, ' . $notifiable->name . '!')
            ->line('Your email has been verified and your account is ready.')
            ->line('Start exploring movies and series available on our platform.')
            ->action('Browse Content', route('home'))
            ->salutation('— ' . config('app.name'));
    }
}
