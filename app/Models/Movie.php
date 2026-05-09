<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Movie extends Model
{
    protected $fillable = [
        'title', 'description', 'genre', 'year', 'duration',
        'thumbnail', 'video_path', 'status',
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

    public function videoIsReady(): bool
    {
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
}
