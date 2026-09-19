<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\TriggersHlsEncoding;
use App\Models\ActivityLog;
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
            'is_free'        => ['nullable', 'boolean'],
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
            'is_free'        => $request->boolean('is_free'),
            'video_path'     => $finalPath,
        ]);

        $series = $season->series;

        if (strtolower($ext) === 'mkv') {
            $mp4RelPath = "videos/series/{$uuid}.mp4";
            $this->convertMkv(
                $episode->uuid,
                storage_path('app/public/' . $finalPath),
                storage_path('app/public/' . $mp4RelPath),
                $mp4RelPath,
                'series',
            );
        }

        $this->convertVideo(
            $episode->uuid,
            'series',
            $episode->label(),
            storage_path('app/public/' . $finalPath),
            $series->title,
        );

        ActivityLog::record('created', 'Episode', $episode->id, $episode->label() . ' — ' . $series->title);

        return redirect()
            ->route('admin.series.edit', $series)
            ->with('status', 'episode-processing');
    }

    public function toggleFree(Episode $episode)
    {
        $episode->update(['is_free' => !$episode->is_free]);
        return response()->json(['is_free' => $episode->is_free]);
    }

    public function update(Request $request, Episode $episode)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration'    => ['nullable', 'integer', 'min:1'],
            'is_free'     => ['nullable', 'boolean'],
        ]);

        $episode->update([
            'title'       => $request->input('title'),
            'description' => $request->input('description'),
            'duration'    => $request->input('duration'),
            'is_free'     => $request->boolean('is_free'),
        ]);

        return redirect()
            ->route('admin.series.edit', $episode->season->series)
            ->with('status', 'episode-saved');
    }

    public function updateVideo(Request $request, Episode $episode)
    {
        $request->validate([
            'video'         => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska,video/mkv,video/matroska'],
            'streaming_url' => ['nullable', 'string', 'max:2048'],
        ]);

        if (!$request->hasFile('video') && !$request->filled('streaming_url')) {
            return back()->withErrors(['video' => 'Provide a video file or a streaming URL.']);
        }

        $series = $episode->season->series;

        if ($request->filled('streaming_url')) {
            $episode->update(['streaming_path' => $request->input('streaming_url')]);
            $episode->forceFill(['is_corrupted' => false, 'corrupted_at' => null])->save();
            return redirect()->route('admin.series.edit', $series)->with('status', 'streaming-url-saved');
        }

        if ($episode->video_path) {
            Storage::disk('public')->delete($episode->video_path);
        }

        $ext       = $request->file('video')->getClientOriginalExtension();
        $uuid      = Str::uuid();
        $finalPath = "videos/series/{$uuid}.{$ext}";

        $request->file('video')->storeAs('videos/series', "{$uuid}.{$ext}", 'public');

        $episode->update([
            'video_path'     => $finalPath,
            'streaming_path' => null,
        ]);
        $episode->forceFill(['is_corrupted' => false, 'corrupted_at' => null])->save();

        $this->cancelJob($episode->uuid);

        if (strtolower($ext) === 'mkv') {
            $mp4RelPath = "videos/series/{$uuid}.mp4";
            $this->convertMkv(
                $episode->uuid,
                storage_path('app/public/' . $finalPath),
                storage_path('app/public/' . $mp4RelPath),
                $mp4RelPath,
                'series',
            );
        }

        $this->convertVideo(
            $episode->uuid,
            'series',
            $episode->label(),
            storage_path('app/public/' . $finalPath),
            $series->title,
        );

        return redirect()
            ->route('admin.series.edit', $series)
            ->with('status', 'episode-processing');
    }

    public function reprocessAll(Episode $episode)
    {
        if (!$episode->video_path) {
            return back()->withErrors(['video' => 'No source video file available for re-encoding.']);
        }

        $videoPath = storage_path('app/public/' . $episode->video_path);
        if (!file_exists($videoPath)) {
            return back()->withErrors(['video' => 'Source video file not found on disk (' . $episode->video_path . '). It may have been deleted.']);
        }

        $series = $episode->season->series;

        $this->cancelJob($episode->uuid);

        $ok = $this->convertVideo($episode->uuid, 'series', $episode->label(), $videoPath, $series->title);

        if (!$ok) {
            return back()->with('error', 'Streaming service is unavailable. Make sure it is running on ' . config('services.streaming.url') . '.');
        }

        return redirect()->route('admin.series.edit', $series)->with('status', 'episode-reprocessing');
    }

    public function reprocessQuality(Request $request, Episode $episode)
    {
        $request->validate(['resolution' => ['required', 'in:360p,480p,720p,1080p']]);

        if (!$episode->video_path) {
            return back()->withErrors(['resolution' => 'No source video file available for re-encoding.']);
        }

        $videoPath = storage_path('app/public/' . $episode->video_path);
        if (!file_exists($videoPath)) {
            return back()->withErrors(['resolution' => 'Source video file not found on disk (' . $episode->video_path . '). It may have been deleted.']);
        }

        $series = $episode->season->series;

        $this->cancelJob($episode->uuid);

        $ok = $this->convertVideo($episode->uuid, 'series', $episode->label(), $videoPath, $series->title, $request->input('resolution'));

        if (!$ok) {
            return back()->with('error', 'Streaming service is unavailable. Make sure it is running on ' . config('services.streaming.url') . '.');
        }

        return redirect()->route('admin.series.edit', $series)->with('status', 'episode-reprocessing');
    }

    public function destroy(Episode $episode)
    {
        $series = $episode->season->series;
        $label  = $episode->label() . ' — ' . $series->title;
        Storage::disk('public')->delete($episode->video_path);
        $episode->delete();
        ActivityLog::record('deleted', 'Episode', null, $label);

        return redirect()
            ->route('admin.series.edit', $series)
            ->with('status', 'episode-deleted');
    }
}
