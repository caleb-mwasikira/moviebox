<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Series;

class TrashController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $movies = Movie::onlyTrashed()->latest('deleted_at')->get();
        $series = Series::onlyTrashed()->latest('deleted_at')->get();

        return view('pages.admin.trash.index', compact('movies', 'series'));
    }
}
