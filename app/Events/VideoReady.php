<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoReady implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $userId,
        public readonly string $label,
        public readonly string $editUrl,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin-notifications.' . $this->userId)];
    }

    public function broadcastWith(): array
    {
        return [
            'label'   => $this->label,
            'editUrl' => $this->editUrl,
        ];
    }
}
