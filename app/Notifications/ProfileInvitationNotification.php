<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileInvitationNotification extends Notification
{
    public function __construct(
        public readonly string $inviterName,
        public readonly string $acceptUrl,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->inviterName . ' invited you to ' . config('app.name'))
            ->greeting('You\'ve been invited!')
            ->line('**' . $this->inviterName . '** has added you as a profile member on their ' . config('app.name') . ' account.')
            ->line('Click the button below to set up your profile. You\'ll need to log in or create an account first.')
            ->action('Accept Invitation', $this->acceptUrl)
            ->line('This invitation link expires in 7 days. If you did not expect this email, you can safely ignore it.')
            ->salutation('— ' . config('app.name'));
    }
}
