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
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Admin\SubtitleController as AdminSubtitleController;
use App\Http\Controllers\Admin\SubscriptionPackageController as AdminSubscriptionController;
use App\Http\Controllers\Admin\ComingSoonController as AdminComingSoonController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\TestEmailController as AdminTestEmailController;
use App\Http\Controllers\Admin\ConvertController as AdminConvertController;
use App\Http\Controllers\Admin\CollectionController as AdminCollectionController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController;
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\TrashController as AdminTrashController;
use App\Http\Controllers\Admin\GiftController as AdminGiftController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ComingSoonController;
use App\Http\Controllers\SharedLinkController;
use App\Http\Controllers\GiftSubscriptionController;
use App\Http\Controllers\WatchPartyController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\VerifyOtpController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\VideoCallbackController;
use App\Http\Controllers\AccountProfileController;
use App\Http\Controllers\ProfileInviteController;
use App\Http\Controllers\ProfileSelectionController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\WatchProgressController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->where('provider', 'google|facebook')->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->where('provider', 'google|facebook')->name('social.callback');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/verify-otp', [VerifyOtpController::class, 'show'])->name('password.verify-otp');
    Route::post('/verify-otp', [VerifyOtpController::class, 'store'])->name('password.verify-otp.store');
    Route::get('/reset-password', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});
Route::post('/logout', [LogoutController::class, 'destroy'])->name('logout')->middleware('auth');

// Email verification (auth required, verified NOT required)
// Invite routes — open to guests (account creation happens here)
Route::get('/profiles/invite/{token}', [ProfileInviteController::class, 'show'])->name('profiles.invite');
Route::post('/profiles/invite/{token}', [ProfileInviteController::class, 'accept'])->name('profiles.invite.accept');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'show'])->name('verification.notice');
    Route::post('/email/verify', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('verification.send');

    // Profile selection (auth only, no verified requirement)
    Route::get('/profiles', [ProfileSelectionController::class, 'show'])->name('profiles.select');
    Route::post('/profiles/select', [ProfileSelectionController::class, 'select'])->name('profiles.select.post');
});

// Internal callbacks from Go streaming service — no auth, CSRF-exempt via bootstrap/app.php
Route::post('/callbacks/video-ready', [VideoCallbackController::class, 'videoReady'])->name('callbacks.video-ready');
Route::post('/callbacks/mkv-converted', [VideoCallbackController::class, 'mkvConverted'])->name('callbacks.mkv-converted');

Route::get('/', [HomeController::class, 'welcome'])->name('home');
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');

// M-Pesa — callback is CSRF-exempt via bootstrap/app.php
Route::post('/mpesa/callback', [MpesaController::class, 'callback'])->name('mpesa.callback');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/mpesa/initiate', [MpesaController::class, 'initiate'])->name('mpesa.initiate');
    Route::get('/mpesa/status/{checkoutRequestId}', [MpesaController::class, 'status'])->name('mpesa.status');
    Route::get('/payment/{packageUuid}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{packageUuid}/activate-free', [PaymentController::class, 'activateFree'])->name('payment.activate-free');
});

Route::view('/about', 'pages.about')->name('about');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/coming-soon', [ComingSoonController::class, 'index'])->name('coming-soon');
Route::get('/coming-soon/{comingSoon}', [ComingSoonController::class, 'show'])->name('coming-soon.show');
Route::view('/shop', 'pages.shop')->name('shop');
Route::get('/browse', [BrowseController::class, 'index'])->name('browse');
Route::get('/collections/{collection}', [CollectionController::class, 'show'])->name('collections.show');
Route::get('/search/suggestions', [BrowseController::class, 'suggestions'])->name('search.suggestions');
Route::get('/s/{sharedLink}', [SharedLinkController::class, 'show'])->name('share.show');
Route::post('/share', [SharedLinkController::class, 'store'])->middleware('auth')->name('share.store');

