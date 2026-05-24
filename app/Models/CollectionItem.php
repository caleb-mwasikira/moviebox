<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CollectionItem extends Model
{
    protected $fillable = [
        'collection_id',
        'collectible_type',
        'collectible_id',
        'sort_order',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function collectible(): MorphTo
    {
        return $this->morphTo();
    }
}
