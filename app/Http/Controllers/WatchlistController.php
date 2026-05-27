<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Movie;
use App\Models\Series;
use App\Models\WatchlistItem;
use App\Models\WatchProgress;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    private const TYPES = [
        'movie'  => Movie::class,
        'series' => Series::class,
    ];

    public function index(Request $request)
    {
        $profileId = session('active_profile_id');

        $items = $profileId
            ? WatchlistItem::where('profile_id', $profileId)
                ->with('watchable.posters')
                ->latest('created_at')
                ->get()
                ->filter(fn($item) => $item->watchable !== null)
            : collect();

        $historyQuery = $profileId
            ? WatchProgress::where('profile_id', $profileId)
            : WatchProgress::where('user_id', $request->user()->id)->whereNull('profile_id');

        $history = $historyQuery
            ->where('position', '>=', 30)
            ->orderByDesc('watched_at')
            ->limit(300)
            ->get();

        $history->loadMorph('watchable', [
            Movie::class   => ['posters'],
            Episode::class => ['season.series.posters'],
        ]);

        $history = $history
            ->filter(fn($item) => $item->watchable !== null)
            ->unique(fn($item) => $item->watchable_type === 'movie'
                ? 'movie_' . $item->watchable_id
                : 'series_' . $item->watchable->season->series->id)
            ->sortByDesc('watched_at')
            ->values();

        return view('pages.account.watchlist', compact('items', 'history'));
    }

    public function toggle(Request $request, string $type, int $id)
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        $profileId = session('active_profile_id');
        abort_unless($profileId, 422);

        $model      = self::TYPES[$type]::findOrFail($id);
        $morphClass = $model->getMorphClass();

        $existing = WatchlistItem::where([
            'profile_id'     => $profileId,
            'watchable_type' => $morphClass,
            'watchable_id'   => $model->id,
        ])->first();

        if ($existing) {
            $existing->delete();
            $watchlisted = false;
        } else {
            WatchlistItem::create([
                'user_id'        => $request->user()->id,
                'profile_id'     => $profileId,
                'watchable_type' => $morphClass,
                'watchable_id'   => $model->id,
                'created_at'     => now(),
            ]);
            $watchlisted = true;
        }

        return response()->json(['watchlisted' => $watchlisted]);
    }
}