// Gift subscriptions — accept is open to all, purchase requires auth
Route::get('/gift/accept/{token}', [GiftSubscriptionController::class, 'accept'])->name('gift.accept');
Route::middleware(['auth', 'verified', 'not.shared.guest'])->group(function () {
    Route::get('/gift/{packageUuid}', [GiftSubscriptionController::class, 'show'])->name('gift.show');
    Route::post('/gift/{packageUuid}', [GiftSubscriptionController::class, 'store'])->name('gift.store');
    Route::get('/gift-sent/{token}', [GiftSubscriptionController::class, 'sent'])->name('gift.sent');
    Route::post('/gift/accept/{token}', [GiftSubscriptionController::class, 'claim'])->name('gift.claim');
    Route::patch('/gift/{gift}/email', [GiftSubscriptionController::class, 'updateEmail'])->name('gift.update-email');
});

Route::get('/movies', [MovieController::class, 'index'])->name('movies');
Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');
Route::get('/series', [SeriesController::class, 'index'])->name('series');
Route::get('/series/{series}', [SeriesController::class, 'show'])->name('series.show');

Route::middleware(['auth.modal', 'subscription'])->group(function () {
    Route::get('/movies/{movie}/watch', [MovieController::class, 'watch'])->name('movies.watch');
    Route::get('/series/{series}/watch/{episode}', [SeriesController::class, 'watch'])->name('series.watch');
});

Route::middleware(['auth', 'verified', 'subscription'])->group(function () {
    Route::get('/movies/{movie}/download', [DownloadController::class, 'movie'])->name('movies.download');
    Route::get('/episodes/{episode}/download', [DownloadController::class, 'episode'])->name('episodes.download');
});
Route::view('/single-product', 'pages.single-product')->name('single-product');
Route::view('/trailers', 'pages.view-trailers')->name('trailers');
Route::view('/watch', 'pages.watch')->name('watch');

Route::middleware('auth')->post('/watch/progress', [WatchProgressController::class, 'upsert'])->name('watch.progress');

Route::middleware(['auth', 'verified'])->post('/watchlist/{type}/{id}', [WatchlistController::class, 'toggle'])->name('watchlist.toggle');

// Watch party — public show + guest join; state/message open to session guests; actions auth-gated
Route::get('/watch-party/{token}', [WatchPartyController::class, 'show'])->name('watch-party.show');
Route::post('/watch-party/{token}/guest-join', [WatchPartyController::class, 'guestJoin'])->name('watch-party.guest-join');
Route::get('/watch-party/{token}/state', [WatchPartyController::class, 'state'])->name('watch-party.state');
Route::post('/watch-party/{token}/message', [WatchPartyController::class, 'sendMessage'])->name('watch-party.message');
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/watch-party/{token}/join', [WatchPartyController::class, 'join'])->name('watch-party.join');
    Route::post('/watch-party/{token}/leave', [WatchPartyController::class, 'leave'])->name('watch-party.leave');
    Route::delete('/watch-party/{token}', [WatchPartyController::class, 'end'])->name('watch-party.end');
    Route::patch('/watch-party/{token}/sync', [WatchPartyController::class, 'sync'])->name('watch-party.sync');
    Route::patch('/watch-party/{token}/countdown', [WatchPartyController::class, 'setCountdown'])->name('watch-party.countdown');
    Route::patch('/watch-party/{token}/next-episode', [WatchPartyController::class, 'nextEpisode'])->name('watch-party.next-episode');
    Route::patch('/watch-party/{token}/jump-to-episode', [WatchPartyController::class, 'jumpToEpisode'])->name('watch-party.jump-to-episode');
    Route::patch('/watch-party/{token}/lobby', [WatchPartyController::class, 'toggleLobby'])->name('watch-party.lobby');
    Route::patch('/watch-party/{token}/kick', [WatchPartyController::class, 'kick'])->name('watch-party.kick');
    Route::patch('/watch-party/{token}/unkick', [WatchPartyController::class, 'unkick'])->name('watch-party.unkick');
});

