<?php

namespace App\Http\Controllers;

use App\Events\VideoReady;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Series;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VideoCallbackController extends Controller
{
    public function videoReady(Request $request)
    {
        $validated = $request->validate([
            'success'   => ['required', 'boolean'],
            'model_uuid'  => ['required', 'string'],
            'film_type' => ['required', 'string', 'in:movies,series,movies_trailer,series_trailer'],
            'path'      => ['required_if:success,true', 'string'],
            'message'   => ['nullable', 'string'],
        ]);

        $isSuccess = $request->boolean('success');
        $modelUUID   = $validated['model_uuid'];
        $filmType  = $validated['film_type'];
        $path      = $validated['path'] ?? null;
        $message = $validated['message'] ?? ($isSuccess ? 'Encoding Complete' : 'Encoding Failed');

        Log::info('HLS video processing complete', [
            'success' => $isSuccess,
            'model_uuid' => $modelUUID,
            'film_type' => $filmType,
            'path' => $path,
            'message' => $message,
        ]);

        if ($isSuccess) {
            $this->updateRecordPath($filmType, $modelUUID, $path);
        }

        // Broadcast to admin for real-time UI update
        $adminId = (int) $request->query('user_id');
        if ($adminId > 0) {
            broadcast(new VideoReady($adminId, $message, $isSuccess));
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Logic to update the correct table and column.
     */
    protected function updateRecordPath(string $type, string $uuid, string $path): void
    {
        switch ($type) {
            case 'movies':
                Movie::where('uuid', $uuid)->update(['streaming_path' => $path]);
                break;

            case 'series':
                // Based on your migration, episodes have the streaming_path
                Episode::where('uuid', $uuid)->update(['streaming_path' => $path]);
                break;

            case 'movies_trailer':
                // Trailers update the trailer_path column in the movies table
                Movie::where('uuid', $uuid)->update(['trailer_path' => $path]);
                break;

            case 'series_trailer':
                // Trailers update the trailer_path column in the series table
                Series::where('uuid', $uuid)->update(['trailer_path' => $path]);
                break;

            default:
                Log::error("Unknown filmType received in callback: {$type}");
                break;
        }
    }
}