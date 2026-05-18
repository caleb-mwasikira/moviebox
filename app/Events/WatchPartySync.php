<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WatchPartySync implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $token,
        public readonly float $position,
        public readonly bool $isPlaying,
    ) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel('watch-party.' . $this->token)];
    }

    public function broadcastAs(): string
    {
        return 'WatchPartySync';
    }

    public function broadcastWith(): array
    {
        return [
            'position'   => $this->position,
            'is_playing' => $this->isPlaying,
        ];
    }
}
