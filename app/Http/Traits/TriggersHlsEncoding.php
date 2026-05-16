<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait TriggersHlsEncoding
{
    public function convertMkv(
        string $videoUUID,
        string $inputPath,
        string $outputPath,
        string $relativeOutputPath,
        string $filmType,
    ): bool {
        if (!file_exists($inputPath)) {
            Log::warning('convertMkv: file not found', ['path' => $inputPath]);
            return false;
        }

        $callbackUrl = url('/callbacks/mkv-converted') . '?' . http_build_query([
            'user_id'   => Auth::id() ?? 0,
            'uuid'      => $videoUUID,
            'film_type' => $filmType,
            'path'      => $relativeOutputPath,
        ]);

        $serviceUrl = env('STREAMING_SERVICE_URL', 'http://127.0.0.1:9090');

        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-Api-Key' => env('APP_SECRET', '')])
                ->post($serviceUrl . '/convert-mkv', [
                    'video_uuid'   => $videoUUID,
                    'input_path'   => $inputPath,
                    'output_path'  => $outputPath,
                    'callback_url' => $callbackUrl,
                ]);

            if ($response->status() === 409) {
                Log::info('MKV conversion already in progress', ['video_uuid' => $videoUUID]);
                return true;
            }

            if (!$response->successful()) {
                Log::warning('Streaming service MKV conversion error', [
                    'video_uuid' => $videoUUID,
                    'status'     => $response->status(),
                    'body'       => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::warning('Could not reach streaming service for MKV conversion', [
                'video_uuid' => $videoUUID,
                'error'      => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function cancelJob(string $videoUUID): void
    {
        $serviceUrl = env('STREAMING_SERVICE_URL', 'http://127.0.0.1:9090');

        try {
            Http::timeout(5)
                ->withHeaders(['X-Api-Key' => env('APP_SECRET', '')])
                ->post($serviceUrl . '/cancel-job', ['video_uuid' => $videoUUID]);
        } catch (\Exception $e) {
            Log::warning('cancelJob: could not reach streaming service', [
                'video_uuid' => $videoUUID,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    public function convertVideo(
        string $videoUUID,
        string $filmType,
        string $videoTitle,
        string $videoPath,
        ?string $seriesTitle = null,
    ): bool {
        if (!file_exists($videoPath)) {
            Log::warning('convertVideo: file not found', ['path' => $videoPath]);
            return false;
        }

        $adminId = Auth::id() ?? 0;

        $callbackUrl = url('/callbacks/video-ready') . '?' . http_build_query([
            'user_id'  => $adminId,
        ]);

        $serviceUrl = env('STREAMING_SERVICE_URL', 'http://127.0.0.1:9090');

        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-Api-Key' => env('APP_SECRET', '')])
                ->post($serviceUrl . '/convert-video', [
                'video_uuid'   => $videoUUID,
                'video_title'  => $videoTitle,
                'series_title' => $seriesTitle ?? '',
                'video_path'   => $videoPath,
                'film_type'    => $filmType,
                'callback_url' => $callbackUrl,
            ]);

            if ($response->status() === 409) {
                Log::info('HLS encoding already in progress', [
                    'film_type' => $filmType,
                    'video_uuid' => $videoUUID
                ]);
                return true;
            }

            if (!$response->successful()) {
                Log::warning('Streaming service error', [
                    'film_type' => $filmType,
                    'video_uuid'  => $videoUUID,
                    'status'    => $response->status(),
                    'body'      => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::warning('Could not reach streaming service', [
                'film_type' => $filmType,
                'video_uuid'  => $videoUUID,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }
}
