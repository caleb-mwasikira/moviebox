<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'           => $this->uuid,
            'episode_number' => $this->episode_number,
            'title'          => $this->title,
            'description'    => $this->description,
            'duration'       => $this->duration,
            'is_free'        => $this->is_free,
            'has_stream'     => (bool) $this->streaming_path,
            'streaming_url'  => $this->streaming_path ? ('/' . ltrim($this->streaming_path, '/')) : null,
        ];
    }
}
