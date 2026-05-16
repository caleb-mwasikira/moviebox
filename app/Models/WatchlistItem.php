<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WatchlistItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'watchable_type', 'watchable_id', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    public function watchable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
