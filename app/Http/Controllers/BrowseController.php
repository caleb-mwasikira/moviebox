<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function suggestions(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $like = "%{$q}%";
        $results = collect();

        Movie::where('status', 'published')->where('coming_soon', false)
            ->where(fn($query) => $query
                ->where('title', 'ilike', $like)
                ->orWhere('genre', 'ilike', $like)
                ->orWhere('description', 'ilike', $like))
            ->latest()->take(4)->get()
            ->each(fn($m) => $results->push([
                'type'  => 'movie',
                'label' => 'Movie',
                'title' => $m->title,
                'genre' => $m->genre,
                'thumb' => $m->thumbnailUrl(),
                'url'   => route('movies.show', $m),
            ]));

        Series::where('status', 'published')
            ->where(fn($query) => $query
                ->where('title', 'ilike', $like)
                ->orWhere('genre', 'ilike', $like)
                ->orWhere('description', 'ilike', $like))
            ->latest()->take(3)->get()
            ->each(fn($s) => $results->push([
                'type'  => 'series',
                'label' => 'Series',
                'title' => $s->title,
                'genre' => $s->genre,
                'thumb' => $s->thumbnailUrl(),
                'url'   => route('series.show', $s),
            ]));

        Movie::where('coming_soon', true)
            ->where(fn($query) => $query
                ->where('title', 'ilike', $like)
                ->orWhere('genre', 'ilike', $like)
                ->orWhere('description', 'ilike', $like))
            ->latest()->take(4)->get()
            ->each(fn($c) => $results->push([
                'type'  => 'coming_soon',
                'label' => 'Coming Soon',
                'title' => $c->title,
                'genre' => $c->genre,
                'thumb' => $c->thumbnailUrl(),
                'url'   => route('coming-soon.show', $c),
            ]));

        return response()->json($results->values());
    }

    public function index(Request $request)
    {
        $type  = $request->input('type', 'all');
        $genre = $request->input('genre');
        $query = trim($request->input('q', ''));

        $movieQuery  = Movie::where('status', 'published')->where('coming_soon', false);
        $seriesQuery = Series::where('status', 'published');

        if ($genre) {
            $movieQuery->where('genre', $genre);
            $seriesQuery->where('genre', $genre);
        }

        if ($query) {
            $movieQuery->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                  ->orWhere('description', 'ilike', "%{$query}%")
                  ->orWhere('genre', 'ilike', "%{$query}%");
            });
            $seriesQuery->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                  ->orWhere('description', 'ilike', "%{$query}%")
                  ->orWhere('genre', 'ilike', "%{$query}%");
            });
        }

        $movies = in_array($type, ['all', 'movies'])
            ? $movieQuery->latest()->get()
            : collect();

        $series = in_array($type, ['all', 'series'])
            ? $seriesQuery->latest()->get()
            : collect();

        // Coming soon only surfaces when searching with type=all
        $comingSoon = collect();
        if ($query && $type === 'all') {
            $csQuery = Movie::where('coming_soon', true)
                ->where(function ($q) use ($query) {
                    $q->where('title', 'ilike', "%{$query}%")
                      ->orWhere('description', 'ilike', "%{$query}%")
                      ->orWhere('genre', 'ilike', "%{$query}%");
                });
            if ($genre) {
                $csQuery->where('genre', $genre);
            }
            $comingSoon = $csQuery->with('posters')->latest()->get();
        }

        $genres = Movie::where('status', 'published')->where('coming_soon', false)->distinct()->pluck('genre')
            ->merge(Series::where('status', 'published')->distinct()->pluck('genre'))
            ->filter()->unique()->sort()->values();

        return view('pages.browse', compact('movies', 'series', 'comingSoon', 'genres', 'type', 'genre', 'query'));
    }
}
