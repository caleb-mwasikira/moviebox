<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\WatchlistItem;
use App\Models\WatchProgress;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $query = Movie::where('status', 'published');

        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }

        if ($request->boolean('free')) {
            $query->where('is_free', true);
        }

        $movies = $query->latest()->paginate(12)->withQueryString();
        $genres = Movie::where('status', 'published')
            ->distinct()
            ->orderBy('genre')
            ->pluck('genre')
            ->filter()
            ->values();

        return view('pages.movies.index', compact('movies', 'genres'));
    }

    public function show(Movie $movie)
    {
        abort_unless($movie->isPublished(), 404);
        $movie->load('posters');

        $profileId     = session('active_profile_id');
        $isWatchlisted = ($profileId && auth()->check())
            ? WatchlistItem::where([
                'profile_id'     => $profileId,
                'watchable_type' => $movie->getMorphClass(),
                'watchable_id'   => $movie->id,
            ])->exists()
            : false;

        $related = Movie::where('status', 'published')
            ->where('id', '!=', $movie->id)
            ->when($movie->genre, fn ($q) => $q->where('genre', $movie->genre))
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('pages.movies.show', compact('movie', 'isWatchlisted', 'related'));
    }

    public function watch(Movie $movie)
    {
        abort_unless($movie->isPublished() && $movie->videoIsReady(), 404);

        $profileId     = session('active_profile_id');
        $savedPosition = ($profileId && auth()->check())
            ? WatchProgress::where([
                'profile_id'     => $profileId,
                'watchable_type' => 'movie',
                'watchable_id'   => $movie->id,
            ])->value('position')
            : null;

        return view('pages.movies.watch', compact('movie', 'savedPosition'));
    }
}
