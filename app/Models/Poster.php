<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Poster extends Model
{
    protected $fillable = ['posterable_id', 'posterable_type', 'path', 'sort_order'];

    public function posterable(): MorphTo
    {
        return $this->morphTo();
    }

    public function url(): string
    {
        return asset('storage/' . $this->path);
    }
}
