<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoEncodingProgress implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int    $userId,
        public readonly string $filmType,
        public readonly int    $modelId,
        public readonly int    $percentage,
        public readonly ?int   $seriesId = null,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin-notifications.' . $this->userId)];
    }

    public function broadcastWith(): array
    {
        return [
            'filmType'   => $this->filmType,
            'modelId'    => $this->modelId,
            'percentage' => $this->percentage,
            'seriesId'   => $this->seriesId,
        ];
    }
}
