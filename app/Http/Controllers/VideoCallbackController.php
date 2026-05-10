<?php

namespace App\Http\Controllers;

use App\Events\VideoEncodingProgress;
use App\Events\VideoReady;
use App\Events\VideoStreamingReady;
use App\Models\Episode;
use App\Models\Movie;
use Illuminate\Http\Request;

class VideoCallbackController extends Controller
{
    public function streamingReady(Request $request)
    {
        $adminId  = (int) $request->query('admin_id');
        $label    = $request->query('label', 'Video');
        $editUrl  = $request->query('edit_url', '/admin');
        $filmType = $request->query('film_type', '');
        $modelId  = (int) $request->query('model_id', 0);

        $seriesId      = null;
        $streamingPath = '';

        if ($filmType === 'episodes' && $modelId > 0) {
            $episode       = Episode::with('season:id,series_id')->find($modelId, ['id', 'season_id', 'streaming_path']);
            $seriesId      = $episode?->season?->series_id;
            $streamingPath = $episode?->streamingUrl() ?? '';
        } elseif ($filmType === 'movies' && $modelId > 0) {
            $streamingPath = Movie::find($modelId, ['id', 'streaming_path'])?->streamingUrl() ?? '';
        }

        if ($adminId > 0) {
            broadcast(new VideoStreamingReady($adminId, $label, $editUrl, $filmType, $modelId, $seriesId, $streamingPath));
        }

        return response()->json(['ok' => true]);
    }

    public function encodingProgress(Request $request)
    {
        $adminId    = (int) $request->query('admin_id');
        $filmType   = $request->input('film_type', '');
        $modelId    = (int) $request->input('model_id', 0);
        $percentage = (int) $request->input('percentage', 0);

        $seriesId = null;
        if ($filmType === 'episodes' && $modelId > 0) {
            $episode  = Episode::with('season:id,series_id')->find($modelId, ['id', 'season_id']);
            $seriesId = $episode?->season?->series_id;
        }

        if ($adminId > 0 && $filmType && $modelId > 0) {
            broadcast(new VideoEncodingProgress($adminId, $filmType, $modelId, $percentage, $seriesId));
        }

        return response()->json(['ok' => true]);
    }

    public function conversionReady(Request $request)
    {
        $adminId = (int) $request->query('admin_id');
        $label   = $request->query('label', 'Video');
        $editUrl = $request->query('edit_url', '/admin');

        if ($adminId > 0) {
            broadcast(new VideoReady($adminId, $label, $editUrl));
        }

        return response()->json(['ok' => true]);
    }
}
