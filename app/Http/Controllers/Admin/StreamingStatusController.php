<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use Illuminate\Support\Facades\Http;

class StreamingStatusController extends Controller
{
    public function index()
    {
        $serviceUrl = env('STREAMING_SERVICE_URL', 'http://127.0.0.1:9090');

        try {
            $response = Http::timeout(5)->get($serviceUrl . '/status');
            $jobs     = collect($response->json('jobs', []));
        } catch (\Exception $e) {
            return response()->json(['jobs' => []]);
        }

        // Enrich episode jobs with their parent series_id so the frontend can
        // map them to the correct series row without a separate lookup.
        $episodeIds = $jobs->where('film_type', 'episodes')->pluck('model_id')->all();
        $seriesMap  = [];
        if ($episodeIds) {
            Episode::with('season:id,series_id')
                ->whereIn('id', $episodeIds)
                ->get(['id', 'season_id'])
                ->each(function ($ep) use (&$seriesMap) {
                    $seriesMap[$ep->id] = $ep->season?->series_id;
                });
        }

        $enriched = $jobs->map(function ($job) use ($seriesMap) {
            $job['series_id'] = ($job['film_type'] === 'episodes' && isset($seriesMap[$job['model_id']]))
                ? $seriesMap[$job['model_id']]
                : null;
            return $job;
        });

        return response()->json(['jobs' => $enriched->values()]);
    }
}
