<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'pages.index')->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::view('/events', 'pages.events')->name('events');
Route::view('/shop', 'pages.shop')->name('shop');
Route::view('/movies', 'pages.single')->name('movies');
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

require __DIR__.'/settings.php';
