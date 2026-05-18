<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchPartyMessage extends Model
{
    protected $fillable = ['watch_party_id', 'user_id', 'message', 'is_system'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(WatchParty::class, 'watch_party_id');
    }
}
