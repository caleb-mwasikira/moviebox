<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'          => $this->uuid,
            'title'         => $this->title,
            'description'   => $this->description,
            'genre'         => $this->genre,
            'year'          => $this->year,
            'duration'      => $this->duration,
            'is_free'       => (bool) $this->is_free,
            'featured'      => (bool) $this->featured,
            'thumbnail_url' => $this->thumbnailUrl(),
            'has_stream'    => (bool) $this->streaming_path,
            'created_at'    => $this->created_at?->toISOString(),
        ];
    }
}
