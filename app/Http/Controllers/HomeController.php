<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Movie;
use App\Models\Series;
use App\Models\SubscriptionPackage;

class HomeController extends Controller
{
    public function index()
    {
        $movies = Movie::where('status', 'published')->where('coming_soon', false)->with('posters')->latest()->take(18)->get();
        $series = Series::where('status', 'published')->with('posters')->latest()->take(18)->get();
        $comingSoon = Movie::where('coming_soon', true)->with('posters')->latest()->take(18)->get();

        $featuredMovies = Movie::where('status', 'published')->where('coming_soon', false)->where('featured', true)
            ->with('posters')->latest()->take(6)->get();
        $featuredMovies->each(fn($m) => $m->content_type = 'movie');

        $featuredSeries = Series::where('status', 'published')->where('featured', true)
            ->with(['posters', 'seasons'])->latest()->take(6)->get();
        $featuredSeries->each(fn($s) => $s->content_type = 'series');

        $featuredComingSoon = Movie::where('coming_soon', true)->where('featured', true)
            ->with('posters')->latest()->take(6)->get();
        $featuredComingSoon->each(fn($m) => $m->content_type = 'coming_soon');

        $featured = $featuredMovies->merge($featuredSeries)->merge($featuredComingSoon)
            ->sortByDesc('updated_at')->values()->take(6);

        $cheapestPackage = SubscriptionPackage::where('is_active', true)
            ->orderBy('price')
            ->first();

        $collections = Collection::where('is_active', true)
            ->whereHas('items')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with(['items' => fn($q) => $q->with('collectible.posters')->limit(12)])
            ->get();

        return view('pages.index', compact('movies', 'series', 'featured', 'comingSoon', 'cheapestPackage', 'collections'));
    }
}
