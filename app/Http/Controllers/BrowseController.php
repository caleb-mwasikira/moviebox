<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;
use Illuminate\Http\Request;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        $type  = $request->input('type', 'all');
        $genre = $request->input('genre');

        $movieQuery  = Movie::where('status', 'published');
        $seriesQuery = Series::where('status', 'published');

        if ($genre) {
            $movieQuery->where('genre', $genre);
            $seriesQuery->where('genre', $genre);
        }

        $movies = in_array($type, ['all', 'movies'])
            ? $movieQuery->latest()->get()
            : collect();

        $series = in_array($type, ['all', 'series'])
            ? $seriesQuery->latest()->get()
            : collect();

        $genres = Movie::where('status', 'published')->distinct()->pluck('genre')
            ->merge(Series::where('status', 'published')->distinct()->pluck('genre'))
            ->filter()->unique()->sort()->values();

        return view('pages.browse', compact('movies', 'series', 'genres', 'type', 'genre'));
    }
}
