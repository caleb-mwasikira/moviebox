<?php

namespace App\Providers;

use App\Models\Collection;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Series;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super-admins (role column = 'admin') pass all Gate checks.
        Gate::before(fn ($user) => $user->isAdmin() ? true : null);

        Relation::morphMap([
            'movie'   => Movie::class,
            'episode' => Episode::class,
            'series'  => Series::class,
        ]);

        $this->configureDefaults();
        $this->adaptDevServicesToRequestHost();
        view()->addNamespace('pages', resource_path('views/pages'));

        view()->composer('layouts.app', function ($view) {
            $view->with('megaComingSoon', Movie::where('coming_soon', true)
                ->whereNotNull('thumbnail')
                ->latest()->take(5)->get(['id', 'title', 'genre', 'thumbnail', 'uuid']));

            $view->with('megaCollections', Collection::where('is_active', true)
                ->whereHas('items')
                ->orderBy('sort_order')->orderBy('name')
                ->get(['id', 'name', 'slug', 'description', 'thumbnail']));
        });

        view()->composer('auth.*', function ($view) {
            $movie = Movie::whereNotNull('thumbnail')->inRandomOrder()->first();
            $view->with('banner', $movie?->thumbnailUrl());
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
    /**
     * Local dev over the LAN (e.g. a phone opening http://192.168.0.105:8000).
     *
     * `public/hot` and STREAMING_BASE_URL point at 127.0.0.1, which on another device is that
     * device itself, and the dev machine's firewall usually blocks the extra dev ports anyway.
     * For non-loopback hosts we therefore serve the compiled assets from /build (same port as
     * the app, no Vite dev server needed) and point the streaming base URL at the request host.
     */
    private function adaptDevServicesToRequestHost(): void
    {
        if (! $this->app->environment('local') || $this->app->runningInConsole()) {
            return;
        }

        $host = request()->getHost();

        if (in_array($host, ['localhost', '127.0.0.1', '::1', '[::1]'], true)) {
            // Vite listens on 0.0.0.0 and records that in public/hot, which browsers refuse to
            // load from. Swap in the host this request came in on.
            $hot = public_path('hot');
            $dev = is_file($hot) ? parse_url(trim((string) file_get_contents($hot))) : null;

            if (($dev['host'] ?? '') === '0.0.0.0') {
                $url = ($dev['scheme'] ?? 'http') . '://' . $host . ':' . ($dev['port'] ?? 5173);
                $perHostHotFile = storage_path('framework/vite-hot-' . md5($host));

                if (! is_file($perHostHotFile) || file_get_contents($perHostHotFile) !== $url) {
                    file_put_contents($perHostHotFile, $url);
                }

                Vite::useHotFile($perHostHotFile);
            }

            return;
        }

        if (is_file(public_path('hot')) && is_file(public_path('build/manifest.json'))) {
            Vite::useHotFile(storage_path('framework/vite-hot-disabled'));
        }

        $streaming = parse_url((string) config('services.streaming.base_url'));
        if (in_array($streaming['host'] ?? '', ['localhost', '127.0.0.1'], true)) {
            config(['services.streaming.base_url' => ($streaming['scheme'] ?? 'http') . '://' . $host . ':' . ($streaming['port'] ?? 9090)]);
        }
    }
}
