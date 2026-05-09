<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;
use App\Models\User;

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
        $userCount   = User::count();
        $bannedCount = User::whereNotNull('banned_at')->count();

        return view('pages.admin.dashboard', compact(
            'movieCount', 'seriesCount', 'published', 'hidden', 'userCount', 'bannedCount'
        ));
    }
}
