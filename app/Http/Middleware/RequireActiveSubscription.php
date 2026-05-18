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

        // Enforce shared-guest session expiry. The session stores a hard deadline
        // set when the guest first visited the link. Check it here so that even if
        // the content-specific shared_access keys are somehow still present, access
        // is fully revoked once the original grant window has closed.
        if (session('shared_guest')) {
            $guestExpires = session('shared_guest_expires', 0);
            if ($guestExpires && time() > $guestExpires) {
                \Illuminate\Support\Facades\Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('home')
                    ->with('error', 'Your guest access has expired. Ask the link owner to share again.');
            }
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
            if ($this->isFreeContent($request) || $this->hasSharedAccess($request)) {
                return $next($request);
            }
            return redirect()->route('subscription.expired');
        }

        // Shared guests are logged in as the owner but may only watch the one shared title.
        if (session('shared_guest') && ! $this->isFreeContent($request) && ! $this->hasSharedAccess($request)) {
            return $this->redirectToSharedContent();
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

    private function redirectToSharedContent(): \Illuminate\Http\RedirectResponse
    {
        $now    = time();
        $grants = session('shared_access', []);

        foreach ($grants['movie'] ?? [] as $id => $exp) {
            if ($exp > $now) {
                $movie = Movie::find($id);
                if ($movie) {
                    return redirect()->route('movies.show', $movie)
                        ->with('info', "Your guest pass only covers \"{$movie->title}\" — you've been brought back so you can enjoy it.");
                }
            }
        }

        foreach ($grants['series'] ?? [] as $id => $exp) {
            if ($exp > $now) {
                $series = \App\Models\Series::find($id);
                if ($series) {
                    return redirect()->route('series.show', $series)
                        ->with('info', "Your guest pass only covers \"{$series->title}\" — you've been brought back so you can enjoy it.");
                }
            }
        }

        return redirect()->route('home')
            ->with('error', 'Your guest session has expired or the shared content is no longer available.');
    }

    private function hasSharedAccess(Request $request): bool
    {
        $now = time();

        $movie = $request->route('movie');
        if ($movie instanceof Movie) {
            $exp = session('shared_access.movie.' . $movie->id);
            return $exp && $exp > $now;
        }

        $episode = $request->route('episode');
        if ($episode instanceof Episode) {
            $series = $episode->season->series;
            $exp = session('shared_access.series.' . $series->id);
            return $exp && $exp > $now;
        }

        return false;
    }
}
