<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeriesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'          => $this->uuid,
            'title'         => $this->title,
            'description'   => $this->description,
            'genre'         => $this->genre,
            'year'          => $this->year,
            'featured'      => (bool) $this->featured,
            'coming_soon'   => (bool) $this->coming_soon,
            'thumbnail_url' => $this->thumbnailUrl(),
            'trailer_url'   => $this->trailerIsReady()
                                    ? (str_starts_with($this->trailer_path, 'http') ? $this->trailer_path : '/' . ltrim($this->trailer_path, '/'))
                                    : null,
            'seasons'       => SeasonResource::collection($this->whenLoaded('seasons')),
            'created_at'    => $this->created_at?->toISOString(),
        ];
    }
}
