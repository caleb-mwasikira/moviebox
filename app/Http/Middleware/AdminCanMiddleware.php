<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates individual admin routes by permission while letting super-admins
 * (users with role = 'admin') bypass all checks.
 *
 * Usage in routes:  ->middleware('admin.can:view movies')
 */
class AdminCanMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if ($user && $user->canInAdmin($permission)) {
            return $next($request);
        }

        abort(403, "You don't have permission to: {$permission}.");
    }
}
