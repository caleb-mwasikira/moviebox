<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poster;
use Illuminate\Support\Facades\Storage;

class PosterController extends Controller
{
    public function destroy(Poster $poster)
    {
        Storage::disk('public')->delete($poster->path);
        $poster->delete();

        return back()->with('status', 'poster-deleted');
    }
}
