<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeasonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'season_number' => $this->season_number,
            'title'         => $this->title,
            'episodes'      => EpisodeResource::collection($this->whenLoaded('episodes')),
        ];
    }
}
