<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessVideoUpload;
use App\Models\Episode;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EpisodeController extends Controller
{
    public function store(Request $request, Season $season)
    {
        $validated = $request->validate([
            'episode_number' => ['required', 'integer', 'min:1'],
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'duration'       => ['nullable', 'integer', 'min:1'],
            'video'          => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/ogg', 'max:2097152'],
        ]);

        $ext         = $request->file('video')->getClientOriginalExtension();
        $uuid        = Str::uuid();
        $stagingPath = "videos/staging/{$uuid}.{$ext}";
        $finalPath   = "videos/series/{$uuid}.{$ext}";

        $request->file('video')->storeAs('videos/staging', "{$uuid}.{$ext}", 'public');

        $episode = $season->episodes()->create([
            'episode_number' => $validated['episode_number'],
            'title'          => $validated['title'],
            'description'    => $validated['description'] ?? null,
            'duration'       => $validated['duration'] ?? null,
            'video_path'     => $stagingPath,
        ]);

        $seriesId = $season->series_id;
        $label    = "S{$season->season_number}E{$episode->episode_number} — {$episode->title}";

        ProcessVideoUpload::dispatch(
            Episode::class,
            $episode->id,
            $stagingPath,
            $finalPath,
            auth()->id(),
            $label,
            route('admin.series.edit', $seriesId),
        );

        return redirect()
            ->route('admin.series.edit', $seriesId)
            ->with('status', 'episode-processing');
    }

    public function destroy(Episode $episode)
    {
        $seriesId = $episode->season->series_id;
        Storage::disk('public')->delete($episode->video_path);
        $episode->delete();

        return redirect()
            ->route('admin.series.edit', $seriesId)
            ->with('status', 'episode-deleted');
    }
}
