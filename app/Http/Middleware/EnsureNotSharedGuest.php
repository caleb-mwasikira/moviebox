<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureNotSharedGuest
{
    public function handle(Request $request, Closure $next)
    {
        if (session('shared_guest')) {
            return redirect()->route('home')
                ->with('error', 'Account settings are not available in guest mode.');
        }

        return $next($request);
    }
}
