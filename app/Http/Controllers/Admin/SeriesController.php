<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PlansBackgroundUploads;
use App\Http\Traits\TriggersHlsEncoding;
use App\Jobs\SendNewContentAlerts;
use App\Models\ActivityLog;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeriesController extends Controller
{
    use PlansBackgroundUploads;
    use TriggersHlsEncoding;

    public function index(Request $request)
    {
        $trashed = $request->boolean('trashed');

        $query = $trashed
            ? Series::onlyTrashed()->latest('deleted_at')
            : Series::withCount('seasons')
                ->withCount(['episodes as unprocessed_count' => fn ($q) => $q->whereNull('streaming_path')])
                ->withCount(['episodes as free_episodes_count' => fn ($q) => $q->where('is_free', true)])
                ->latest();

        $series = $query->paginate(20)->withQueryString();

        return view('pages.admin.series.index', compact('series', 'trashed'));
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
            'trailer'     => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska'],
            'status'      => ['required', 'in:published,hidden'],
            'posters'     => ['nullable', 'array'],
            'posters.*'   => ['image', 'max:4096'],
            'main_poster' => ['nullable', 'string', 'max:20'],
            'remove_posters'   => ['nullable', 'array'],
            'remove_posters.*' => ['integer'],
        ] + $this->deferredFileRules());

        $data = [
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'genre'       => $validated['genre'] ?? null,
            'year'        => $validated['year'] ?? null,
            'status'      => $validated['status'],
        ];

        $series = Series::create($data);
        ActivityLog::record('created', 'Series', $series->id, $series->title);

        $series->applyPosterChanges($request, 'posters/series');

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

        if ($request->expectsJson()) {
            return response()->json([
                'uploads'   => $this->planUploads($request, $series, ['trailer' => route('admin.series.trailer', $series)]),
                'title'     => $series->title,
                'edit_url'  => route('admin.series.edit', $series),
                'index_url' => route('admin.series.index'),
            ]);
        }

        return redirect()->route('admin.series.edit', $series)->with('status', 'series-created');
    }

    public function edit(Series $series)
    {
        $series->ensureThumbnailIsPoster();
        $series->load(['seasons.episodes.videoUploads', 'posters', 'videoUploads']);
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
            'trailer'     => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg,video/x-matroska'],
            'status'      => ['required', 'in:published,hidden'],
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
            'status'      => $validated['status'],
        ];

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

        $series->applyPosterChanges($request, 'posters/series');

        ActivityLog::record('updated', 'Series', $series->id, $series->title);

        return redirect()->route('admin.series.edit', $series)->with('status', 'series-updated');
    }

    public function destroy(Series $series)
    {
        $title = $series->title;

        if (auth()->user()->isAdmin()) {
            $this->deleteSeriesFiles($series);
            $series->forceDelete();
            ActivityLog::record('force_deleted', 'Series', null, $title);
        } else {
            $series->delete();
            ActivityLog::record('deleted', 'Series', $series->id, $title);
        }

        return redirect()->route('admin.series.index')->with('status', 'series-deleted');
    }

    public function restore(int $id)
    {
        $series = Series::withTrashed()->findOrFail($id);
        $series->restore();
        ActivityLog::record('restored', 'Series', $series->id, $series->title);

        return redirect()->route('admin.trash.index')->with('status', 'series-restored');
    }

    public function forceDestroy(int $id)
    {
        $series = Series::withTrashed()->findOrFail($id);
        $title  = $series->title;
        $this->deleteSeriesFiles($series);
        $series->forceDelete();
        ActivityLog::record('force_deleted', 'Series', null, $title);

        return redirect()->route('admin.trash.index')->with('status', 'series-force-deleted');
    }

    private function deleteSeriesFiles(Series $series): void
    {
        if ($series->thumbnail) {
            Storage::disk('public')->delete($series->thumbnail);
        }
        foreach ($series->posters as $poster) {
            Storage::disk('public')->delete($poster->path);
        }
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
        $newStatus = $series->status === 'published' ? 'hidden' : 'published';
        $series->update(['status' => $newStatus]);

        if ($newStatus === 'published') {
            SendNewContentAlerts::dispatch(
                title: $series->title,
                type:  'series',
                url:   route('series.show', $series),
                genre: $series->genre,
                year:  $series->year,
            );
        }

        return back();
    }

    public function toggleFeatured(Series $series)
    {
        $series->update(['featured' => !$series->featured]);

        return back();
    }
}
