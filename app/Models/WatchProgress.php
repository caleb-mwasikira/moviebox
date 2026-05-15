<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WatchProgress extends Model
{
    protected $table = 'watch_progress';

    protected $fillable = [
        'user_id',
        'watchable_type',
        'watchable_id',
        'position',
        'duration',
        'watched_at',
    ];

    protected $casts = [
        'position'   => 'float',
        'duration'   => 'float',
        'watched_at' => 'datetime',
    ];

    public function watchable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function percentComplete(): int
    {
        if (!$this->duration) return 0;
        return min(100, (int) round(($this->position / $this->duration) * 100));
    }
}
