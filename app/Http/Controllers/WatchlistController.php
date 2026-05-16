<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;
use App\Models\WatchlistItem;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    private const TYPES = [
        'movie'  => Movie::class,
        'series' => Series::class,
    ];

    public function index(Request $request)
    {
        $items = $request->user()
            ->watchlistItems()
            ->with('watchable.posters')
            ->latest('created_at')
            ->get()
            ->filter(fn ($item) => $item->watchable !== null);

        return view('pages.account.watchlist', compact('items'));
    }

    public function toggle(Request $request, string $type, int $id)
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        $modelClass = self::TYPES[$type];
        $model      = $modelClass::findOrFail($id);
        $user       = $request->user();

        $existing = WatchlistItem::where([
            'user_id'        => $user->id,
            'watchable_type' => $model->getMorphClass(),
            'watchable_id'   => $model->id,
        ])->first();

        if ($existing) {
            $existing->delete();
            $watchlisted = false;
        } else {
            WatchlistItem::create([
                'user_id'        => $user->id,
                'watchable_type' => $model->getMorphClass(),
                'watchable_id'   => $model->id,
                'created_at'     => now(),
            ]);
            $watchlisted = true;
        }

        return response()->json(['watchlisted' => $watchlisted]);
    }
}
