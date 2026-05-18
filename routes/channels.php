<?php

use App\Models\WatchParty;
use App\Models\WatchPartyMember;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', fn($user, $id) => (int) $user->id === (int) $id);

Broadcast::channel('admin-notifications.{userId}', fn($user, $userId) => $user->isAdmin() && (int) $user->id === (int) $userId);

Broadcast::channel('watch-party.{token}', function ($user, $token) {
    $party = WatchParty::where('token', $token)->first();
    if (! $party) return false;
    $isMember = WatchPartyMember::where('watch_party_id', $party->id)
        ->where('user_id', $user->id)
        ->whereNull('kicked_at')
        ->exists();
    if (! $isMember) return false;
    return ['id' => $user->id, 'name' => $user->name, 'is_host' => $user->id === $party->host_id];
});
