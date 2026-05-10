<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Movie extends Model
{
    protected $fillable = [
        'title', 'description', 'genre', 'year', 'duration',
        'thumbnail', 'video_path', 'streaming_path', 'trailer_path', 'status', 'featured',
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

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isFeatured(): bool
    {
        return (bool) $this->featured;
    }

    public function streamingUrl(): ?string
    {
        if (!$this->streaming_path) return null;
        $base = rtrim(env('STREAMING_SERVICE_URL', 'http://127.0.0.1:9090'), '/');
        return $base . $this->streaming_path;
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

    public function trailerUrl(): string
    {
        if (str_starts_with($this->trailer_path, 'http')) {
            return $this->trailer_path;
        }
        return asset('storage/' . $this->trailer_path);
    }
}
