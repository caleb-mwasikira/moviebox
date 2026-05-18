<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchPartyEpisode extends Model
{
    protected $fillable = ['watch_party_id', 'episode_id', 'sort_order'];

    public function episode(): BelongsTo
    {
        return $this->belongsTo(Episode::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(WatchParty::class, 'watch_party_id');
    }
}
