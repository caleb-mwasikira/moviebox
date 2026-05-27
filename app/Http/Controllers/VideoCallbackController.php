<?php

namespace App\Http\Controllers;

use App\Events\VideoReady;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Series;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoCallbackController extends Controller
{
    public function mkvConverted(Request $request)
    {
        $request->validate([
            'success'    => ['required', 'boolean'],
            'video_uuid' => ['required', 'string'],
            'message'    => ['nullable', 'string'],
        ]);

        $isSuccess    = $request->boolean('success');
        $videoUUID    = $request->input('video_uuid');
        $filmType     = $request->query('film_type');
        $relativePath = $request->query('path');
        $adminId      = (int) $request->query('user_id');

        Log::info('MKV conversion complete', [
            'success'    => $isSuccess,
            'video_uuid' => $videoUUID,
            'film_type'  => $filmType,
            'path'       => $relativePath,
            'message'    => $request->input('message'),
        ]);

        if ($isSuccess && $relativePath) {
            match ($filmType) {
                'movies' => Movie::where('uuid', $videoUUID)->update(['video_path' => $relativePath]),
                'series' => Episode::where('uuid', $videoUUID)->update(['video_path' => $relativePath]),
                default  => Log::error("Unknown film_type in mkv callback: {$filmType}"),
            };
        }

        if ($adminId > 0) {
            $message = $isSuccess
                ? 'MKV conversion complete — MP4 ready'
                : ($request->input('message') ?? 'MKV conversion failed');
            broadcast(new VideoReady($adminId, $message, $isSuccess));
        }

        return response()->json(['ok' => true]);
    }

    public function videoReady(Request $request)
    {
        $validated = $request->validate([
            'success'                    => ['required', 'boolean'],
            'model_uuid'                 => ['required', 'string'],
            'film_type'                  => ['required', 'string', 'in:movies,series,movies_trailer,series_trailer'],
            'path'                       => ['required_if:success,true', 'string'],
            'message'                    => ['nullable', 'string'],
            'subtitles'                  => ['nullable', 'array'],
            'subtitles.*.language'       => ['required_with:subtitles', 'string', 'max:100'],
            'subtitles.*.language_code'  => ['required_with:subtitles', 'string', 'max:10'],
            'subtitles.*.path'           => ['required_with:subtitles', 'string'],
            'subtitles.*.is_default'     => ['boolean'],
        ]);

        $isSuccess = $request->boolean('success');
        $modelUUID = $validated['model_uuid'];
        $filmType  = $validated['film_type'];
        $path      = $validated['path'] ?? null;
        $message   = $validated['message'] ?? ($isSuccess ? 'Encoding Complete' : 'Encoding Failed');

        Log::info('HLS video processing complete', [
            'success'    => $isSuccess,
            'model_uuid' => $modelUUID,
            'film_type'  => $filmType,
            'path'       => $path,
            'message'    => $message,
        ]);

        if ($isSuccess) {
            $model = $this->updateRecordPath($filmType, $modelUUID, $path);

            if ($model && !empty($validated['subtitles'])) {
                $this->saveSubtitles($model, $modelUUID, $validated['subtitles']);
            }
        }

        // Broadcast to admin for real-time UI update
        $adminId = (int) $request->query('user_id');
        if ($adminId > 0) {
            broadcast(new VideoReady($adminId, $message, $isSuccess));
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Update the streaming/trailer path for the model and return it (or null for trailers).
     */
    protected function updateRecordPath(string $type, string $uuid, string $path): ?Model
    {
        switch ($type) {
            case 'movies':
                $model = Movie::where('uuid', $uuid)->first();
                $model?->update(['streaming_path' => $path]);
                return $model;

            case 'series':
                $model = Episode::where('uuid', $uuid)->first();
                $model?->update(['streaming_path' => $path]);
                return $model;

            case 'movies_trailer':
                Movie::where('uuid', $uuid)->update(['trailer_path' => $path]);
                return null;

            case 'series_trailer':
                Series::where('uuid', $uuid)->update(['trailer_path' => $path]);
                return null;

            default:
                Log::error("Unknown filmType received in callback: {$type}");
                return null;
        }
    }

    /**
     * Download extracted VTT files from the streaming service and store them
     * in public storage, then upsert Subtitle records on the model.
     */
    protected function saveSubtitles(Model $model, string $modelUuid, array $subtitles): void
    {
        $baseUrl = rtrim(env('STREAMING_SERVICE_URL', 'http://127.0.0.1:9090'), '/');
        $apiKey  = env('APP_SECRET', '');

        foreach ($subtitles as $sub) {
            try {
                $response = Http::withHeaders(['X-Api-Key' => $apiKey])
                    ->get($baseUrl . '/internal/subs', ['path' => $sub['path']]);

                if (!$response->ok()) {
                    Log::warning('saveSubtitles: failed to download subtitle', [
                        'path'   => $sub['path'],
                        'status' => $response->status(),
                    ]);
                    continue;
                }

                $localPath = "subtitles/{$modelUuid}/{$sub['language_code']}.vtt";
                Storage::disk('public')->put($localPath, $response->body());

                $model->subtitles()->updateOrCreate(
                    ['language_code' => $sub['language_code']],
                    [
                        'language'   => $sub['language'],
                        'file_path'  => $localPath,
                        'is_default' => $sub['is_default'] ?? false,
                    ]
                );

                Log::info('saveSubtitles: stored', ['path' => $localPath]);
            } catch (\Throwable $e) {
                Log::error('saveSubtitles: exception', [
                    'language_code' => $sub['language_code'],
                    'error'         => $e->getMessage(),
                ]);
            }
        }
    }
}