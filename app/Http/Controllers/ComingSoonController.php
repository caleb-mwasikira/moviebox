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

        return view('pages.coming-soon', compact('films'));
    }

    public function show(Movie $comingSoon)
    {
        abort_unless($comingSoon->coming_soon, 404);
        $comingSoon->loadMissing('posters');
        return view('pages.coming-soon-show', compact('comingSoon'));
    }
}
