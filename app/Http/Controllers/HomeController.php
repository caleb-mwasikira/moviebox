<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;

class HomeController extends Controller
{
    public function index()
    {
        $movies = Movie::where('status', 'published')->latest()->take(7)->get();
        $series = Series::where('status', 'published')->latest()->take(4)->get();

        $featuredMovies = Movie::where('status', 'published')->where('featured', true)
            ->with('posters')->latest()->take(6)->get();
        $featuredMovies->each(fn($m) => $m->content_type = 'movie');

        $featuredSeries = Series::where('status', 'published')->where('featured', true)
            ->with('posters')->latest()->take(6)->get();
        $featuredSeries->each(fn($s) => $s->content_type = 'series');

        $featured = $featuredMovies->merge($featuredSeries)->sortByDesc('updated_at')->values()->take(6);

        return view('pages.index', compact('movies', 'series', 'featured'));
    }
}