Route::middleware(['auth', 'verified', 'not.shared.guest'])->prefix('account')->name('account.')->group(function () {
    Route::view('/', 'pages.account.index')->name('dashboard');
    Route::get('profile', [AccountController::class, 'profile'])->name('profile');
    Route::post('profile/avatar', [AccountController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('profile/avatar', [AccountController::class, 'removeAvatar'])->name('profile.avatar.remove');
    Route::get('personal-info', fn() => redirect()->route('account.profile'))->name('personal-info');
    Route::put('personal-info', [AccountController::class, 'updatePersonalInfo'])->name('personal-info.update');
    Route::put('password', [AccountController::class, 'updatePassword'])->name('password.update');
    Route::get('billing', [AccountController::class, 'billingDetails'])->name('billing');
    Route::post('update-subscription-plan/{newPackage}', [AccountController::class, 'updateSubscriptionPlan'])->name('update-plan');
    Route::get('watching', [WatchProgressController::class, 'index'])->name('watching');
    Route::get('watchlist', [WatchlistController::class, 'index'])->name('watchlist');
    Route::get('watch-party', [WatchPartyController::class, 'index'])->name('watch-party.index');
    Route::get('watch-party/create', [WatchPartyController::class, 'create'])->name('watch-party.create');
    Route::post('watch-party', [WatchPartyController::class, 'store'])->name('watch-party.store');
    Route::get('profiles', fn() => redirect()->route('account.profile'))->name('profiles');
    Route::post('profiles', [AccountProfileController::class, 'store'])->name('profiles.store');
    Route::patch('profiles/{profile}/activate', [AccountProfileController::class, 'activate'])->name('profiles.activate');
    Route::patch('profiles/{profile}/enable', [AccountProfileController::class, 'enable'])->name('profiles.enable');
    Route::patch('profiles/{profile}/disable', [AccountProfileController::class, 'disable'])->name('profiles.disable');
    Route::post('profiles/{profile}/resend-invite', [AccountProfileController::class, 'resendInvite'])->name('profiles.resend-invite');
    Route::patch('profiles/{profile}/own', [AccountProfileController::class, 'updateOwn'])->name('profiles.update-own');
    Route::delete('profiles/{profile}', [AccountProfileController::class, 'destroy'])->name('profiles.destroy');
    Route::get('delete-account', [AccountController::class, 'deleteAccountPage'])->name('delete-account');
    Route::delete('delete-account', [AccountController::class, 'deleteAccount'])->name('delete-account.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Analytics
    Route::get('analytics', [AdminAnalyticsController::class, 'index'])
        ->middleware('admin.can:view analytics')->name('analytics');

    // Encoding status
    Route::get('encoding-status', [AdminStreamingStatusController::class, 'index'])
        ->middleware('admin.can:view encoding status')->name('encoding-status');

    // ── Movies ────────────────────────────────────────────────────────────────
    Route::middleware('admin.can:view movies')->group(function () {
        Route::resource('movies', AdminMovieController::class)->except(['show']);
        Route::patch('movies/{movie}/status', [AdminMovieController::class, 'toggleStatus'])->name('movies.toggle-status');
        Route::patch('movies/{movie}/featured', [AdminMovieController::class, 'toggleFeatured'])->name('movies.toggle-featured');
        Route::post('movies/{movie}/trailer', [AdminMovieController::class, 'uploadTrailer'])
            ->middleware('admin.can:upload movie videos')->name('movies.trailer');
        Route::post('movies/{movie}/video', [AdminMovieController::class, 'updateVideo'])
            ->middleware('admin.can:upload movie videos')->name('movies.video');
        Route::post('movies/{movie}/reprocess', [AdminMovieController::class, 'reprocess'])
            ->middleware('admin.can:reprocess movies')->name('movies.reprocess');
        Route::get('movies/{movie}/encoding-status', [AdminMovieController::class, 'encodingStatus'])
            ->name('movies.encoding-status');
        Route::post('movies/{movie}/encode-quality', [AdminMovieController::class, 'reprocessQuality'])
            ->middleware('admin.can:reprocess movies')->name('movies.encode-quality');
        Route::post('movies/{movie}/subtitles', [AdminSubtitleController::class, 'storeForMovie'])
            ->middleware('admin.can:manage subtitles')->name('movies.subtitles.store');
        Route::post('movies/{movie}/convert-to-episode', [AdminConvertController::class, 'movieToEpisode'])
            ->middleware('admin.can:convert content')->name('movies.convert-to-episode');
    });

    // ── Series ────────────────────────────────────────────────────────────────
    Route::middleware('admin.can:view series')->group(function () {
        Route::resource('series', AdminSeriesController::class)->except(['show']);
        Route::patch('series/{series}/status', [AdminSeriesController::class, 'toggleStatus'])->name('series.toggle-status');
        Route::patch('series/{series}/featured', [AdminSeriesController::class, 'toggleFeatured'])->name('series.toggle-featured');
        Route::post('series/{series}/trailer', [AdminSeriesController::class, 'uploadTrailer'])
            ->middleware('admin.can:upload series videos')->name('series.trailer');
        Route::post('series/{series}/seasons', [AdminSeasonController::class, 'store'])->name('seasons.store');
        Route::delete('seasons/{season}', [AdminSeasonController::class, 'destroy'])->name('seasons.destroy');
        Route::post('seasons/{season}/episodes', [AdminEpisodeController::class, 'store'])->name('episodes.store');
        Route::put('episodes/{episode}', [AdminEpisodeController::class, 'update'])->name('episodes.update');
        Route::patch('episodes/{episode}/toggle-free', [AdminEpisodeController::class, 'toggleFree'])->name('episodes.toggle-free');
        Route::post('episodes/{episode}/video', [AdminEpisodeController::class, 'updateVideo'])
            ->middleware('admin.can:upload series videos')->name('episodes.video');
        Route::post('episodes/{episode}/encode-quality', [AdminEpisodeController::class, 'reprocessQuality'])
            ->middleware('admin.can:reprocess movies')->name('episodes.encode-quality');
        Route::post('episodes/{episode}/encode-all', [AdminEpisodeController::class, 'reprocessAll'])
            ->middleware('admin.can:reprocess movies')->name('episodes.encode-all');
        Route::delete('episodes/{episode}', [AdminEpisodeController::class, 'destroy'])->name('episodes.destroy');
        Route::post('episodes/{episode}/subtitles', [AdminSubtitleController::class, 'storeForEpisode'])
            ->middleware('admin.can:manage subtitles')->name('episodes.subtitles.store');
        Route::post('episodes/{episode}/convert-to-movie', [AdminConvertController::class, 'episodeToMovie'])
            ->middleware('admin.can:convert content')->name('episodes.convert-to-movie');
    });

    // Shared subtitle/poster delete (content managers have both movie+series)
    Route::delete('subtitles/{subtitle}', [AdminSubtitleController::class, 'destroy'])
        ->middleware('admin.can:manage subtitles')->name('subtitles.destroy');
    Route::delete('posters/{poster}', [AdminPosterController::class, 'destroy'])
        ->middleware('admin.can:manage posters')->name('posters.destroy');

    // ── Collections ───────────────────────────────────────────────────────────
    Route::middleware('admin.can:view collections')->group(function () {
        Route::resource('collections', AdminCollectionController::class)->except(['show']);
        Route::post('collections/{collection}/items', [AdminCollectionController::class, 'addItem'])
            ->middleware('admin.can:manage collections')->name('collections.add-item');
        Route::delete('collections/{collection}/items/{item}', [AdminCollectionController::class, 'removeItem'])
            ->middleware('admin.can:manage collections')->name('collections.remove-item');
    });

    // ── Coming Soon ───────────────────────────────────────────────────────────
    Route::middleware('admin.can:view coming soon')->group(function () {
        Route::get('coming-soon', [AdminComingSoonController::class, 'index'])->name('coming-soon.index');
        Route::get('coming-soon/create', [AdminComingSoonController::class, 'create'])
            ->middleware('admin.can:manage coming soon')->name('coming-soon.create');
        Route::post('coming-soon', [AdminComingSoonController::class, 'store'])
            ->middleware('admin.can:manage coming soon')->name('coming-soon.store');
        Route::get('coming-soon/{comingSoon}/edit', [AdminComingSoonController::class, 'edit'])
            ->middleware('admin.can:manage coming soon')->name('coming-soon.edit');
        Route::put('coming-soon/{comingSoon}', [AdminComingSoonController::class, 'update'])
            ->middleware('admin.can:manage coming soon')->name('coming-soon.update');
        Route::patch('coming-soon/{comingSoon}/featured', [AdminComingSoonController::class, 'toggleFeatured'])
            ->middleware('admin.can:manage coming soon')->name('coming-soon.toggle-featured');
        Route::post('coming-soon/{comingSoon}/trailer', [AdminComingSoonController::class, 'uploadTrailer'])
            ->middleware('admin.can:manage coming soon')->name('coming-soon.trailer');
        Route::post('coming-soon/{comingSoon}/video', [AdminComingSoonController::class, 'uploadVideo'])
            ->middleware('admin.can:manage coming soon')->name('coming-soon.video');
        Route::post('coming-soon/{comingSoon}/promote-series', [AdminComingSoonController::class, 'promoteToSeries'])
            ->middleware('admin.can:manage coming soon')->name('coming-soon.promote-series');
        Route::delete('coming-soon/{comingSoon}', [AdminComingSoonController::class, 'destroy'])
            ->middleware('admin.can:manage coming soon')->name('coming-soon.destroy');
    });

    // ── Roles ─────────────────────────────────────────────────────────────────
    Route::middleware('admin.can:assign permissions')->group(function () {
        Route::resource('roles', AdminRoleController::class)->except(['show']);
    });

    // ── Users ─────────────────────────────────────────────────────────────────
    Route::middleware('admin.can:view users')->group(function () {
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::patch('users/{user}/ban', [AdminUserController::class, 'ban'])
            ->middleware('admin.can:ban users')->name('users.ban');
        Route::patch('users/{user}/unban', [AdminUserController::class, 'unban'])
            ->middleware('admin.can:ban users')->name('users.unban');
        Route::patch('users/{user}/extend-subscription', [AdminUserController::class, 'extendSubscription'])
            ->middleware('admin.can:manage user subscriptions')->name('users.extend-subscription');
        Route::patch('users/{user}/assign-role', [AdminUserController::class, 'assignRole'])
            ->middleware('admin.can:assign permissions')->name('users.assign-role');
        Route::get('users/{user}/permissions', [AdminUserController::class, 'permissions'])
            ->middleware('admin.can:assign permissions')->name('users.permissions');
        Route::put('users/{user}/permissions', [AdminUserController::class, 'syncPermissions'])
            ->middleware('admin.can:assign permissions')->name('users.permissions.sync');
    });

    // ── Finance ───────────────────────────────────────────────────────────────
    Route::get('payments', [AdminPaymentController::class, 'index'])
        ->middleware('admin.can:view payments')->name('payments.index');

    Route::get('gifts', [AdminGiftController::class, 'index'])->name('gifts.index');
    Route::post('gifts/{gift}/resend', [AdminGiftController::class, 'resend'])->name('gifts.resend');

    Route::middleware('admin.can:view packages')->group(function () {
        Route::resource('packages', AdminSubscriptionController::class);
        Route::post('packages/{uuid}/restore', [AdminSubscriptionController::class, 'restore'])->name('packages.restore');
    });

    // ── Support ───────────────────────────────────────────────────────────────
    Route::middleware('admin.can:view contacts')->group(function () {
        Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
        Route::get('contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
        Route::delete('contacts/{contact}', [AdminContactController::class, 'destroy'])
            ->middleware('admin.can:manage contacts')->name('contacts.destroy');
    });

    // ── Activity Log (admin only) ─────────────────────────────────────────────
    Route::get('activity', [AdminActivityController::class, 'index'])->name('activity.index');

    // ── Trash (admin only — restore / force-delete soft-deleted content) ──────
    Route::get('trash', [AdminTrashController::class, 'index'])->name('trash.index');
    Route::patch('trash/movies/{id}/restore', [AdminMovieController::class, 'restore'])->name('movies.restore');
    Route::delete('trash/movies/{id}/force', [AdminMovieController::class, 'forceDestroy'])->name('movies.force-delete');
    Route::patch('trash/series/{id}/restore', [AdminSeriesController::class, 'restore'])->name('series.restore');
    Route::delete('trash/series/{id}/force', [AdminSeriesController::class, 'forceDestroy'])->name('series.force-delete');

    // ── System ────────────────────────────────────────────────────────────────
    Route::middleware('admin.can:send test emails')->group(function () {
        Route::get('test-email', [AdminTestEmailController::class, 'show'])->name('test-email.show');
        Route::post('test-email', [AdminTestEmailController::class, 'send'])->name('test-email.send');
    });
});

require __DIR__.'/settings.php';
