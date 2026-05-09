<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessVideoUpload;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class MovieController extends Controller
{
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
            'video'       => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/ogg'],
            'status'      => ['required', 'in:published,hidden'],
            'posters'     => ['nullable', 'array'],
            'posters.*'   => ['image', 'max:4096'],
        ]);

        $ext         = $request->file('video')->getClientOriginalExtension();
        $uuid        = Str::uuid();
        $stagingPath = "videos/staging/{$uuid}.{$ext}";
        $finalPath   = "videos/movies/{$uuid}.{$ext}";

        $request->file('video')->storeAs('videos/staging', "{$uuid}.{$ext}", 'public');

        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'genre'       => $validated['genre'] ?? null,
            'year'        => $validated['year'] ?? null,
            'duration'    => $validated['duration'] ?? null,
            'status'      => $validated['status'],
            'video_path'  => $stagingPath,
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

        ProcessVideoUpload::dispatch(
            Movie::class,
            $movie->id,
            $stagingPath,
            $finalPath,
            auth()->id(),
            $movie->title,
            route('admin.movies.edit', $movie),
        );

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
            'video'       => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg'],
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

        if ($request->hasFile('video')) {
            Storage::disk('public')->delete($movie->video_path);

            $ext         = $request->file('video')->getClientOriginalExtension();
            $uuid        = Str::uuid();
            $stagingPath = "videos/staging/{$uuid}.{$ext}";
            $finalPath   = "videos/movies/{$uuid}.{$ext}";

            $request->file('video')->storeAs('videos/staging', "{$uuid}.{$ext}", 'public');
            $data['video_path'] = $stagingPath;
        }

        $movie->update($data);

        if (isset($stagingPath, $finalPath)) {
            ProcessVideoUpload::dispatch(
                Movie::class,
                $movie->id,
                $stagingPath,
                $finalPath,
                auth()->id(),
                $movie->title,
                route('admin.movies.edit', $movie),
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

    public function toggleStatus(Movie $movie)
    {
        $movie->update([
            'status' => $movie->status === 'published' ? 'hidden' : 'published',
        ]);

        return back();
    }
}
