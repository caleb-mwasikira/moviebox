<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Series;
use App\Models\WatchProgress;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index(Request $request)
    {
        $query = Series::where('status', 'published');

        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }

        $series = $query->latest()->paginate(12)->withQueryString();
        $genres = Series::where('status', 'published')
            ->distinct()
            ->orderBy('genre')
            ->pluck('genre')
            ->filter()
            ->values();

        return view('pages.series.index', compact('series', 'genres'));
    }

    public function show(Series $series)
    {
        abort_unless($series->isPublished(), 404);
        $series->load(['seasons.episodes', 'posters']);
        return view('pages.series.show', compact('series'));
    }

    public function watch(Series $series, Episode $episode)
    {
        abort_unless($series->isPublished(), 404);
        abort_unless($episode->videoIsReady(), 404);
        abort_unless($episode->season->series_id === $series->id, 404);

        $series->load('seasons.episodes');

        $allEpisodes  = $series->seasons->flatMap(fn($s) => $s->episodes);
        $currentIndex = $allEpisodes->search(fn($e) => $e->id === $episode->id);
        $prev         = $currentIndex > 0 ? $allEpisodes[$currentIndex - 1] : null;
        $next         = $currentIndex < $allEpisodes->count() - 1 ? $allEpisodes[$currentIndex + 1] : null;

        $savedPosition = auth()->check()
            ? WatchProgress::where([
                'user_id'        => auth()->id(),
                'watchable_type' => 'episode',
                'watchable_id'   => $episode->id,
            ])->value('position')
            : null;

        return view('pages.series.watch', compact('series', 'episode', 'allEpisodes', 'prev', 'next', 'savedPosition'));
    }
}
