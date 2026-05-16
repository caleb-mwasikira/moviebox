<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class Profile extends Model
{
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_PENDING  = 'pending';
    public const STATUS_DISABLED = 'disabled';

    public const COLORS = [
        '#6366f1', '#ec4899', '#f59e0b', '#10b981',
        '#0ea5e9', '#8b5cf6', '#f97316', '#14b8a6',
        '#ef4444', '#84cc16',
    ];

    protected $fillable = [
        'user_id', 'invited_user_id', 'name', 'avatar_color',
        'is_kids', 'pin', 'invite_email', 'invite_token', 'status',
    ];

    protected $hidden = ['pin', 'invite_token'];

    protected $casts = ['is_kids' => 'boolean'];

    // ── Relations ──────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }

    public function watchProgress(): HasMany
    {
        return $this->hasMany(WatchProgress::class);
    }

    public function watchlistItems(): HasMany
    {
        return $this->hasMany(WatchlistItem::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_PENDING]);
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function isOwnerProfile(): bool
    {
        // Owner profiles are auto-created (never via email invite), so invite_email is always null.
        // Invited profiles always carry an invite_email, even after acceptance.
        return $this->invite_email === null;
    }

    public function isPending(): bool  { return $this->status === self::STATUS_PENDING; }
    public function isActive(): bool   { return $this->status === self::STATUS_ACTIVE; }
    public function isDisabled(): bool { return $this->status === self::STATUS_DISABLED; }

    public function initials(): string
    {
        $words = array_filter(explode(' ', trim($this->name)));
        $chars = array_map(fn($w) => mb_substr($w, 0, 1), array_slice($words, 0, 2));
        return strtoupper(implode('', $chars));
    }

    public function hasPin(): bool
    {
        return $this->pin !== null;
    }

    public function checkPin(string $pin): bool
    {
        return Hash::check($pin, $this->pin);
    }

    public function displayName(): string
    {
        if ($this->isPending()) {
            return $this->invite_email ?? $this->name;
        }
        return $this->name;
    }
}
