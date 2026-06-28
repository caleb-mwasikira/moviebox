<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    public function movie(Request $request, Movie $movie): JsonResponse
    {
        if (!$movie->isPublished() || $movie->isComingSoon()) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        if (!$movie->is_free && !$request->user()->currentSubscription) {
            return response()->json(['message' => 'A subscription is required to stream this content.'], 403);
        }

        $streamingUrl = $movie->streamingUrl();
        if (!$streamingUrl) {
            return response()->json(['message' => 'Stream not yet available.'], 404);
        }

        return response()->json([
            'streaming_url' => $streamingUrl,
            'token'         => $this->generateToken($request->user()->id),
        ]);
    }

    public function episode(Request $request, Episode $episode): JsonResponse
    {
        $series = $episode->season->series;

        if (!$series->isPublished()) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        if (!$episode->is_free && !$request->user()->currentSubscription) {
            return response()->json(['message' => 'A subscription is required to stream this content.'], 403);
        }

        $streamingUrl = $episode->streamingUrl();
        if (!$streamingUrl) {
            return response()->json(['message' => 'Stream not yet available.'], 404);
        }

        return response()->json([
            'streaming_url' => $streamingUrl,
            'token'         => $this->generateToken($request->user()->id),
        ]);
    }

    private function generateToken(int $userId): string
    {
        $uid = (string) $userId;
        $exp = now()->addHours(6)->timestamp;
        $sig = hash_hmac('sha256', $uid . ':' . $exp, config('services.streaming.secret'));

        return base64_encode($uid . ':' . $exp . ':' . $sig);
    }
}
