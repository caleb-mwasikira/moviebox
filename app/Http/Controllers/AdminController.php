<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;

class AdminController extends Controller
{
    public function dashboard()
    {
        $movieCount  = Movie::count();
        $seriesCount = Series::count();
        $published   = Movie::where('status', 'published')->count()
                     + Series::where('status', 'published')->count();
        $hidden      = Movie::where('status', 'hidden')->count()
                     + Series::where('status', 'hidden')->count();

        return view('pages.admin.dashboard', compact(
            'movieCount', 'seriesCount', 'published', 'hidden'
        ));
    }
}
