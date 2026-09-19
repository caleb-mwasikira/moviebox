<?php

namespace App\Http\Controllers;

use App\Models\Movie;

class ComingSoonController extends Controller
{
    public function index()
    {
        $films = Movie::where('coming_soon', true)
            ->with('posters')
            ->latest()
            ->get();

        $featured = $films->sortByDesc('featured')->values()->take(5);
        $featured->each(fn ($f) => $f->content_type = 'coming_soon');

        return view('pages.coming-soon', compact('films', 'featured'));
    }

    public function show(Movie $comingSoon)
    {
        abort_unless($comingSoon->coming_soon, 404);
        $comingSoon->loadMissing('posters');
        return view('pages.coming-soon-show', compact('comingSoon'));
    }
}
