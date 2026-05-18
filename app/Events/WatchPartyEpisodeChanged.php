<?php

namespace App\Events;

use App\Models\Episode;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WatchPartyEpisodeChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string  $token,
        public readonly Episode $episode,
    ) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel('watch-party.' . $this->token)];
    }

    public function broadcastAs(): string
    {
        return 'WatchPartyEpisodeChanged';
    }

    public function broadcastWith(): array
    {
        return [
            'current_episode_id' => $this->episode->id,
            'episode' => [
                'id'      => $this->episode->id,
                'label'   => $this->episode->label(),
                'src'     => $this->episode->streamingUrl() ?? $this->episode->videoUrl(),
                'use_hls' => (bool) $this->episode->streaming_path,
            ],
        ];
    }
}
