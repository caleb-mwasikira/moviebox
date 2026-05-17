<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Season;
use App\Models\Series;
use Illuminate\Http\Request;

class ConvertController extends Controller
{
    public function movieToEpisode(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'season_id'       => ['nullable', 'integer', 'exists:seasons,id'],
            'new_series_name' => ['nullable', 'string', 'max:255'],
            'episode_number'  => ['required', 'integer', 'min:1'],
        ]);

        if (empty($validated['season_id']) && empty($validated['new_series_name'])) {
            return back()->withErrors(['season_id' => 'Select an existing season or enter a new series name.']);
        }

        if (!empty($validated['new_series_name'])) {
            $series = Series::create([
                'title'       => $validated['new_series_name'],
                'description' => $movie->description ?? '',
                'status'      => 'hidden',
                'genre'       => $movie->genre,
                'year'        => $movie->year,
                'thumbnail'   => $movie->thumbnail,
            ]);
            $season = $series->seasons()->create(['season_number' => 1]);
        } else {
            $season = Season::with('series')->findOrFail($validated['season_id']);
        }

        $episode = $season->episodes()->create([
            'episode_number' => $validated['episode_number'],
            'title'          => $movie->title,
            'description'    => $movie->description,
            'duration'       => $movie->duration,
            'is_free'        => $movie->is_free,
            'video_path'     => $movie->video_path,
            'streaming_path' => $movie->streaming_path,
        ]);

        foreach ($movie->subtitles as $subtitle) {
            $episode->subtitles()->create(
                $subtitle->only(['language', 'language_code', 'file_path', 'is_default'])
            );
        }

        // Nullify paths so the Movie destructor won't delete the files we just transferred
        $movie->update(['video_path' => null, 'streaming_path' => null]);
        $movie->delete();

        return redirect()
            ->route('admin.series.edit', $season->series)
            ->with('success', "Movie converted to {$episode->label()} successfully.");
    }

    public function episodeToMovie(Episode $episode)
    {
        $series = $episode->season->series;

        $movie = Movie::create([
            'title'          => $episode->title,
            'description'    => $episode->description ?? '',
            'duration'       => $episode->duration,
            'genre'          => $series->genre,
            'year'           => $series->year,
            'thumbnail'      => $series->thumbnail,
            'is_free'        => $episode->is_free,
            'status'         => 'hidden',
            'video_path'     => $episode->video_path,
            'streaming_path' => $episode->streaming_path,
        ]);

        foreach ($episode->subtitles as $subtitle) {
            $movie->subtitles()->create(
                $subtitle->only(['language', 'language_code', 'file_path', 'is_default'])
            );
        }

        $episode->update(['video_path' => null, 'streaming_path' => null]);
        $episode->delete();

        return redirect()
            ->route('admin.movies.edit', $movie)
            ->with('success', 'Episode converted to movie. Review the details and publish when ready.');
    }
}
