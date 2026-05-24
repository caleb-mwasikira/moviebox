<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Episode;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Series extends Model
{
    protected $fillable = [
        'title', 'description', 'genre', 'year',
        'thumbnail', 'trailer_path', 'status', 'featured', 'coming_soon',
    ];

    protected static function booted(): void
    {
        static::creating(function (Series $series) {
            $series->uuid = (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function posters(): MorphMany
    {
        return $this->morphMany(Poster::class, 'posterable')->orderBy('sort_order');
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class)->orderBy('season_number');
    }

    public function collectionItems(): MorphMany
    {
        return $this->morphMany(CollectionItem::class, 'collectible');
    }

    public function episodes(): HasManyThrough
    {
        return $this->hasManyThrough(Episode::class, Season::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isFeatured(): bool
    {
        return (bool) $this->featured;
    }

    public function isComingSoon(): bool
    {
        return (bool) $this->coming_soon;
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    public function trailerIsReady(): bool
    {
        return (bool) $this->trailer_path && !str_starts_with($this->trailer_path, 'videos/staging/');
    }

    public function trailerUrl(): string
    {
        if (!$this->trailer_path) {
            return '';
        }
        if (str_starts_with($this->trailer_path, 'http')) {
            return $this->trailer_path;
        }
        $baseUrl = rtrim(env('STREAMING_SERVICE_URL', 'http://127.0.0.1:9090'), '/');
        return $baseUrl . '/' . ltrim($this->trailer_path, '/');
    }
}
