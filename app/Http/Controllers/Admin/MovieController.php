<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\TriggersHlsEncoding;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    use TriggersHlsEncoding;

    public function index()
    {
        $movies = Movie::latest()->paginate(20);
        return view('pages.admin.movies.index', compact('movies'));
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
            'thumbnail'   => ['nullable', 'image', 'max:2048'],
            'video'       => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska'],
            'trailer'     => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska'],
            'status'      => ['required', 'in:published,hidden'],
            'posters'     => ['nullable', 'array'],
            'posters.*'   => ['image', 'max:4096'],
        ]);

        $ext       = $request->file('video')->getClientOriginalExtension();
        $uuid      = Str::uuid();
        $finalPath = "videos/movies/{$uuid}.{$ext}";

        $request->file('video')->storeAs('videos/movies', "{$uuid}.{$ext}", 'public');

        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'genre'       => $validated['genre'] ?? null,
            'year'        => $validated['year'] ?? null,
            'duration'    => $validated['duration'] ?? null,
            'status'      => $validated['status'],
            'video_path'  => $finalPath,
        ];

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails/movies', 'public');
        }

        $movie = Movie::create($data);

        foreach ($request->file('posters', []) as $i => $file) {
            $movie->posters()->create([
                'path'       => $file->store('posters/movies', 'public'),
                'sort_order' => $i,
            ]);
        }

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

        return redirect()->route('admin.movies.index')->with('status', 'movie-processing');
    }

    public function edit(Movie $movie)
    {
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
            'thumbnail'   => ['nullable', 'image', 'max:2048'],
            'video'       => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska'],
            'status'      => ['required', 'in:published,hidden'],
            'posters'     => ['nullable', 'array'],
            'posters.*'   => ['image', 'max:4096'],
        ]);

        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'genre'       => $validated['genre'] ?? null,
            'year'        => $validated['year'] ?? null,
            'duration'    => $validated['duration'] ?? null,
            'status'      => $validated['status'],
        ];

        if ($request->hasFile('thumbnail')) {
            if ($movie->thumbnail) {
                Storage::disk('public')->delete($movie->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails/movies', 'public');
        }

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

        $offset = $movie->posters()->max('sort_order') ?? -1;
        foreach ($request->file('posters', []) as $i => $file) {
            $movie->posters()->create([
                'path'       => $file->store('posters/movies', 'public'),
                'sort_order' => $offset + $i + 1,
            ]);
        }

        return redirect()->route('admin.movies.edit', $movie)->with('status', 'movie-updated');
    }

    public function reprocess(Movie $movie)
    {
        $this->cancelJob($movie->uuid);

        $ok = $this->convertVideo(
            $movie->uuid,
            'movies',
            $movie->title,
            storage_path('app/public/' . $movie->video_path),
        );

        if (!$ok) {
            return back()->with('error', 'Streaming service is unavailable. Make sure it is running on ' . env('STREAMING_SERVICE_URL', 'http://127.0.0.1:9090') . '.');
        }

        return back()->with('status', 'movie-reprocessing');
    }

    public function destroy(Movie $movie)
    {
        Storage::disk('public')->delete($movie->video_path);
        if ($movie->thumbnail) {
            Storage::disk('public')->delete($movie->thumbnail);
        }
        foreach ($movie->posters as $poster) {
            Storage::disk('public')->delete($poster->path);
        }
        $movie->delete();

        return redirect()->route('admin.movies.index')->with('status', 'movie-deleted');
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
        $movie->update([
            'status' => $movie->status === 'published' ? 'hidden' : 'published',
        ]);

        return back();
    }

    public function toggleFeatured(Movie $movie)
    {
        $movie->update(['featured' => !$movie->featured]);

        return back();
    }
}
