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
}
