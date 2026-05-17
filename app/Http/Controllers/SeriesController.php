<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Series;
use App\Models\WatchlistItem;
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

        if ($request->boolean('free')) {
            $query->whereHas('episodes', fn ($q) => $q->where('is_free', true));
        }

        $series = $query
            ->withCount(['episodes as free_episodes_count' => fn ($q) => $q->where('is_free', true)])
            ->latest()->paginate(12)->withQueryString();

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

        $profileId     = session('active_profile_id');
        $isWatchlisted = ($profileId && auth()->check())
            ? WatchlistItem::where([
                'profile_id'     => $profileId,
                'watchable_type' => $series->getMorphClass(),
                'watchable_id'   => $series->id,
            ])->exists()
            : false;

        $related = Series::where('status', 'published')
            ->where('id', '!=', $series->id)
            ->when($series->genre, fn ($q) => $q->where('genre', $series->genre))
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('pages.series.show', compact('series', 'isWatchlisted', 'related'));
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
