<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Subtitle extends Model
{
    protected $fillable = [
        'language',
        'language_code',
        'file_path',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function subtitleable(): MorphTo
    {
        return $this->morphTo();
    }

    public function fileUrl(): string
    {
        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://') || str_starts_with($this->file_path, '/')) {
            return $this->file_path;
        }
        return asset('storage/' . $this->file_path);
    }
}
