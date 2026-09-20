<?php

namespace App\Support;

use Illuminate\Support\Str;

/** A partially (or fully) received upload kept on local disk until a form claims it. */
class ChunkedUpload
{
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly int $size,
        public readonly int $userId,
    ) {}

    public static function dir(): string
    {
        $dir = storage_path('app/chunked');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        return $dir;
    }

    public static function create(int $userId, string $name, int $size): string
    {
        $id = (string) Str::uuid();
        file_put_contents(self::dir() . "/{$id}.json", json_encode(compact('name', 'size', 'userId')));
        touch(self::dir() . "/{$id}.part");

        return $id;
    }

    public static function find(string $id, int $userId): ?self
    {
        if (! Str::isUuid($id) || ! is_file(self::dir() . "/{$id}.json")) {
            return null;
        }

        $meta = json_decode((string) file_get_contents(self::dir() . "/{$id}.json"), true);

        if (! is_array($meta) || (int) $meta['userId'] !== $userId) {
            return null;
        }

        return new self($id, $meta['name'], (int) $meta['size'], $userId);
    }

    public function path(): string
    {
        return self::dir() . "/{$this->id}.part";
    }

    public function received(): int
    {
        clearstatcache(true, $this->path());

        return is_file($this->path()) ? (int) filesize($this->path()) : 0;
    }

    public function isComplete(): bool
    {
        return $this->received() === $this->size;
    }

    /** Appends a stream to the file and returns the new size. */
    public function append($stream): int
    {
        $out = fopen($this->path(), 'ab');
        stream_copy_to_stream($stream, $out);
        fclose($out);

        return $this->received();
    }

    public function discard(): void
    {
        @unlink($this->path());
        @unlink(self::dir() . "/{$this->id}.json");
    }

    public static function prune(int $olderThanHours = 48): int
    {
        $count = 0;
        foreach (glob(self::dir() . '/*.json') ?: [] as $meta) {
            if (filemtime($meta) < time() - $olderThanHours * 3600 && (! is_file($part = substr($meta, 0, -5) . '.part') || filemtime($part) < time() - $olderThanHours * 3600)) {
                @unlink($part);
                @unlink($meta);
                $count++;
            }
        }

        return $count;
    }
}
