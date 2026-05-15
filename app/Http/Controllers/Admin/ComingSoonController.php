<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\TriggersHlsEncoding;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComingSoonController extends Controller
{
    use TriggersHlsEncoding;

    public function index()
    {
        $films = Movie::where('coming_soon', true)->latest()->paginate(20);
        return view('pages.admin.coming-soon.index', compact('films'));
    }

    public function create()
    {
        return view('pages.admin.coming-soon.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'genre'       => ['nullable', 'string', 'max:100'],
            'year'        => ['nullable', 'integer', 'min:1888', 'max:2100'],
            'thumbnail'   => ['nullable', 'image', 'max:2048'],
            'trailer'     => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska'],
            'posters'     => ['nullable', 'array'],
            'posters.*'   => ['image', 'max:4096'],
        ]);

        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'genre'       => $validated['genre'] ?? null,
            'year'        => $validated['year'] ?? null,
            'status'      => 'hidden',
            'coming_soon' => true,
        ];

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails/movies', 'public');
        }

        $film = Movie::create($data);

        foreach ($request->file('posters', []) as $i => $file) {
            $film->posters()->create([
                'path'       => $file->store('posters/movies', 'public'),
                'sort_order' => $i,
            ]);
        }

        if ($request->hasFile('trailer')) {
            $ext         = $request->file('trailer')->getClientOriginalExtension();
            $uuid        = Str::uuid();
            $stagingPath = "videos/staging/{$uuid}.{$ext}";
            $request->file('trailer')->storeAs('videos/staging', "{$uuid}.{$ext}", 'public');
            $film->update(['trailer_path' => $stagingPath]);

            $this->convertVideo(
                $film->uuid,
                'movies_trailer',
                $film->title,
                storage_path('app/public/' . $stagingPath),
            );
        }

        return redirect()->route('admin.coming-soon.index')->with('status', 'coming-soon-created');
    }

    public function edit(Movie $comingSoon)
    {
        $comingSoon->load('posters');
        return view('pages.admin.coming-soon.edit', ['film' => $comingSoon]);
    }

    public function update(Request $request, Movie $comingSoon)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'genre'       => ['nullable', 'string', 'max:100'],
            'year'        => ['nullable', 'integer', 'min:1888', 'max:2100'],
            'thumbnail'   => ['nullable', 'image', 'max:2048'],
            'posters'     => ['nullable', 'array'],
            'posters.*'   => ['image', 'max:4096'],
        ]);

        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'genre'       => $validated['genre'] ?? null,
            'year'        => $validated['year'] ?? null,
        ];

        if ($request->hasFile('thumbnail')) {
            if ($comingSoon->thumbnail) {
                Storage::disk('public')->delete($comingSoon->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails/movies', 'public');
        }

        $comingSoon->update($data);

        $offset = $comingSoon->posters()->max('sort_order') ?? -1;
        foreach ($request->file('posters', []) as $i => $file) {
            $comingSoon->posters()->create([
                'path'       => $file->store('posters/movies', 'public'),
                'sort_order' => $offset + $i + 1,
            ]);
        }

        return redirect()->route('admin.coming-soon.edit', $comingSoon)->with('status', 'coming-soon-updated');
    }

    public function uploadTrailer(Request $request, Movie $comingSoon)
    {
        $request->validate([
            'trailer' => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska,video/mkv,video/matroska'],
        ]);

        if ($comingSoon->trailer_path && !str_starts_with($comingSoon->trailer_path, 'http')) {
            Storage::disk('public')->delete($comingSoon->trailer_path);
        }

        $ext         = $request->file('trailer')->getClientOriginalExtension();
        $uuid        = Str::uuid();
        $stagingPath = "videos/staging/{$uuid}.{$ext}";

        $request->file('trailer')->storeAs('videos/staging', "{$uuid}.{$ext}", 'public');
        $comingSoon->update(['trailer_path' => $stagingPath]);

        $this->convertVideo(
            $comingSoon->uuid,
            'movies_trailer',
            $comingSoon->title,
            storage_path('app/public/' . $stagingPath),
        );

        return redirect()->route('admin.coming-soon.edit', $comingSoon)->with('status', 'trailer-processing');
    }

    public function uploadVideo(Request $request, Movie $comingSoon)
    {
        $request->validate([
            'video' => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska,video/mkv,video/matroska'],
        ]);

        $ext       = $request->file('video')->getClientOriginalExtension();
        $uuid      = Str::uuid();
        $finalPath = "videos/movies/{$uuid}.{$ext}";

        $request->file('video')->storeAs('videos/movies', "{$uuid}.{$ext}", 'public');

        $comingSoon->update([
            'video_path'  => $finalPath,
            'coming_soon' => false,
            'status'      => 'published',
        ]);

        if (strtolower($ext) === 'mkv') {
            $mp4RelPath = "videos/movies/{$uuid}.mp4";
            $this->convertMkv(
                $comingSoon->uuid,
                storage_path('app/public/' . $finalPath),
                storage_path('app/public/' . $mp4RelPath),
                $mp4RelPath,
                'movies',
            );
        }

        $this->convertVideo(
            $comingSoon->uuid,
            'movies',
            $comingSoon->title,
            storage_path('app/public/' . $finalPath),
        );

        return redirect()->route('admin.movies.edit', $comingSoon)->with('status', 'movie-processing');
    }

    public function toggleFeatured(Request $request, Movie $comingSoon)
    {
        $comingSoon->update(['featured' => !$comingSoon->featured]);
        if ($request->wantsJson()) {
            return response()->json(['featured' => $comingSoon->featured]);
        }
        return back();
    }

    public function destroy(Request $request, Movie $comingSoon)
    {
        if ($comingSoon->thumbnail) {
            Storage::disk('public')->delete($comingSoon->thumbnail);
        }
        if ($comingSoon->trailer_path && !str_starts_with($comingSoon->trailer_path, 'http')) {
            Storage::disk('public')->delete($comingSoon->trailer_path);
        }
        foreach ($comingSoon->posters as $poster) {
            Storage::disk('public')->delete($poster->path);
        }
        $comingSoon->delete();

        if ($request->wantsJson()) {
            return response()->json(['deleted' => true]);
        }
        return redirect()->route('admin.coming-soon.index')->with('status', 'coming-soon-deleted');
    }
}
