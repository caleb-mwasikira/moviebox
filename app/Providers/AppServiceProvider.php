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
}
