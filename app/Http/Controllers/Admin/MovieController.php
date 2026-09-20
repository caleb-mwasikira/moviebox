<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PlansBackgroundUploads;
use App\Http\Traits\TriggersHlsEncoding;
use App\Jobs\SendNewContentAlerts;
use App\Models\ActivityLog;
use App\Models\Movie;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    use PlansBackgroundUploads;
    use TriggersHlsEncoding;

    public function index(Request $request)
    {
        $trashed = $request->boolean('trashed');

        $movies = $trashed
            ? Movie::onlyTrashed()->latest('deleted_at')->paginate(20)->withQueryString()
            : Movie::with('videoUploads')->latest()->paginate(20)->withQueryString();

        return view('pages.admin.movies.index', compact('movies', 'trashed'));
    }

    public function create()
    {
        return view('pages.admin.movies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'genre'       => ['nullable', 'string', 'max:100'],
            'year'        => ['nullable', 'integer', 'min:1888', 'max:2100'],
            'duration'    => ['nullable', 'integer', 'min:1'],
            'video'       => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska'],
            'trailer'     => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska'],
            'status'      => ['required', 'in:published,hidden'],
            'is_free'     => ['nullable', 'boolean'],
            'posters'     => ['nullable', 'array'],
            'posters.*'   => ['image', 'max:4096'],
            'main_poster' => ['nullable', 'string', 'max:20'],
            'remove_posters'   => ['nullable', 'array'],
            'remove_posters.*' => ['integer'],
        ] + $this->deferredFileRules());

        // The video is optional: it can be sent from the browser after the movie exists (see planUploads)
        // or added later from the edit page.
        $ext       = $request->hasFile('video') ? $request->file('video')->getClientOriginalExtension() : null;
        $uuid      = Str::uuid();
        $finalPath = $ext ? "videos/movies/{$uuid}.{$ext}" : null;

        if ($ext) {
            $request->file('video')->storeAs('videos/movies', "{$uuid}.{$ext}", 'public');
        }

        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'genre'       => $validated['genre'] ?? null,
            'year'        => $validated['year'] ?? null,
            'duration'    => $validated['duration'] ?? null,
            'status'      => $validated['status'],
            'is_free'     => $request->boolean('is_free'),
            'video_path'  => $finalPath,
        ];

        $movie = Movie::create($data);
        ActivityLog::record('created', 'Movie', $movie->id, $movie->title);

        $movie->applyPosterChanges($request, 'posters/movies');

        if ($finalPath && strtolower($ext) === 'mkv') {
            $mp4RelPath = "videos/movies/{$uuid}.mp4";
            $this->convertMkv(
                $movie->uuid,
                storage_path('app/public/' . $finalPath),
                storage_path('app/public/' . $mp4RelPath),
                $mp4RelPath,
                'movies',
            );
        }

        if ($finalPath) {
            $this->convertVideo(
                $movie->uuid,
                'movies',
                $movie->title,
                storage_path('app/public/' . $finalPath),
            );
        }

        if ($request->hasFile('trailer')) {
            $tExt         = $request->file('trailer')->getClientOriginalExtension();
            $tUuid        = Str::uuid();
            $tStagingPath = "videos/staging/{$tUuid}.{$tExt}";
            $request->file('trailer')->storeAs('videos/staging', "{$tUuid}.{$tExt}", 'public');
            $movie->update(['trailer_path' => $tStagingPath]);

            $this->convertVideo(
                $movie->uuid,
                'movies_trailer',
                $movie->title,
                storage_path('app/public/' . $tStagingPath),
            );
        }

        if ($request->expectsJson()) {
            return response()->json([
                'uploads'   => $this->planUploads($request, $movie, [
                    'video'   => route('admin.movies.video', $movie),
                    'trailer' => route('admin.movies.trailer', $movie),
                ]),
                'title'     => $movie->title,
                'edit_url'  => route('admin.movies.edit', $movie),
                'index_url' => route('admin.movies.index'),
            ]);
        }

        // Saved without any video: go straight to the edit page, where it can be added.
        if (! $movie->video_path) {
            return redirect()->route('admin.movies.edit', $movie)->with('status', 'movie-saved');
        }

        return redirect()->route('admin.movies.index')->with('status', 'movie-processing');
    }

    public function edit(Movie $movie)
    {
        $movie->ensureThumbnailIsPoster();
        $movie->load('posters');
        return view('pages.admin.movies.edit', compact('movie'));
    }

    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'genre'       => ['nullable', 'string', 'max:100'],
            'year'        => ['nullable', 'integer', 'min:1888', 'max:2100'],
            'duration'    => ['nullable', 'integer', 'min:1'],
            'video'       => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska'],
            'status'      => ['required', 'in:published,hidden'],
            'is_free'     => ['nullable', 'boolean'],
            'posters'     => ['nullable', 'array'],
            'posters.*'   => ['image', 'max:4096'],
            'main_poster' => ['nullable', 'string', 'max:20'],
            'remove_posters'   => ['nullable', 'array'],
            'remove_posters.*' => ['integer'],
        ]);

        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'genre'       => $validated['genre'] ?? null,
            'year'        => $validated['year'] ?? null,
            'duration'    => $validated['duration'] ?? null,
            'status'      => $validated['status'],
            'is_free'     => $request->boolean('is_free'),
        ];

        $newVideoPath = null;
        if ($request->hasFile('video')) {
            Storage::disk('public')->delete($movie->video_path);

            $ext          = $request->file('video')->getClientOriginalExtension();
            $uuid         = Str::uuid();
            $newVideoPath = "videos/movies/{$uuid}.{$ext}";

            $request->file('video')->storeAs('videos/movies', "{$uuid}.{$ext}", 'public');
            $data['video_path']      = $newVideoPath;
            $data['streaming_path']  = null; // clear old HLS since video is replaced
        }

        $movie->update($data);

        if ($newVideoPath) {
            $this->convertVideo(
                $movie->uuid,
                'movies',
                $movie->title,
                storage_path('app/public/' . $newVideoPath),
            );
        }

        $movie->applyPosterChanges($request, 'posters/movies');

        ActivityLog::record('updated', 'Movie', $movie->id, $movie->title);

        return redirect()->route('admin.movies.edit', $movie)->with('status', 'movie-updated');
    }

    public function reprocess(Request $request, Movie $movie)
    {
        if (!$movie->video_path) {
            if ($request->expectsJson()) return response()->json(['error' => 'No source video file.'], 422);
            return back()->withErrors(['video' => 'No source video file available for re-encoding.']);
        }

        $videoPath = storage_path('app/public/' . $movie->video_path);
        if (!file_exists($videoPath)) {
            if ($request->expectsJson()) return response()->json(['error' => 'Source video file not found on disk: ' . $movie->video_path], 422);
            return back()->withErrors(['video' => 'Source video file not found on disk. It may have been deleted.']);
        }

        $this->cancelJob($movie->uuid);

        $ok = $this->convertVideo($movie->uuid, 'movies', $movie->title, $videoPath);

        if (!$ok) {
            if ($request->expectsJson()) return response()->json(['error' => 'Streaming service unreachable at ' . config('services.streaming.url')], 503);
            return back()->with('error', 'Streaming service is unavailable. Make sure it is running on ' . config('services.streaming.url') . '.');
        }

        if ($request->expectsJson()) return response()->json(['status' => 'encoding']);
        return back()->with('status', 'movie-reprocessing');
    }

    public function encodingStatus(Movie $movie)
    {
        try {
            $response = Http::timeout(5)->get(config('services.streaming.url') . '/status');
            if ($response->ok()) {
                foreach ($response->json('jobs', []) as $job) {
                    if ($job['model_uuid'] === $movie->uuid && $job['film_type'] === 'movies') {
                        return response()->json(['encoding' => true, 'progress' => $job['progress']]);
                    }
                }
            }
        } catch (\Exception) {}

        return response()->json(['encoding' => false, 'progress' => 0]);
    }

    public function reprocessQuality(Request $request, Movie $movie)
    {
        $request->validate(['resolution' => ['required', 'in:360p,480p,720p,1080p']]);

        if (!$movie->video_path) {
            if ($request->expectsJson()) return response()->json(['error' => 'No source video file.'], 422);
            return back()->withErrors(['resolution' => 'No source video file available for re-encoding.']);
        }

        $videoPath = storage_path('app/public/' . $movie->video_path);
        if (!file_exists($videoPath)) {
            if ($request->expectsJson()) return response()->json(['error' => 'Source video file not found on disk: ' . $movie->video_path], 422);
            return back()->withErrors(['resolution' => 'Source video file not found on disk. It may have been deleted.']);
        }

        $this->cancelJob($movie->uuid);

        $ok = $this->convertVideo($movie->uuid, 'movies', $movie->title, $videoPath, null, $request->input('resolution'));

        if (!$ok) {
            if ($request->expectsJson()) return response()->json(['error' => 'Streaming service unreachable at ' . config('services.streaming.url')], 503);
            return back()->with('error', 'Streaming service is unavailable. Make sure it is running on ' . config('services.streaming.url') . '.');
        }

        if ($request->expectsJson()) return response()->json(['status' => 'encoding']);
        return back()->with('status', 'movie-reprocessing');
    }

    public function destroy(Movie $movie)
    {
        $title = $movie->title;

        if (auth()->user()->isAdmin()) {
            $this->deleteMovieFiles($movie);
            $movie->forceDelete();
            ActivityLog::record('force_deleted', 'Movie', null, $title);
        } else {
            $movie->delete();
            ActivityLog::record('deleted', 'Movie', $movie->id, $title);
        }

        return redirect()->route('admin.movies.index')->with('status', 'movie-deleted');
    }

    public function restore(int $id)
    {
        $movie = Movie::withTrashed()->findOrFail($id);
        $movie->restore();
        ActivityLog::record('restored', 'Movie', $movie->id, $movie->title);

        return redirect()->route('admin.trash.index')->with('status', 'movie-restored');
    }

    public function forceDestroy(int $id)
    {
        $movie = Movie::withTrashed()->findOrFail($id);
        $title = $movie->title;
        $this->deleteMovieFiles($movie);
        $movie->forceDelete();
        ActivityLog::record('force_deleted', 'Movie', null, $title);

        return redirect()->route('admin.trash.index')->with('status', 'movie-force-deleted');
    }

    private function deleteMovieFiles(Movie $movie): void
    {
        Storage::disk('public')->delete($movie->video_path);
        if ($movie->thumbnail) {
            Storage::disk('public')->delete($movie->thumbnail);
        }
        foreach ($movie->posters as $poster) {
            Storage::disk('public')->delete($poster->path);
        }
    }

    public function updateVideo(Request $request, Movie $movie)
    {
        $request->validate([
            'video'         => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska,video/mkv,video/matroska'],
            'streaming_url' => ['nullable', 'string', 'max:2048'],
        ]);

        if (!$request->hasFile('video') && !$request->filled('streaming_url')) {
            return back()->withErrors(['video' => 'Provide a video file or a streaming URL.']);
        }

        if ($request->filled('streaming_url')) {
            $movie->update(['streaming_path' => $request->input('streaming_url')]);
            $movie->forceFill(['is_corrupted' => false, 'corrupted_at' => null])->save();
            return redirect()->route('admin.movies.edit', $movie)->with('status', 'streaming-url-saved');
        }

        if ($movie->video_path) {
            Storage::disk('public')->delete($movie->video_path);
        }

        $ext       = $request->file('video')->getClientOriginalExtension();
        $uuid      = Str::uuid();
        $finalPath = "videos/movies/{$uuid}.{$ext}";

        $request->file('video')->storeAs('videos/movies', "{$uuid}.{$ext}", 'public');

        $movie->update([
            'video_path'     => $finalPath,
            'streaming_path' => null,
        ]);
        $movie->forceFill(['is_corrupted' => false, 'corrupted_at' => null])->save();

        $this->cancelJob($movie->uuid);

        if (strtolower($ext) === 'mkv') {
            $mp4RelPath = "videos/movies/{$uuid}.mp4";
            $this->convertMkv(
                $movie->uuid,
                storage_path('app/public/' . $finalPath),
                storage_path('app/public/' . $mp4RelPath),
                $mp4RelPath,
                'movies',
            );
        }

        $this->convertVideo(
            $movie->uuid,
            'movies',
            $movie->title,
            storage_path('app/public/' . $finalPath),
        );

        return redirect()->route('admin.movies.edit', $movie)->with('status', 'movie-processing');
    }

    public function uploadTrailer(Request $request, Movie $movie)
    {
        $request->validate([
            'trailer' => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska,video/mkv,video/matroska'],
        ]);

        if ($movie->trailer_path && !str_starts_with($movie->trailer_path, 'http')) {
            Storage::disk('public')->delete($movie->trailer_path);
        }

        $ext         = $request->file('trailer')->getClientOriginalExtension();
        $uuid        = Str::uuid();
        $stagingPath = "videos/staging/{$uuid}.{$ext}";

        $request->file('trailer')->storeAs('videos/staging', "{$uuid}.{$ext}", 'public');
        $movie->update(['trailer_path' => $stagingPath]);

        $this->convertVideo(
            $movie->uuid,
            'movies_trailer',
            $movie->title,
            storage_path('app/public/' . $stagingPath)
        );

        return redirect()->route('admin.movies.edit', $movie)->with('status', 'trailer-processing');
    }

    public function toggleStatus(Movie $movie)
    {
        $newStatus = $movie->status === 'published' ? 'hidden' : 'published';
        $movie->update(['status' => $newStatus]);

        if ($newStatus === 'published') {
            SendNewContentAlerts::dispatch(
                title: $movie->title,
                type:  'movie',
                url:   route('movies.show', $movie),
                genre: $movie->genre,
                year:  $movie->year,
            );
        }

        return back();
    }

    public function toggleFeatured(Movie $movie)
    {
        $movie->update(['featured' => !$movie->featured]);

        return back();
    }
}
