<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\TriggersHlsEncoding;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeriesController extends Controller
{
    use TriggersHlsEncoding;

    public function index()
    {
        $series = Series::withCount('seasons')
            ->withCount(['episodes as unprocessed_count' => fn ($q) => $q->whereNull('streaming_path')])
            ->latest()
            ->paginate(20);
        return view('pages.admin.series.index', compact('series'));
    }

    public function create()
    {
        return view('pages.admin.series.create');
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
            'status'      => ['required', 'in:published,hidden'],
            'posters'     => ['nullable', 'array'],
            'posters.*'   => ['image', 'max:4096'],
        ]);

        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'genre'       => $validated['genre'] ?? null,
            'year'        => $validated['year'] ?? null,
            'status'      => $validated['status'],
        ];

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails/series', 'public');
        }

        $series = Series::create($data);

        foreach ($request->file('posters', []) as $i => $file) {
            $series->posters()->create([
                'path'       => $file->store('posters/series', 'public'),
                'sort_order' => $i,
            ]);
        }

        if ($request->hasFile('trailer')) {
            $tExt         = $request->file('trailer')->getClientOriginalExtension();
            $tUuid        = Str::uuid();
            $tStagingPath = "videos/staging/{$tUuid}.{$tExt}";
            $request->file('trailer')->storeAs('videos/staging', "{$tUuid}.{$tExt}", 'public');
            $series->update(['trailer_path' => $tStagingPath]);

            $this->convertVideo(
                $series->uuid,
                'series_trailer',
                $series->title,
                storage_path('app/public/' . $tStagingPath),
                $series->title,
            );
        }

        return redirect()->route('admin.series.edit', $series)->with('status', 'series-created');
    }

    public function edit(Series $series)
    {
        $series->load(['seasons.episodes', 'posters']);
        $nextSeasonNumber = ($series->seasons->max('season_number') ?? 0) + 1;
        return view('pages.admin.series.edit', compact('series', 'nextSeasonNumber'));
    }

    public function update(Request $request, Series $series)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'genre'       => ['nullable', 'string', 'max:100'],
            'year'        => ['nullable', 'integer', 'min:1888', 'max:2100'],
            'thumbnail'   => ['nullable', 'image', 'max:2048'],
            'trailer'     => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska'],
            'status'      => ['required', 'in:published,hidden'],
            'posters'     => ['nullable', 'array'],
            'posters.*'   => ['image', 'max:4096'],
        ]);

        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'genre'       => $validated['genre'] ?? null,
            'year'        => $validated['year'] ?? null,
            'status'      => $validated['status'],
        ];

        if ($request->hasFile('thumbnail')) {
            if ($series->thumbnail) {
                Storage::disk('public')->delete($series->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails/series', 'public');
        }

        $series->update($data);

        if ($request->hasFile('trailer')) {
            if ($series->trailer_path && !str_starts_with($series->trailer_path, 'http')) {
                Storage::disk('public')->delete($series->trailer_path);
            }
            $tExt         = $request->file('trailer')->getClientOriginalExtension();
            $tUuid        = Str::uuid();
            $tStagingPath = "videos/staging/{$tUuid}.{$tExt}";
            $request->file('trailer')->storeAs('videos/staging', "{$tUuid}.{$tExt}", 'public');
            $series->update(['trailer_path' => $tStagingPath]);

            $this->convertVideo(
                $series->uuid,
                'series_trailer',
                $series->title,
                storage_path('app/public/' . $tStagingPath),
                $series->title,
            );
        }

        $offset = $series->posters()->max('sort_order') ?? -1;
        foreach ($request->file('posters', []) as $i => $file) {
            $series->posters()->create([
                'path'       => $file->store('posters/series', 'public'),
                'sort_order' => $offset + $i + 1,
            ]);
        }

        return redirect()->route('admin.series.edit', $series)->with('status', 'series-updated');
    }

    public function destroy(Series $series)
    {
        if ($series->thumbnail) {
            Storage::disk('public')->delete($series->thumbnail);
        }
        foreach ($series->posters as $poster) {
            Storage::disk('public')->delete($poster->path);
        }
        $series->delete();

        return redirect()->route('admin.series.index')->with('status', 'series-deleted');
    }

    public function uploadTrailer(Request $request, Series $series)
    {
        $request->validate([
            'trailer' => ['required', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska,video/mkv,video/matroska'],
        ]);

        if ($series->trailer_path && !str_starts_with($series->trailer_path, 'http')) {
            Storage::disk('public')->delete($series->trailer_path);
        }

        $ext         = $request->file('trailer')->getClientOriginalExtension();
        $uuid        = Str::uuid();
        $stagingPath = "videos/staging/{$uuid}.{$ext}";

        $request->file('trailer')->storeAs('videos/staging', "{$uuid}.{$ext}", 'public');
        $series->update(['trailer_path' => $stagingPath]);

        $this->convertVideo(
            $series->uuid,
            'series_trailer',
            $series->title,
            storage_path('app/public/' . $stagingPath),
            $series->title,
        );

        return redirect()->route('admin.series.edit', $series)->with('status', 'trailer-processing');
    }

    public function toggleStatus(Series $series)
    {
        $series->update([
            'status' => $series->status === 'published' ? 'hidden' : 'published',
        ]);

        return back();
    }

    public function toggleFeatured(Series $series)
    {
        $series->update(['featured' => !$series->featured]);

        return back();
    }
}
