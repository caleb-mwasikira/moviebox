<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;

class HomeController extends Controller
{
    public function index()
    {
        $movies = Movie::where('status', 'published')->latest()->take(7)->get();
        $series = Series::where('status', 'published')->latest()->take(4)->get();

        return view('pages.index', compact('movies', 'series'));
    }
}
