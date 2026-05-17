<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequireAuthModal
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (! Auth::user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            return $next($request);
        }

        // Allow free movies and episodes to be watched without login
        $movie = $request->route('movie');
        if ($movie instanceof \App\Models\Movie && $movie->is_free) {
            return $next($request);
        }

        $episode = $request->route('episode');
        if ($episode instanceof \App\Models\Episode && $episode->is_free) {
            return $next($request);
        }

        // Allow guests who arrived via a shared link and have a valid temporary grant
        if ($this->hasSharedAccess($request)) {
            return $next($request);
        }

        // Preserve the intended URL so post-login redirect works

        session()->put('url.intended', $request->url());

        // Use the HTTP Referer header for the back URL.
        // redirect()->back() reads the stale session _previous.url which can itself
        // be a watch URL, causing an infinite redirect loop. The Referer header
        // reliably reflects where the user just came from (typically the show page).
        // If there is no Referer, or the Referer is also a protected watch URL,
        // open the auth modal on the home page instead.
        $back = $request->header('referer', '');
        $backPath = parse_url($back, PHP_URL_PATH) ?? '';
        if (!$back || str_contains($backPath, '/watch')) {
            $back = route('home');
        }

        return redirect($back)->with('show_auth_modal', true);
    }

    private function hasSharedAccess(Request $request): bool
    {
        $now = time();

        $movie = $request->route('movie');
        if ($movie instanceof \App\Models\Movie) {
            $exp = session('shared_access.movie.' . $movie->id);
            return $exp && $exp > $now;
        }

        $episode = $request->route('episode');
        if ($episode instanceof \App\Models\Episode) {
            $series = $episode->season->series;
            $exp = session('shared_access.series.' . $series->id);
            return $exp && $exp > $now;
        }

        return false;
    }
}
