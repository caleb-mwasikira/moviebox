<?php

namespace App\Http\Controllers;

use App\Models\Collection;

class CollectionController extends Controller
{
    public function show(Collection $collection)
    {
        abort_if(! $collection->is_active, 404);

        $collection->load(['items.collectible.posters']);

        return view('pages.collections.show', compact('collection'));
    }
}
