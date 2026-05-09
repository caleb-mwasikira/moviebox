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
            return $next($request);
        }

        // Preserve the intended URL so post-login redirect works
        session()->put('url.intended', $request->url());

        return redirect()->back()->with('show_auth_modal', true);
    }
}
