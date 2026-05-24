<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\StreamController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mobile API routes
|
| All routes require the X-App-Key header matching MOBILE_APP_KEY in .env.
| Authenticated routes additionally require a Sanctum bearer token.
|--------------------------------------------------------------------------
*/

Route::middleware('api.key')->group(function () {

    // Auth
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);

    // Public catalog — browsing does not require login
    Route::get('movies', [CatalogController::class, 'movies']);
    Route::get('movies/{movie:uuid}', [CatalogController::class, 'movie']);
    Route::get('series', [CatalogController::class, 'series']);
    Route::get('series/{series:uuid}', [CatalogController::class, 'show']);

    // Subscription packages list is public
    Route::get('subscriptions/packages', [SubscriptionController::class, 'packages']);

    // Authenticated endpoints
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        // Subscriptions
        Route::get('subscriptions/current', [SubscriptionController::class, 'current']);
        Route::post('subscriptions/initiate', [SubscriptionController::class, 'initiate']);
        Route::get('subscriptions/status', [SubscriptionController::class, 'status']);

        // Streaming — returns HLS master playlist URL + bearer token
        Route::get('stream/movie/{movie:uuid}', [StreamController::class, 'movie']);
        Route::get('stream/episode/{episode:uuid}', [StreamController::class, 'episode']);
    });
});
