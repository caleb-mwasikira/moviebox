<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;


class WatchParty extends Model
{
    protected $fillable = [
        'host_id',
        'watchable_type',
        'watchable_id',
        'token',
        'starts_at',
        'current_episode_id',
        'status',
        'is_playing',
        'playback_position',
        'playback_updated_at',
        'ended_at',
    ];

    protected $casts = [
        'is_playing'          => 'boolean',
        'playback_position'   => 'float',
        'starts_at'           => 'datetime',
        'playback_updated_at' => 'datetime',
        'ended_at'            => 'datetime',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function watchable(): MorphTo
    {
        return $this->morphTo();
    }

    public function members(): HasMany
    {
        return $this->hasMany(WatchPartyMember::class);
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(WatchPartyEpisode::class)->orderBy('sort_order');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WatchPartyMessage::class)->orderBy('id');
    }

    public function currentEpisode(): BelongsTo
    {
        return $this->belongsTo(Episode::class, 'current_episode_id');
    }

    public function isEnded(): bool
    {
        return $this->status === 'ended';
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function isInCountdown(): bool
    {
        return $this->starts_at !== null && $this->starts_at->isFuture();
    }

    /** Members seen in the last 15 seconds are considered online. */
    public function onlineMembers()
    {
        return $this->members()->where('last_seen_at', '>=', now()->subSeconds(15));
    }

    public function title(): string
    {
        if (! $this->watchable) {
            return 'Unknown';
        }

        if ($this->watchable instanceof Episode) {
            return $this->watchable->season->series->title . ' · ' . $this->watchable->label();
        }

        return $this->watchable->title;
    }
}
