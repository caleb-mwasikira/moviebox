<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\NewContentAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendNewContentAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $title,
        public readonly string $type,
        public readonly string $url,
        public readonly ?string $genre = null,
        public readonly ?int $year = null,
    ) {}

    public function handle(): void
    {
        $notification = new NewContentAlert(
            title: $this->title,
            type:  $this->type,
            url:   $this->url,
            genre: $this->genre,
            year:  $this->year,
        );

        User::whereNotNull('email_verified_at')
            ->whereNull('banned_at')
            ->where('role', '!=', 'admin')
            ->chunkById(100, function ($users) use ($notification) {
                Notification::send($users, $notification);
            });
    }
}
