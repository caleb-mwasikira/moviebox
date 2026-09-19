<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

/**
 * Lets the admin preview player report a video that failed to load. The flag is only set
 * when the streaming service is reachable — otherwise the failure is more likely an outage
 * than a broken file.
 */
class VideoHealthController extends Controller
{
    public function reportMovie(Movie $movie)
    {
        return $this->report($movie);
    }

    public function reportEpisode(Episode $episode)
    {
        return $this->report($episode);
    }

    public function clearMovie(Movie $movie)
    {
        return $this->clear($movie);
    }

    public function clearEpisode(Episode $episode)
    {
        return $this->clear($episode);
    }

    private function report(Model $video)
    {
        if (! $video->videoIsReady()) {
            return response()->json(['marked' => false, 'reason' => 'This video has not finished processing.']);
        }

        if (! $this->streamingServiceIsUp()) {
            return response()->json([
                'marked' => false,
                'reason' => 'The streaming service is unreachable, so the video was not flagged.',
            ]);
        }

        $video->forceFill(['is_corrupted' => true, 'corrupted_at' => now()])->save();

        return response()->json(['marked' => true]);
    }

    private function clear(Model $video)
    {
        $video->forceFill(['is_corrupted' => false, 'corrupted_at' => null])->save();

        return response()->json(['marked' => false]);
    }

    private function streamingServiceIsUp(): bool
    {
        try {
            return Http::timeout(3)->get(rtrim(config('services.streaming.url'), '/') . '/status')->ok();
        } catch (\Throwable) {
            return false;
        }
    }
}
