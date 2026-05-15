<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\Admin\MovieController as AdminMovieController;
use App\Http\Controllers\Admin\SeriesController as AdminSeriesController;
use App\Http\Controllers\Admin\SeasonController as AdminSeasonController;
use App\Http\Controllers\Admin\EpisodeController as AdminEpisodeController;
use App\Http\Controllers\Admin\PosterController as AdminPosterController;
use App\Http\Controllers\Admin\StreamingStatusController as AdminStreamingStatusController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SubscriptionPackageController as AdminSubscriptionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyOtpController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\VideoCallbackController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/verify-otp', [VerifyOtpController::class, 'show'])->name('password.verify-otp');
    Route::post('/verify-otp', [VerifyOtpController::class, 'store'])->name('password.verify-otp.store');
    Route::get('/reset-password', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});
Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout')->middleware('auth');

// Internal callbacks from Go streaming service — no auth, CSRF-exempt via bootstrap/app.php
Route::post('/callbacks/video-ready', [VideoCallbackController::class, 'videoReady'])->name('callbacks.video-ready');
Route::post('/callbacks/mkv-converted', [VideoCallbackController::class, 'mkvConverted'])->name('callbacks.mkv-converted');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::view('/events', 'pages.events')->name('events');
Route::view('/shop', 'pages.shop')->name('shop');
Route::get('/browse', [BrowseController::class, 'index'])->name('browse');
Route::get('/movies', [MovieController::class, 'index'])->name('movies');
Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');
Route::get('/series', [SeriesController::class, 'index'])->name('series');
Route::get('/series/{series}', [SeriesController::class, 'show'])->name('series.show');

Route::middleware('auth.modal')->group(function () {
    Route::get('/movies/{movie}/watch', [MovieController::class, 'watch'])->name('movies.watch');
    Route::get('/series/{series}/watch/{episode}', [SeriesController::class, 'watch'])->name('series.watch');
});
Route::view('/single-product', 'pages.single-product')->name('single-product');
Route::view('/trailers', 'pages.view-trailers')->name('trailers');
Route::view('/watch', 'pages.watch')->name('watch');

Route::middleware(['auth', 'verified'])->prefix('account')->name('account.')->group(function () {
    Route::view('/', 'pages.account.index')->name('dashboard');
    Route::get('personal-info', [AccountController::class, 'personalInfo'])->name('personal-info');
    Route::put('personal-info', [AccountController::class, 'updatePersonalInfo'])->name('personal-info.update');
    Route::put('password', [AccountController::class, 'updatePassword'])->name('password.update');
    Route::get('billing', [AccountController::class, 'billingDetails'])->name('billing');
    Route::post('update-subscription-plan/{newPackage}', [AccountController::class, 'updateSubscriptionPlan'])->name('update-plan');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::resource('movies', AdminMovieController::class)->except(['show']);
    Route::patch('movies/{movie}/status', [AdminMovieController::class, 'toggleStatus'])->name('movies.toggle-status');
    Route::patch('movies/{movie}/featured', [AdminMovieController::class, 'toggleFeatured'])->name('movies.toggle-featured');
    Route::post('movies/{movie}/trailer', [AdminMovieController::class, 'uploadTrailer'])->name('movies.trailer');
    Route::post('movies/{movie}/video', [AdminMovieController::class, 'updateVideo'])->name('movies.video');
    Route::post('movies/{movie}/reprocess', [AdminMovieController::class, 'reprocess'])->name('movies.reprocess');

    Route::resource('series', AdminSeriesController::class)->except(['show']);
    Route::patch('series/{series}/status', [AdminSeriesController::class, 'toggleStatus'])->name('series.toggle-status');
    Route::patch('series/{series}/featured', [AdminSeriesController::class, 'toggleFeatured'])->name('series.toggle-featured');
    Route::post('series/{series}/trailer', [AdminSeriesController::class, 'uploadTrailer'])->name('series.trailer');

    Route::post('series/{series}/seasons', [AdminSeasonController::class, 'store'])->name('seasons.store');
    Route::delete('seasons/{season}', [AdminSeasonController::class, 'destroy'])->name('seasons.destroy');

    Route::post('seasons/{season}/episodes', [AdminEpisodeController::class, 'store'])->name('episodes.store');
    Route::post('episodes/{episode}/video', [AdminEpisodeController::class, 'updateVideo'])->name('episodes.video');
    Route::delete('episodes/{episode}', [AdminEpisodeController::class, 'destroy'])->name('episodes.destroy');

    Route::delete('posters/{poster}', [AdminPosterController::class, 'destroy'])->name('posters.destroy');

    Route::get('encoding-status', [AdminStreamingStatusController::class, 'index'])->name('encoding-status');

    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/ban', [AdminUserController::class, 'ban'])->name('users.ban');
    Route::patch('users/{user}/unban', [AdminUserController::class, 'unban'])->name('users.unban');

    Route::resource('packages', AdminSubscriptionController::class);
});

require __DIR__.'/settings.php';
