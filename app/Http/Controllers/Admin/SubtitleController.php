<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Subtitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubtitleController extends Controller
{
    public function storeForMovie(Request $request, Movie $movie)
    {
        $this->validateAndStore($request, $movie);
        return back()->with('success', 'Subtitle track added.');
    }

    public function storeForEpisode(Request $request, Episode $episode)
    {
        $this->validateAndStore($request, $episode);
        return back()->with('success', 'Subtitle track added.');
    }

    public function destroy(Subtitle $subtitle)
    {
        if (!str_starts_with($subtitle->file_path, 'http') && !str_starts_with($subtitle->file_path, '/')) {
            Storage::disk('public')->delete($subtitle->file_path);
        }
        $subtitle->delete();
        return back()->with('success', 'Subtitle track removed.');
    }

    private function validateAndStore(Request $request, Movie|Episode $model): void
    {
        $data = $request->validate([
            'language'      => 'required|string|max:50',
            'language_code' => 'required|string|max:10',
            'subtitle_file' => 'nullable|file|mimes:vtt,srt|max:5120',
            'subtitle_url'  => 'nullable|string|max:1000',
            'is_default'    => 'nullable|boolean',
        ]);

        if (!$request->hasFile('subtitle_file') && empty($data['subtitle_url'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'subtitle_file' => 'Please upload a file or provide a URL.',
            ]);
        }

        if ($request->hasFile('subtitle_file')) {
            $file    = $request->file('subtitle_file');
            $content = file_get_contents($file->getRealPath());
            if (strtolower($file->getClientOriginalExtension()) === 'srt') {
                $content = $this->srtToVtt($content);
            }
            $folder = $model instanceof Movie ? 'movies' : 'episodes';
            $slug   = Str::slug($data['language']);
            $path   = "subtitles/{$folder}/{$model->uuid}/{$slug}.vtt";
            Storage::disk('public')->put($path, $content);
        } else {
            $path = $data['subtitle_url'];
        }

        if ($request->boolean('is_default')) {
            $model->subtitles()->update(['is_default' => false]);
        }

        $model->subtitles()->create([
            'language'      => $data['language'],
            'language_code' => strtolower($data['language_code']),
            'file_path'     => $path,
            'is_default'    => $request->boolean('is_default'),
        ]);
    }

    private function srtToVtt(string $srt): string
    {
        $srt = ltrim($srt, "\xEF\xBB\xBF"); // strip UTF-8 BOM
        return "WEBVTT\n\n" . preg_replace('/(\d{2}:\d{2}:\d{2}),(\d{3})/', '$1.$2', $srt);
    }
}
