<?php

namespace App\Http\Controllers;

use App\Models\Series;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    public function index(Request $request)
    {
        $query = Series::where('status', 'published');

        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }

        $series = $query->latest()->paginate(12)->withQueryString();
        $genres = Series::where('status', 'published')
            ->distinct()
            ->orderBy('genre')
            ->pluck('genre')
            ->filter()
            ->values();

        return view('pages.series.index', compact('series', 'genres'));
    }

    public function show(Series $series)
    {
        abort_unless($series->isPublished(), 404);
        $series->load(['seasons.episodes', 'posters']);
        return view('pages.series.show', compact('series'));
    }
}
