<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'banned_at', 'avatar_path', 'social_provider', 'social_id', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'banned_at'         => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function isBanned(): bool
    {
        return $this->banned_at !== null;
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path
            ? asset('storage/' . $this->avatar_path)
            : null;
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function currentSubscription(): HasOne
    {
        return $this->hasOne(UserSubscriptions::class)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latestOfMany();
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscriptions::class);
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function activeProfile(): ?Profile
    {
        $id = session('active_profile_id');
        return $id ? $this->profiles()->find($id) : null;
    }

    public function maxProfiles(): int
    {
        return $this->currentSubscription?->package?->max_screens ?? 1;
    }

    public function canDownload(): bool
    {
        return (bool) $this->currentSubscription?->package?->download_enabled;
    }

    public function maxSharedLinks(): int
    {
        return (int) ($this->currentSubscription?->package?->max_shared_links ?? 5);
    }

    public function canUseWatchParty(): bool
    {
        return (bool) $this->currentSubscription?->package?->watch_party_enabled;
    }

    public function watchlistItems(): HasMany
    {
        return $this->hasMany(WatchlistItem::class);
    }

    public function mpesaPayments(): HasMany
    {
        return $this->hasMany(MpesaPayment::class);
    }
}
