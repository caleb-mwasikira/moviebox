<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\Series;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function store(Request $request, Series $series)
    {
        $validated = $request->validate([
            'season_number' => ['required', 'integer', 'min:1'],
            'title'         => ['nullable', 'string', 'max:255'],
        ]);

        $series->seasons()->create($validated);

        return redirect()->route('admin.series.edit', $series)->with('status', 'season-added');
    }

    public function destroy(Season $season)
    {
        $series = $season->series;
        $season->delete();

        return redirect()->route('admin.series.edit', $series)->with('status', 'season-deleted');
    }
}
