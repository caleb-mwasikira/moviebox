<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Collection $collection) {
            if (empty($collection->slug)) {
                $collection->slug = Str::slug($collection->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function items(): HasMany
    {
        return $this->hasMany(CollectionItem::class)->orderBy('sort_order');
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    public function movies()
    {
        return $this->items()->where('collectible_type', Movie::class)->with('collectible');
    }

    public function series()
    {
        return $this->items()->where('collectible_type', Series::class)->with('collectible');
    }
}
