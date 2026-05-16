<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadController extends Controller
{
    public function movie(Request $request, Movie $movie)
    {
        if (! $request->user()->canDownload()) {
            return redirect()->back()->with('error', 'Downloads are not included in your current plan. Upgrade to download content for offline viewing.');
        }

        if (! $movie->video_path || ! Storage::disk('public')->exists($movie->video_path)) {
            return redirect()->back()->with('error', 'This title is not currently available for download.');
        }

        $filename = Str::slug($movie->title) . '.' . pathinfo($movie->video_path, PATHINFO_EXTENSION);

        return response()->download(Storage::disk('public')->path($movie->video_path), $filename);
    }

    public function episode(Request $request, Episode $episode)
    {
        if (! $request->user()->canDownload()) {
            return redirect()->back()->with('error', 'Downloads are not included in your current plan. Upgrade to download content for offline viewing.');
        }

        if (! $episode->video_path || ! Storage::disk('public')->exists($episode->video_path)) {
            return redirect()->back()->with('error', 'This episode is not currently available for download.');
        }

        $series   = $episode->season->series;
        $filename = Str::slug($series->title) . '-' . Str::slug($episode->label()) . '.' . pathinfo($episode->video_path, PATHINFO_EXTENSION);

        return response()->download(Storage::disk('public')->path($episode->video_path), $filename);
    }
}
