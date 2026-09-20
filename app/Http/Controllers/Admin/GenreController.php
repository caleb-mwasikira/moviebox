<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    /** Add a genre (or return the existing one when the name matches, ignoring case). */
    public function store(Request $request)
    {
        $request->merge(['name' => trim(preg_replace('/\s+/', ' ', (string) $request->input('name')))]);
        $request->validate(['name' => ['required', 'string', 'max:60']]);

        $name = $request->input('name');
        $existing = Genre::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
        $genre = $existing ?? Genre::create(['name' => $name]);

        return response()->json(['name' => $genre->name, 'created' => $existing === null], $existing ? 200 : 201);
    }
}
