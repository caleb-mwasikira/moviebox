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

        $model = match ($data['type']) {
            'movie'   => Movie::where('uuid', $data['uuid'])->firstOrFail(),
            'episode' => Episode::where('uuid', $data['uuid'])->firstOrFail(),
        };

        WatchProgress::updateOrCreate(
            [
                'user_id'        => $request->user()->id,
                'watchable_type' => $data['type'],
                'watchable_id'   => $model->id,
            ],
            [
                'position'   => $data['position'],
                'duration'   => $data['duration'],
                'watched_at' => now(),
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function index(Request $request)
    {
        $items = WatchProgress::where('user_id', $request->user()->id)
            ->where('position', '>=', 30)
            ->orderByDesc('watched_at')
            ->limit(24)
            ->get();

        $items->loadMorph('watchable', [
            Movie::class   => ['posters'],
            Episode::class => ['season.series.posters'],
        ]);

        $items = $items->filter(fn($item) => $item->watchable !== null)->values();

        return view('pages.account.watching', compact('items'));
    }
}
