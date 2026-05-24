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
            'thumbnail_url' => $this->thumbnailUrl(),
            'seasons'       => SeasonResource::collection($this->whenLoaded('seasons')),
            'created_at'    => $this->created_at?->toISOString(),
        ];
    }
}
