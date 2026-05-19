<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Movie;
use App\Models\WatchProgress;
use Illuminate\Http\Request;

class WatchProgressController extends Controller
{
    public function upsert(Request $request)
    {
        $data = $request->validate([
            'type'     => ['required', 'in:movie,episode'],
            'uuid'     => ['required', 'string'],
            'position' => ['required', 'numeric', 'min:0'],
            'duration' => ['required', 'numeric', 'min:0'],
        ]);

        $profileId = session('active_profile_id');

        if (! $profileId) {
            return response()->json(['ok' => false, 'reason' => 'no_profile'], 422);
        }

        $model = match ($data['type']) {
            'movie'   => Movie::where('uuid', $data['uuid'])->firstOrFail(),
            'episode' => Episode::where('uuid', $data['uuid'])->firstOrFail(),
        };

        WatchProgress::updateOrCreate(
            [
                'profile_id'     => $profileId,
                'watchable_type' => $data['type'],
                'watchable_id'   => $model->id,
            ],
            [
                'user_id'    => $request->user()->id,
                'position'   => $data['position'],
                'duration'   => $data['duration'],
                'watched_at' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function index(Request $request)
    {
        $profileId = session('active_profile_id');

        $query = $profileId
            ? WatchProgress::where('profile_id', $profileId)
            : WatchProgress::where('user_id', $request->user()->id)->whereNull('profile_id');

        $items = $query
            ->where('position', '>=', 30)
            ->orderByDesc('position')
            ->limit(300)
            ->get();

        $items->loadMorph('watchable', [
            Movie::class   => ['posters'],
            Episode::class => ['season.series.posters'],
        ]);

        $items = $items
            ->filter(fn($item) => $item->watchable !== null)
            ->unique(fn($item) => $item->watchable_type === 'movie'
                ? 'movie_' . $item->watchable_id
                : 'series_' . $item->watchable->season->series->id)
            ->sortByDesc('watched_at')
            ->values();

        return view('pages.account.watching', compact('items'));
    }
}
