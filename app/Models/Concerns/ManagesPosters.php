<?php

namespace App\Models\Concerns;

use App\Models\Poster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * One list of poster images with one marked as main. `thumbnail` mirrors the main poster's path,
 * so cards and heroes across the site keep reading a single column.
 */
trait ManagesPosters
{
    /**
     * Applies an admin's poster edits: removals, new uploads and the chosen main poster.
     *
     * The form sends `remove_posters[]` (ids), `posters[]` (new files) and `main_poster`
     * ("e{id}" for an existing poster, "n{index}" for the index-th new file).
     */
    public function applyPosterChanges(Request $request, string $folder): void
    {
        $remove = collect($request->input('remove_posters', []))->map(fn ($id) => (int) $id);

        $this->posters()->whereIn('id', $remove)->get()->each(function (Poster $poster) {
            Storage::disk('public')->delete($poster->path);
            $poster->delete();
        });

        $sort    = ($this->posters()->max('sort_order') ?? -1) + 1;
        $created = [];
        foreach ($request->file('posters', []) as $file) {
            $created[] = $this->posters()->create(['path' => $file->store($folder, 'public'), 'sort_order' => $sort++]);
        }

        $main = (string) $request->input('main_poster', '');
        $pick = null;
        if (preg_match('/^e(\d+)$/', $main, $m)) {
            $pick = $this->posters()->find((int) $m[1]);
        } elseif (preg_match('/^n(\d+)$/', $main, $m)) {
            $pick = $created[(int) $m[1]] ?? null;
        }

        $this->settleMainPoster($pick);
    }

    /** Marks $pick as main (or keeps the current main, or falls back to the first poster). */
    public function settleMainPoster(?Poster $pick = null): void
    {
        $posters = $this->posters()->orderBy('sort_order')->orderBy('id')->get();
        $main    = $pick ?? $posters->firstWhere('is_main', true) ?? $posters->first();

        $this->posters()->update(['is_main' => false]);
        $main?->forceFill(['is_main' => true])->save();
        $this->forceFill(['thumbnail' => $main?->path])->save();
    }

    /** Titles saved before posters had a main (e.g. coming-soon) get their thumbnail listed as a poster. */
    public function ensureThumbnailIsPoster(): void
    {
        if ($this->thumbnail && ! $this->posters()->where('path', $this->thumbnail)->exists()) {
            $this->posters()->create(['path' => $this->thumbnail, 'sort_order' => -1, 'is_main' => ! $this->posters()->where('is_main', true)->exists()]);
        }
    }
}
