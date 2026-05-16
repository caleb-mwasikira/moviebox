<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Episode extends Model
{
    protected $fillable = [
        'season_id', 'episode_number', 'title',
        'description', 'video_path', 'streaming_path', 'duration', 'is_free',
    ];

    protected $casts = [
        'is_free' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Episode $episode) {
            $episode->uuid = (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function videoUrl(): string
    {
        return asset('storage/' . $this->video_path);
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

    public function videoIsReady(): bool
    {
        if ($this->streaming_path) return true;
        return (bool) $this->video_path && !str_starts_with($this->video_path, 'videos/staging/');
    }

    public function label(): string
    {
        return 'E' . str_pad($this->episode_number, 2, '0', STR_PAD_LEFT) . ' — ' . $this->title;
    }
}
