<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftSubscription extends Model
{
    protected $fillable = [
        'sender_id',
        'subscription_package_id',
        'recipient_email',
        'token',
        'message',
        'status',
        'claimed_by_id',
        'claimed_at',
        'expires_at',
    ];

    protected $casts = [
        'claimed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPackage::class, 'subscription_package_id');
    }

    public function claimedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimed_by_id');
    }

    public function isClaimed(): bool
    {
        return $this->status === 'claimed';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isClaimable(): bool
    {
        return $this->status === 'sent' && ! $this->isExpired();
    }
}
