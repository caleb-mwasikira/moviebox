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
            return redirect()->route('subscription.expired');
        }

        return $next($request);
    }
}
