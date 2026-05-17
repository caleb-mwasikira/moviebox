<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SubscriptionPackage extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'currency',
        'price',
        'interval',
        'trial_period_days',
        'max_screens',
        'max_shared_links',
        'max_resolution',
        'has_ads',
        'download_enabled',
        'is_active',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::creating(function (SubscriptionPackage $subscription) {
            $subscription->uuid = (string) Str::uuid();
            $subscription->slug = (string) Str::slug($subscription->name);
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
