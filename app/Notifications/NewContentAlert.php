<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContentAlert extends Notification
{
    public function __construct(
        public readonly string $title,
        public readonly string $type,
        public readonly string $url,
        public readonly ?string $genre = null,
        public readonly ?int $year = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $typeLabel = ucfirst($this->type);
        $meta      = implode(' · ', array_filter([$this->genre, $this->year]));

        return (new MailMessage)
            ->subject('New ' . $typeLabel . ' — ' . $this->title . ' | ' . config('app.name'))
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('A new ' . $typeLabel . ' is now available on ' . config('app.name') . '.')
            ->line('**' . $this->title . '**' . ($meta ? '  ·  ' . $meta : ''))
            ->action('Watch Now', $this->url)
            ->salutation('— ' . config('app.name'));
    }
}
