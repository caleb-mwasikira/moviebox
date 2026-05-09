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
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::view('/events', 'pages.events')->name('events');
Route::view('/shop', 'pages.shop')->name('shop');
Route::get('/browse', [BrowseController::class, 'index'])->name('browse');
Route::get('/movies', [MovieController::class, 'index'])->name('movies');
Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');
Route::get('/movies/{movie}/watch', [MovieController::class, 'watch'])->name('movies.watch');
Route::get('/series', [SeriesController::class, 'index'])->name('series');
Route::get('/series/{series}', [SeriesController::class, 'show'])->name('series.show');
Route::get('/series/{series}/watch/{episode}', [SeriesController::class, 'watch'])->name('series.watch');
Route::view('/single-product', 'pages.single-product')->name('single-product');
Route::view('/trailers', 'pages.view-trailers')->name('trailers');
Route::view('/watch', 'pages.watch')->name('watch');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('/account', 'pages.account.index')->name('account');
    Route::get('/account/personal-info', [AccountController::class, 'personalInfo'])->name('account.personal-info');
    Route::put('/account/personal-info', [AccountController::class, 'updatePersonalInfo'])->name('account.personal-info.update');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::get('/account/billing', [AccountController::class, 'billingDetails'])->name('account.billing');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::resource('movies', AdminMovieController::class)->except(['show']);
    Route::patch('movies/{movie}/status', [AdminMovieController::class, 'toggleStatus'])->name('movies.toggle-status');
    Route::patch('movies/{movie}/featured', [AdminMovieController::class, 'toggleFeatured'])->name('movies.toggle-featured');
    Route::post('movies/{movie}/trailer', [AdminMovieController::class, 'uploadTrailer'])->name('movies.trailer');

    Route::resource('series', AdminSeriesController::class)->except(['show']);
    Route::patch('series/{series}/status', [AdminSeriesController::class, 'toggleStatus'])->name('series.toggle-status');
    Route::patch('series/{series}/featured', [AdminSeriesController::class, 'toggleFeatured'])->name('series.toggle-featured');
    Route::post('series/{series}/trailer', [AdminSeriesController::class, 'uploadTrailer'])->name('series.trailer');

    Route::post('series/{series}/seasons', [AdminSeasonController::class, 'store'])->name('seasons.store');
    Route::delete('seasons/{season}', [AdminSeasonController::class, 'destroy'])->name('seasons.destroy');

    Route::post('seasons/{season}/episodes', [AdminEpisodeController::class, 'store'])->name('episodes.store');
    Route::delete('episodes/{episode}', [AdminEpisodeController::class, 'destroy'])->name('episodes.destroy');

    Route::delete('posters/{poster}', [AdminPosterController::class, 'destroy'])->name('posters.destroy');
});

require __DIR__.'/settings.php';
