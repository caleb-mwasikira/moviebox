<?php

use App\Http\Middleware\AdminCanMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Middleware\CheckBanned;
use App\Http\Middleware\EnsureNotSharedGuest;
use App\Http\Middleware\RequireActiveSubscription;
use App\Http\Middleware\RequireAuthModal;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin'            => AdminMiddleware::class,
            'admin.can'        => AdminCanMiddleware::class,
            'api.key'          => ApiKeyMiddleware::class,
            'auth.modal'       => RequireAuthModal::class,
            'subscription'     => RequireActiveSubscription::class,
            'not.shared.guest' => EnsureNotSharedGuest::class,
        ]);
        $middleware->appendToGroup('web', CheckBanned::class);
        $middleware->preventRequestForgery([
            '/callbacks/*',
            '/mpesa/callback',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
