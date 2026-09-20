<?php

namespace App\Models;

use App\Support\ChunkedUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/** A video or trailer that is still being sent from the admin's browser. */
class VideoUpload extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    /** An upload that hasn't received bytes for this long is treated as interrupted. */
    public const STALL_SECONDS = 45;

    protected $fillable = ['user_id', 'uploadable_type', 'uploadable_id', 'kind', 'file_name', 'file_size', 'received'];

    protected $casts = ['file_size' => 'integer', 'received' => 'integer'];

    public function uploadable(): MorphTo
    {
        return $this->morphTo();
    }

    /** Registers the upload and reserves its file on disk. Its id doubles as the chunk-upload id. */
    public static function start(int $userId, Model $target, string $kind, string $name, int $size): self
    {
        $upload = new self([
            'user_id' => $userId, 'kind' => $kind, 'file_name' => $name, 'file_size' => $size, 'received' => 0,
        ]);
        $upload->uploadable()->associate($target);
        $upload->id = ChunkedUpload::create($userId, $name, $size);
        $upload->save();

        return $upload;
    }

    public function percent(): int
    {
        return $this->file_size ? (int) min(100, floor($this->received / $this->file_size * 100)) : 0;
    }

    public function isStalled(): bool
    {
        return $this->updated_at->lt(now()->subSeconds(self::STALL_SECONDS));
    }

    public function forget(): void
    {
        ChunkedUpload::find($this->id, $this->user_id)?->discard();
        $this->delete();
    }
}
