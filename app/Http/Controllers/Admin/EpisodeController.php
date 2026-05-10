<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\TriggersHlsEncoding;
use App\Models\Episode;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EpisodeController extends Controller
{
    use TriggersHlsEncoding;

    public function store(Request $request, Season $season)
    {
        $validated = $request->validate([
            'episode_number' => ['required', 'integer', 'min:1'],
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'duration'       => ['nullable', 'integer', 'min:1'],
            'video'          => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska'],
        ]);

        $ext       = $request->file('video')->getClientOriginalExtension();
        $uuid      = Str::uuid();
        $finalPath = "videos/series/{$uuid}.{$ext}";

        $request->file('video')->storeAs('videos/series', "{$uuid}.{$ext}", 'public');

        $episode = $season->episodes()->create([
            'episode_number' => $validated['episode_number'],
            'title'          => $validated['title'],
            'description'    => $validated['description'] ?? null,
            'duration'       => $validated['duration'] ?? null,
            'video_path'     => $finalPath,
        ]);

        $series = $season->series;

        $this->convertVideo(
            $episode->uuid,
            'series',
            $series->title . ' - ep. ' . $validated['episode_number'],
            storage_path('app/public/' . $finalPath),
        );

        return redirect()
            ->route('admin.series.edit', $series)
            ->with('status', 'episode-processing');
    }

    public function destroy(Episode $episode)
    {
        $series = $episode->season->series;
        Storage::disk('public')->delete($episode->video_path);
        $episode->delete();

        return redirect()
            ->route('admin.series.edit', $series)
            ->with('status', 'episode-deleted');
    }
}
