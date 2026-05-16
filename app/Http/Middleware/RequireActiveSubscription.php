<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireActiveSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && !$user->isAdmin() && !$user->currentSubscription) {
            $movie = $request->route('movie');
            if ($movie instanceof \App\Models\Movie && $movie->is_free) {
                return $next($request);
            }

            $episode = $request->route('episode');
            if ($episode instanceof \App\Models\Episode && $episode->is_free) {
                return $next($request);
            }

            return redirect()->route('subscription.expired');
        }

        return $next($request);
    }
}
