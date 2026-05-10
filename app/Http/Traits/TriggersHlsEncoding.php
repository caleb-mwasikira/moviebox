<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait TriggersHlsEncoding
{
    public function convertVideo(
        string $videoUUID,
        string $filmType,
        string $videoTitle,
        string $videoPath,
    ): bool {
        if (!file_exists($videoPath)) {
            Log::warning('convertVideo: file not found', ['path' => $videoPath]);
            return false;
        }

        $adminId = auth()->id() ?? 0;

        $callbackUrl = url('/callbacks/video-ready') . '?' . http_build_query([
            'user_id'  => $adminId,
        ]);

        $serviceUrl = env('STREAMING_SERVICE_URL', 'http://127.0.0.1:9090');

        try {
            $response = Http::timeout(10)->post($serviceUrl . '/convert-video', [
                'video_uuid' => $videoUUID,
                'video_title' => $videoTitle,
                'video_path' => $videoPath,
                'film_type' => $filmType,
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
