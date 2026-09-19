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
                ->whereLike('title', $like)
                ->orWhereLike('genre', $like)
                ->orWhereLike('description', $like))
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
                ->whereLike('title', $like)
                ->orWhereLike('genre', $like)
                ->orWhereLike('description', $like))
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
                ->whereLike('title', $like)
                ->orWhereLike('genre', $like)
                ->orWhereLike('description', $like))
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

    private const PER_PAGE = 24;

    public function index(Request $request)
    {
        $type    = $request->input('type', 'all');
        $genre   = $request->input('genre');
        $query   = trim($request->input('q', ''));
        $page    = max(1, (int) $request->input('page', 1));
        $perPage = self::PER_PAGE;

        $movieQuery  = Movie::where('status', 'published')->where('coming_soon', false);
        $seriesQuery = Series::where('status', 'published');

        if ($genre) {
            $movieQuery->where('genre', $genre);
            $seriesQuery->where('genre', $genre);
        }

        if ($query) {
            $movieQuery->where(fn($q) => $q
                ->whereLike('title', "%{$query}%")
                ->orWhereLike('description', "%{$query}%")
                ->orWhereLike('genre', "%{$query}%"));
            $seriesQuery->where(fn($q) => $q
                ->whereLike('title', "%{$query}%")
                ->orWhereLike('description', "%{$query}%")
                ->orWhereLike('genre', "%{$query}%"));
        }

        // Build combined sorted list, then paginate in PHP
        $all = collect();

        if (in_array($type, ['all', 'movies'])) {
            $movieQuery->latest()->get()
                ->each(fn($m) => $all->push(['type' => 'movie', 'item' => $m, 'ts' => $m->created_at]));
        }
        if (in_array($type, ['all', 'series'])) {
            $seriesQuery->latest()->get()
                ->each(fn($s) => $all->push(['type' => 'series', 'item' => $s, 'ts' => $s->created_at]));
        }

        if ($type === 'all') {
            $all = $all->sortByDesc('ts')->values();
        }

        $total   = $all->count();
        $paged   = $all->forPage($page, $perPage);
        $hasMore = ($page * $perPage) < $total;

        // AJAX — return JSON for infinite-scroll requests
        if ($request->expectsJson()) {
            return response()->json([
                'items'   => $paged->map(fn($e) => $this->itemJson($e))->values(),
                'hasMore' => $hasMore,
                'page'    => $page,
            ]);
        }

        // Coming soon only surfaces when searching with type=all
        $comingSoon = collect();
        if ($query && $type === 'all') {
            $csQuery = Movie::where('coming_soon', true)
                ->where(fn($q) => $q
                    ->whereLike('title', "%{$query}%")
                    ->orWhereLike('description', "%{$query}%")
                    ->orWhereLike('genre', "%{$query}%"));
            if ($genre) {
                $csQuery->where('genre', $genre);
            }
            $comingSoon = $csQuery->with('posters')->latest()->get();
        }

        $genres = Movie::where('status', 'published')->where('coming_soon', false)->distinct()->pluck('genre')
            ->merge(Series::where('status', 'published')->distinct()->pluck('genre'))
            ->filter()->unique()->sort()->values();

        return view('pages.browse', compact('paged', 'comingSoon', 'genres', 'type', 'genre', 'query', 'hasMore'));
    }

    private function itemJson(array $entry): array
    {
        $item = $entry['item'];
        return [
            'type'         => $entry['type'],
            'title'        => $item->title,
            'url'          => $entry['type'] === 'movie'
                ? route('movies.show', $item)
                : route('series.show', $item),
            'thumbnailUrl' => $item->thumbnailUrl(),
            'genre'        => $item->genre,
            'year'         => $item->year,
            'isFree'       => $entry['type'] === 'movie' && ($item->is_free ?? false),
        ];
    }
}
