<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Profile;
use App\Models\Series;
use Illuminate\Http\Request;

class ReelsController extends Controller
{
    /** Max titles loaded into the swipe feed. */
    private const FEED_SIZE = 24;

    public function index(Request $request, ?string $vid = null)
    {
        $hasAccess = $this->hasAccess();
        $items = collect();

        // One reel per title. Each holds a sequence of videos: the trailer first (when there is
        // one), then the full movie or the episodes. Left/right swipes move through the sequence;
        // up/down moves to a different title.
        $movies = Movie::where(fn ($q) => $q->where('status', 'published')->orWhere('coming_soon', true))->get();

        foreach ($movies as $movie) {
            $videos = collect();

            if ($movie->trailerIsReady()) {
                $videos->push($this->trailer($movie));
            }

            if (! $movie->coming_soon && $movie->status === 'published' && $movie->videoIsReady()) {
                // Paid videos stay in the sequence but are locked (no source is sent to the browser).
                $locked = ! $hasAccess && ! $movie->is_free;
                $videos->push([
                    'vid'    => $movie->uuid,
                    'kind'   => 'movie',
                    'season' => null,
                    'label'  => 'Full movie',
                    'locked' => $locked,
                    'hls'    => (bool) $movie->streaming_path,
                    'src'    => $locked ? null : ($movie->streaming_path ? $movie->streamingUrl() : $movie->videoUrl()),
                ]);
            }

            // A reel needs at least one video the viewer can actually play.
            if ($videos->contains(fn ($v) => ! $v['locked'])) {
                $items->push($this->item('movie:' . $movie->uuid, $movie->coming_soon ? 'coming_soon' : 'movie', $movie,
                    $movie->coming_soon ? route('coming-soon.show', $movie) : route('movies.show', $movie), $videos));
            }
        }

        $series = Series::where('status', 'published')
            ->with(['seasons' => fn ($q) => $q->orderBy('season_number'), 'seasons.episodes' => fn ($q) => $q->orderBy('episode_number')])
            ->get();

        foreach ($series as $show) {
            $videos = collect();

            if ($show->trailerIsReady()) {
                $videos->push($this->trailer($show));
            }

            foreach ($show->seasons as $season) {
                foreach ($season->episodes as $ep) {
                    if (! $ep->videoIsReady()) {
                        continue;
                    }
                    $locked = ! $hasAccess && ! $ep->is_free;
                    $videos->push([
                        'vid'    => $ep->uuid,
                        'kind'   => 'episode',
                        'season' => $season->season_number,
                        'label'  => $ep->label(),
                        'locked' => $locked,
                        'hls'    => (bool) $ep->streaming_path,
                        'src'    => $locked ? null : ($ep->streaming_path ? $ep->streamingUrl() : $ep->videoUrl()),
                    ]);
                }
            }

            if ($videos->contains(fn ($v) => ! $v['locked'])) {
                $items->push($this->item('series:' . $show->uuid, 'series', $show, route('series.show', $show), $videos));
            }
        }

        // Feed order: shuffled, with featured titles nudged towards the front.
        $feed = $items->shuffle()->sortByDesc('featured')->values()->take(self::FEED_SIZE);

        // Deep link: /reels/{vid} opens that exact video (trailer, movie or episode id).
        if ($vid) {
            $picked = $items->first(fn ($i) => collect($i['episodes'])->contains('vid', $vid));
            abort_unless($picked, 404);
            $feed = $feed->reject(fn ($i) => $i['key'] === $picked['key'])->prepend($picked)->values();
        }

        return view('pages.reels', [
            'feed'      => $feed->map(fn ($i) => collect($i)->except('featured')->all())->values(),
            'hasAccess' => $hasAccess,
            'startVid'  => $vid,
        ]);
    }

    private function trailer($model): array
    {
        $src = $model->trailerUrl();

        return [
            'vid'    => 'trailer-' . $model->uuid,
            'kind'   => 'trailer',
            'season' => null,
            'label'  => 'Trailer',
            'locked' => false,
            'hls'    => str_contains($src, '.m3u8'),
            'src'    => $src,
        ];
    }

    private function item(string $key, string $type, $model, string $url, $videos): array
    {
        return [
            'key'         => $key,
            'type'        => $type,
            'title'       => $model->title,
            'genre'       => $model->genre,
            'year'        => $model->year,
            'description' => $model->description,
            'poster'      => $model->thumbnailUrl(),
            'url'         => $url,
            'featured'    => (bool) $model->featured,
            'episodes'    => $videos->values()->all(),
        ];
    }

    /** Mirrors RequireActiveSubscription: whose subscription governs this session. */
    private function hasAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }
        if ($user->isAdmin()) {
            return true;
        }
        if (session('shared_guest')) {
            return false;
        }

        $subscription = $user->currentSubscription;

        if ($profileId = session('active_profile_id')) {
            $profile = Profile::find($profileId);
            if ($profile && $profile->user_id !== $user->id) {
                $owner = $profile->user;
                $subscription = ($owner?->isBanned() || $profile->isDisabled()) ? null : $owner?->currentSubscription;
            }
        }

        return (bool) $subscription;
    }
}
