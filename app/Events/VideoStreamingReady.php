<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoStreamingReady implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int     $userId,
        public readonly string  $label,
        public readonly string  $editUrl,
        public readonly string  $filmType = '',
        public readonly int     $modelId  = 0,
        public readonly ?int    $seriesId = null,
        public readonly string  $streamingPath = '',
    ) {}

    public function broadcastOn(): array
    {
        $channels = [new PrivateChannel('admin-notifications.' . $this->userId)];

        if ($this->filmType && $this->modelId) {
            $channels[] = new Channel('film-stream.' . $this->filmType . '.' . $this->modelId);
        }

        return $channels;
    }

    public function broadcastWith(): array
    {
        return [
            'label'         => $this->label,
            'editUrl'       => $this->editUrl,
            'filmType'      => $this->filmType,
            'modelId'       => $this->modelId,
            'seriesId'      => $this->seriesId,
            'streamingPath' => $this->streamingPath,
        ];
    }
}
