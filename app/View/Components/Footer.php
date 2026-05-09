<?php

namespace App\View\Components;

use App\Models\Movie;
use App\Models\Series;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Footer extends Component
{
    public Collection $latestReleases;

    public function __construct()
    {
        $movies = Movie::where('status', 'published')
            ->latest()
            ->take(4)
            ->get()
            ->map(fn($m) => ['type' => 'movie', 'item' => $m]);

        $series = Series::where('status', 'published')
            ->latest()
            ->take(4)
            ->get()
            ->map(fn($s) => ['type' => 'series', 'item' => $s]);

        $this->latestReleases = $movies->concat($series)
            ->sortByDesc(fn($entry) => $entry['item']->created_at)
            ->take(4)
            ->values();
    }

    public function render()
    {
        return view('components.footer');
    }
}
