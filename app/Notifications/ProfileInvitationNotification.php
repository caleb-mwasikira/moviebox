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

    public function toMail(object $_notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->inviterName . ' invited you to ' . config('app.name'))
            ->view('emails.account.invite', [
                'inviterName' => $this->inviterName,
                'acceptUrl'   => $this->acceptUrl,
            ]);
    }
}
