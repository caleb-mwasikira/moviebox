<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovieResource;
use App\Http\Resources\SeriesResource;
use App\Models\Movie;
use App\Models\Series;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $q     = trim((string) $request->input('q', ''));
        $genre = $request->input('genre');

        $movieQuery  = Movie::where('status', 'published')->where('coming_soon', false);
        $seriesQuery = Series::where('status', 'published');

        if ($q !== '') {
            $pattern = '%' . strtolower($q) . '%';
            $movieQuery->where(fn ($b) => $b
                ->whereRaw('LOWER(title) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(genre) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(description) LIKE ?', [$pattern])
            );
            $seriesQuery->where(fn ($b) => $b
                ->whereRaw('LOWER(title) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(genre) LIKE ?', [$pattern])
                ->orWhereRaw('LOWER(description) LIKE ?', [$pattern])
            );
        }

        if ($genre && $genre !== 'All') {
            $p = '%' . strtolower($genre) . '%';
            $movieQuery->whereRaw('LOWER(genre) LIKE ?', [$p]);
            $seriesQuery->whereRaw('LOWER(genre) LIKE ?', [$p]);
        }

        return response()->json([
            'movies' => MovieResource::collection($movieQuery->orderByDesc('created_at')->take(20)->get()),
            'series' => SeriesResource::collection($seriesQuery->orderByDesc('created_at')->take(20)->get()),
        ]);
    }

    public function movies(Request $request): JsonResponse
    {
        $query = Movie::where('status', 'published')->where('coming_soon', false);

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(fn ($b) => $b->where('title', 'like', "%{$q}%")->orWhere('genre', 'like', "%{$q}%")->orWhere('description', 'like', "%{$q}%"));
        }

        if ($request->filled('genre') && $request->input('genre') !== 'All') {
            $query->where('genre', 'like', '%' . $request->input('genre') . '%');
        }

        $movies = $query
            ->orderByDesc('featured')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'data' => MovieResource::collection($movies->items()),
            'meta' => [
                'current_page' => $movies->currentPage(),
                'last_page'    => $movies->lastPage(),
                'total'        => $movies->total(),
            ],
        ]);
    }

    public function movie(Movie $movie): JsonResponse
    {
        if (!$movie->isPublished() || $movie->isComingSoon()) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        return response()->json(['data' => new MovieResource($movie)]);
    }

    public function series(Request $request): JsonResponse
    {
        $series = Series::where('status', 'published')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'data' => SeriesResource::collection($series->items()),
            'meta' => [
                'current_page' => $series->currentPage(),
                'last_page'    => $series->lastPage(),
                'total'        => $series->total(),
            ],
        ]);
    }

    public function show(Series $series): JsonResponse
    {
        if (!$series->isPublished()) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $series->load(['seasons.episodes']);

        return response()->json(['data' => new SeriesResource($series)]);
    }
}
