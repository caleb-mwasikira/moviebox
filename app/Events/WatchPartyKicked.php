<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WatchPartyKicked implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $token,
        public readonly int    $userId,
    ) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel('watch-party.' . $this->token)];
    }

    public function broadcastAs(): string
    {
        return 'WatchPartyKicked';
    }

    public function broadcastWith(): array
    {
        return ['user_id' => $this->userId];
    }
}
