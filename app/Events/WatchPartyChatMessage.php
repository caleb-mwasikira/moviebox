<?php

namespace App\Events;

use App\Models\WatchPartyMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WatchPartyChatMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string            $token,
        public readonly int               $hostId,
        public readonly WatchPartyMessage $message,
    ) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel('watch-party.' . $this->token)];
    }

    public function broadcastAs(): string
    {
        return 'WatchPartyChatMessage';
    }

    public function broadcastWith(): array
    {
        return [
            'id'        => $this->message->id,
            'user_id'   => $this->message->user_id,
            'name'      => $this->message->user->name,
            'message'   => $this->message->message,
            'is_host'   => $this->message->user_id === $this->hostId,
            'time'      => $this->message->created_at->format('H:i'),
            'is_system' => (bool) $this->message->is_system,
        ];
    }
}
