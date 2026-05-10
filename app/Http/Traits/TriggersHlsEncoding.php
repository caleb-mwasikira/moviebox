<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait TriggersHlsEncoding
{
    /**
     * Returns true  -> 202 Accepted (encoding queued) or 409 Conflict (already running — not an error).
     * Returns false -> service unreachable or returned an unexpected error response.
     *
     * @param string $editUrl  The admin edit-page URL included in the completion toast.
     * @param string $label    Human-readable label shown in the completion toast.
     */
    private function triggerConversion(
        int    $modelId,
        string $filmType,
        string $absolutePath,
        string $editUrl,
        string $label = '',
        string $field = 'trailer_path',
    ): bool {
        if (!file_exists($absolutePath)) {
            Log::warning('triggerConversion: file not found', ['path' => $absolutePath]);
            return false;
        }

        $adminId = auth()->id() ?? 0;
        $label   = $label ?: ucfirst($filmType) . " #{$modelId} (trailer)";

        $callbackUrl = url('/callbacks/conversion-ready') . '?' . http_build_query([
            'admin_id' => $adminId,
            'label'    => $label,
            'edit_url' => $editUrl,
        ]);

        $serviceUrl = env('STREAMING_SERVICE_URL', 'http://127.0.0.1:9090');

        try {
            $response = Http::timeout(10)->post($serviceUrl . '/convert-video', [
                'film_type'    => $filmType,
                'model_id'     => $modelId,
                'video_path'   => $absolutePath,
                'callback_url' => $callbackUrl,
                'field'        => $field,
            ]);

            if (!$response->successful()) {
                Log::warning('Streaming service conversion error', [
                    'film_type' => $filmType,
                    'model_id'  => $modelId,
                    'status'    => $response->status(),
                    'body'      => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::warning('Could not reach streaming service (conversion)', [
                'film_type' => $filmType,
                'model_id'  => $modelId,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }

    private function triggerHlsEncoding(
        int    $modelId,
        string $filmType,
        string $absolutePath,
        string $editUrl,
        string $label = '',
    ): bool {
        if (!file_exists($absolutePath)) {
            Log::warning('triggerHlsEncoding: file not found', ['path' => $absolutePath]);
            return false;
        }

        $adminId = auth()->id() ?? 0;
        $label   = $label ?: ucfirst($filmType) . " #{$modelId}";

        $callbackUrl = url('/callbacks/streaming-ready') . '?' . http_build_query([
            'admin_id'  => $adminId,
            'film_type' => $filmType,
            'model_id'  => $modelId,
            'label'     => $label,
            'edit_url'  => $editUrl,
        ]);

        $serviceUrl = env('STREAMING_SERVICE_URL', 'http://127.0.0.1:9090');

        try {
            $response = Http::timeout(10)->post($serviceUrl . '/process-video', [
                'film_type'    => $filmType,
                'model_id'     => $modelId,
                'video_path'   => $absolutePath,
                'callback_url' => $callbackUrl,
                'admin_id'     => $adminId,
                'title'        => $label,
            ]);

            if ($response->status() === 409) {
                Log::info('HLS encoding already in progress', ['film_type' => $filmType, 'model_id' => $modelId]);
                return true;
            }

            if (!$response->successful()) {
                Log::warning('Streaming service error', [
                    'film_type' => $filmType,
                    'model_id'  => $modelId,
                    'status'    => $response->status(),
                    'body'      => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::warning('Could not reach streaming service', [
                'film_type' => $filmType,
                'model_id'  => $modelId,
                'error'     => $e->getMessage(),
            ]);
            return false;
        }
    }
}
