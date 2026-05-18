<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchPartyMember extends Model
{
    protected $fillable = [
        'watch_party_id',
        'user_id',
        'joined_at',
        'last_seen_at',
        'kicked_at',
    ];

    protected $casts = [
        'joined_at'    => 'datetime',
        'last_seen_at' => 'datetime',
        'kicked_at'    => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(WatchParty::class, 'watch_party_id');
    }
}
