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
    ) {}

    public function handle(): void
    {
        Storage::disk('public')->move($this->stagingPath, $this->finalPath);

        $this->modelClass::find($this->modelId)?->update(['video_path' => $this->finalPath]);

        broadcast(new VideoReady($this->adminId, $this->label, $this->editUrl));
    }
}
