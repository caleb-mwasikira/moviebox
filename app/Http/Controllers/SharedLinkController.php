<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;
use App\Models\SharedLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SharedLinkController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'shareable_type' => ['required', 'in:movie,series'],
            'shareable_id'   => ['required', 'integer'],
        ]);

        $user  = $request->user();
        $limit = $user->maxSharedLinks();

        /** @var Movie|Series $model */
        $model = match ($request->shareable_type) {
            'movie'  => Movie::findOrFail($request->shareable_id),
            'series' => Series::findOrFail($request->shareable_id),
        };

        // Check if this exact content is already shared — reusing doesn't count against the limit.
        $existing = SharedLink::where('user_id', $user->id)
            ->where('shareable_type', $request->shareable_type)
            ->where('shareable_id', $model->id)
            ->first();

        if (! $existing && $limit > 0) {
            $count = SharedLink::where('user_id', $user->id)->count();
            if ($count >= $limit) {
                return response()->json([
                    'error' => "You've reached the shared links limit ({$limit}) for your plan.",
                ], 422);
            }
        }

        $shared = $existing ?? SharedLink::create([
            'user_id'        => $user->id,
            'shareable_type' => $request->shareable_type,
            'shareable_id'   => $model->id,
            'token'          => Str::random(12),
        ]);

        return response()->json(['url' => route('share.show', $shared->token)]);
    }

    public function show(SharedLink $sharedLink)
    {
        $type   = $sharedLink->shareable_type;
        $sharer = $sharedLink->user()->with('currentSubscription')->first();

        /** @var Movie|Series $model */
        $model = match ($type) {
            'movie'  => Movie::findOrFail($sharedLink->shareable_id),
            'series' => Series::findOrFail($sharedLink->shareable_id),
            default  => abort(404),
        };

        if ($sharer && $sharer->currentSubscription) {
            $expiresAt = now()->addHours(6);

            if (! Auth::check()) {
                // Log the guest in as the sharer so they can watch using the sharer's subscription.
                Auth::login($sharer, false);
                session([
                    'shared_guest'         => true,
                    'shared_guest_expires' => $expiresAt->timestamp,
                    'shared_guest_name'    => $sharer->name,
                ]);
            }

            // Keep the session grant so already-logged-in non-subscribers also get access.
            session(['shared_access.' . $type . '.' . $model->id => $expiresAt->timestamp]);
        }

        return match ($type) {
            'movie'  => redirect()->route('movies.show', $model),
            'series' => redirect()->route('series.show', $model),
        };
    }
}
