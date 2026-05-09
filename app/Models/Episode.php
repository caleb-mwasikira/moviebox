<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    protected $fillable = [
        'season_id', 'episode_number', 'title',
        'description', 'video_path', 'duration',
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function videoUrl(): string
    {
        return asset('storage/' . $this->video_path);
    }

    public function videoIsReady(): bool
    {
        return (bool) $this->video_path && !str_starts_with($this->video_path, 'videos/staging/');
    }

    public function label(): string
    {
        return 'E' . str_pad($this->episode_number, 2, '0', STR_PAD_LEFT) . ' — ' . $this->title;
    }
}
