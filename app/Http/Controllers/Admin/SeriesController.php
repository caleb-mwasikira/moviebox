<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SeriesController extends Controller
{
    public function index()
    {
        $series = Series::withCount('seasons')->latest()->paginate(20);
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

    public function toggleStatus(Series $series)
    {
        $series->update([
            'status' => $series->status === 'published' ? 'hidden' : 'published',
        ]);

        return back();
    }
}
