<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'               => $this->uuid,
            'name'               => $this->name,
            'description'        => $this->description,
            'currency'           => $this->currency,
            'price'              => $this->price,
            'interval'           => $this->interval,
            'trial_period_days'  => $this->trial_period_days,
            'max_screens'        => $this->max_screens,
            'max_resolution'     => $this->max_resolution,
            'has_ads'            => $this->has_ads,
            'download_enabled'   => $this->download_enabled,
            'watch_party_enabled' => $this->watch_party_enabled,
        ];
    }
}
