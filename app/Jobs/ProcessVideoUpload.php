<?php

namespace App\Jobs;

use App\Events\VideoReady;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcessVideoUpload implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;

    public function __construct(
        public readonly string $modelClass,
        public readonly int $modelId,
        public readonly string $stagingPath,
        public readonly string $finalPath,
        public readonly int $adminId,
        public readonly string $label,
        public readonly string $editUrl,
        public readonly string $field = 'video_path',
    ) {}

    public function handle(): void
    {
        // Always produce an MP4 regardless of the uploaded container format (MKV, WebM, etc.)
        // so every browser can play it natively.
        $finalPath = preg_replace('/\.[^.]+$/', '.mp4', $this->finalPath);

        $src  = storage_path('app/public/' . $this->stagingPath);
        $dest = storage_path('app/public/' . $finalPath);

        @mkdir(dirname($dest), 0755, true);

        // H.264 video + AAC audio, faststart for streaming
        $cmd = sprintf(
            'ffmpeg -i %s -c:v libx264 -preset fast -crf 22 -c:a aac -b:a 128k -movflags +faststart -y %s',
            escapeshellarg($src),
            escapeshellarg($dest)
        );

        exec($cmd . ' 2>&1', $output, $exitCode);

        Storage::disk('public')->delete($this->stagingPath);

        if ($exitCode !== 0 || !file_exists($dest)) {
            // Transcode failed — log and leave; the model keeps the staging path so the UI shows "processing"
            \Illuminate\Support\Facades\Log::error('ProcessVideoUpload ffmpeg failed', [
                'label'  => $this->label,
                'src'    => $src,
                'output' => implode("\n", $output),
            ]);
            return;
        }

        $this->modelClass::find($this->modelId)?->update([$this->field => $finalPath]);

        broadcast(new VideoReady($this->adminId, $this->label, $this->editUrl));
    }
}
