<?php

namespace App\Http\Middleware;

use App\Models\Episode;
use App\Models\Movie;
use App\Models\Profile;
use Closure;
use Illuminate\Http\Request;

class RequireActiveSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user || $user->isAdmin()) {
            return $next($request);
        }

        // Determine whose subscription governs access.
        // If the active profile belongs to another account, use that account owner's subscription.
        $profileId    = session('active_profile_id');
        $subscription = $user->currentSubscription;

        if ($profileId) {
            $profile = Profile::find($profileId);

            if ($profile && $profile->user_id !== $user->id) {
                // Sub-account: check the profile owner's subscription
                $owner        = $profile->user;
                $subscription = $owner?->currentSubscription;

                // Block if the owner's account is banned
                if ($owner?->isBanned()) {
                    return redirect()->route('profiles.select')
                        ->with('error', 'This profile is no longer available.');
                }

                // Block if the owner's subscription has expired
                if (! $subscription) {
                    return redirect()->route('profiles.select')
                        ->with('error', 'The account owner\'s subscription has expired. Content is currently unavailable.');
                }

                // Also block access if the profile itself is disabled
                if ($profile->isDisabled()) {
                    return redirect()->route('profiles.select')
                        ->with('error', 'This profile has been disabled by the account owner.');
                }
            }
        }

        if (! $subscription) {
            if ($this->isFreeContent($request)) {
                return $next($request);
            }
            return redirect()->route('subscription.expired');
        }

        return $next($request);
    }

    private function isFreeContent(Request $request): bool
    {
        $movie = $request->route('movie');
        if ($movie instanceof Movie && $movie->is_free) {
            return true;
        }

        $episode = $request->route('episode');
        if ($episode instanceof Episode && $episode->is_free) {
            return true;
        }

        return false;
    }
}
