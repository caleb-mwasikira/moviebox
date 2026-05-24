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
    public function movies(Request $request): JsonResponse
    {
        $movies = Movie::where('status', 'published')
            ->where('coming_soon', false)
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
