<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\CollectionItem;
use App\Models\Movie;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    public function index()
    {
        $collections = Collection::withCount('items')->orderBy('sort_order')->orderBy('name')->get();
        return view('pages.admin.collections.index', compact('collections'));
    }

    public function create()
    {
        return view('pages.admin.collections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail'   => ['nullable', 'image', 'max:2048'],
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $slug = Str::slug($validated['name']);
        $base = $slug;
        $i    = 1;
        while (Collection::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $data = [
            'name'        => $validated['name'],
            'slug'        => $slug,
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->boolean('is_active', true),
            'sort_order'  => $validated['sort_order'] ?? 0,
        ];

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails/collections', 'public');
        }

        $collection = Collection::create($data);

        return redirect()->route('admin.collections.edit', $collection)->with('status', 'collection-created');
    }

    public function edit(Collection $collection)
    {
        $collection->load(['items.collectible']);

        $movies = Movie::where('status', 'published')->orderBy('title')->get(['id', 'title', 'thumbnail', 'year']);
        $series = Series::where('status', 'published')->orderBy('title')->get(['id', 'title', 'thumbnail', 'year']);

        return view('pages.admin.collections.edit', compact('collection', 'movies', 'series'));
    }

    public function update(Request $request, Collection $collection)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'thumbnail'   => ['nullable', 'image', 'max:2048'],
            'is_active'   => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);

        $data = [
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->boolean('is_active'),
            'sort_order'  => $validated['sort_order'] ?? 0,
        ];

        if ($request->hasFile('thumbnail')) {
            if ($collection->thumbnail) {
                Storage::disk('public')->delete($collection->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails/collections', 'public');
        }

        $collection->update($data);

        return redirect()->route('admin.collections.edit', $collection)->with('status', 'collection-updated');
    }

    public function destroy(Collection $collection)
    {
        if ($collection->thumbnail) {
            Storage::disk('public')->delete($collection->thumbnail);
        }
        $collection->delete();

        return redirect()->route('admin.collections.index')->with('status', 'collection-deleted');
    }

    public function addItem(Request $request, Collection $collection)
    {
        $request->validate([
            'type' => ['required', 'in:movie,series'],
            'id'   => ['required', 'integer'],
        ]);

        $modelClass = $request->input('type') === 'movie' ? Movie::class : Series::class;
        $model      = $modelClass::findOrFail($request->input('id'));

        $nextOrder = $collection->items()->max('sort_order') + 1;

        $collection->items()->firstOrCreate(
            ['collectible_type' => $modelClass, 'collectible_id' => $model->id],
            ['sort_order' => $nextOrder],
        );

        return back()->with('status', 'item-added');
    }

    public function removeItem(Collection $collection, CollectionItem $item)
    {
        abort_if($item->collection_id !== $collection->id, 404);
        $item->delete();

        return back()->with('status', 'item-removed');
    }
}
