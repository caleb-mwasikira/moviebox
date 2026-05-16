<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Movie;
use App\Models\Series;
use App\Models\User;
use App\Models\UserSubscriptions;
use App\Models\WatchProgress;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        // ── Content counts ────────────────────────────────────────────
        $movieCount  = Movie::count();
        $seriesCount = Series::count();
        $published   = Movie::where('status', 'published')->count()
                     + Series::where('status', 'published')->count();
        $hidden      = Movie::where('status', 'hidden')->count()
                     + Series::where('status', 'hidden')->count();
        $userCount   = User::count();
        $bannedCount = User::whereNotNull('banned_at')->count();

        // ── Active subscribers ────────────────────────────────────────
        $activeSubscribers = UserSubscriptions::where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->count();

        // ── Signups over time (last 30 days) ──────────────────────────
        $signupRows = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $signupLabels = [];
        $signupData   = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i)->toDateString();
            $signupLabels[] = Carbon::parse($day)->format('M j');
            $signupData[]   = (int) ($signupRows[$day] ?? 0);
        }

        // ── Most watched content (top 10) ─────────────────────────────
        $watchRows = WatchProgress::select('watchable_type', 'watchable_id', DB::raw('COUNT(*) as views'))
            ->groupBy('watchable_type', 'watchable_id')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        $movieIds   = $watchRows->where('watchable_type', 'movie')->pluck('watchable_id');
        $episodeIds = $watchRows->where('watchable_type', 'episode')->pluck('watchable_id');

        $movies   = Movie::whereIn('id', $movieIds)->pluck('title', 'id');
        $episodes = Episode::with('season.series')->whereIn('id', $episodeIds)->get()->keyBy('id');

        $mostWatched = $watchRows->map(function ($row) use ($movies, $episodes) {
            if ($row->watchable_type === 'movie') {
                return ['title' => $movies[$row->watchable_id] ?? 'Unknown', 'type' => 'Movie', 'views' => $row->views];
            }
            $ep     = $episodes[$row->watchable_id] ?? null;
            $series = $ep?->season?->series?->title ?? 'Unknown';
            $title  = $ep ? "{$series} S{$ep->season->season_number}E{$ep->episode_number}" : 'Unknown';
            return ['title' => $title, 'type' => 'Episode', 'views' => $row->views];
        });

        return view('pages.admin.dashboard', compact(
            'movieCount', 'seriesCount', 'published', 'hidden', 'userCount', 'bannedCount',
            'activeSubscribers',
            'signupLabels', 'signupData',
            'mostWatched',
        ));
    }
}
