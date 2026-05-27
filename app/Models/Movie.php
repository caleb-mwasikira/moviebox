<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Movie extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'genre',
        'year',
        'duration',
        'thumbnail',
        'video_path',
        'streaming_path',
        'trailer_path',
        'status',
        'featured',
        'coming_soon',
        'is_free',
    ];

    protected $casts = [
        'is_free' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Movie $movie) {
            $movie->uuid = (string) Str::uuid();
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

    public function subtitles(): MorphMany
    {
        return $this->morphMany(Subtitle::class, 'subtitleable')->orderBy('language');
    }

    public function collectionItems(): MorphMany
    {
        return $this->morphMany(CollectionItem::class, 'collectible');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isComingSoon(): bool
    {
        return (bool) $this->coming_soon;
    }

    public function isFeatured(): bool
    {
        return (bool) $this->featured;
    }

    public function videoIsReady(): bool
    {
        if ($this->streaming_path) return true;
        return (bool) $this->video_path && !str_starts_with($this->video_path, 'videos/staging/');
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    public function videoUrl(): string
    {
        return asset('storage/' . $this->video_path);
    }

    public function trailerIsReady(): bool
    {
        return (bool) $this->trailer_path && !str_starts_with($this->trailer_path, 'videos/staging/');
    }

    public function streamingUrl(): ?string
    {
        if (!$this->streaming_path) {
            return null;
        }
        // Use the env variable as the base, defaulting to local if not set
        $baseUrl = rtrim(env('STREAMING_SERVICE_URL', 'http://127.0.0.1:9090'), '/');
        // Ensure we don't have double slashes when joining
        $path = ltrim($this->streaming_path, '/');
        return "{$baseUrl}/{$path}";
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
