<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Series;
use App\Models\WatchParty;
use App\Models\WatchPartyEpisode;
use App\Models\WatchPartyMember;
use App\Models\WatchPartyMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WatchPartyController extends Controller
{
    // ── Dashboard list ────────────────────────────────────────────────────────

    public function index()
    {
        $user = Auth::user();

        $parties = WatchParty::where('host_id', $user->id)
            ->where('status', '!=', 'ended')
            ->with('watchable')
            ->latest()
            ->get();

        $joined = WatchPartyMember::where('user_id', $user->id)
            ->whereHas('party', fn($q) => $q->where('status', '!=', 'ended'))
            ->with(['party.host', 'party.watchable'])
            ->latest()
            ->get();

        return view('pages.account.watch-party.index', compact('parties', 'joined'));
    }

    public function create()
    {
        $user = Auth::user();

        if (! $user->canUseWatchParty()) {
            return redirect()->route('pricing')
                ->with('error', 'Watch Parties require a paid plan with Watch Party access.');
        }

        $movies = Movie::where('status', 'published')
            ->where(fn($q) => $q->whereNotNull('video_path')->orWhereNotNull('streaming_path'))
            ->orderBy('title')
            ->get(['id', 'title', 'uuid']);

        $series = Series::where('status', 'published')
            ->with([
                'seasons' => fn($q) => $q->orderBy('season_number'),
                'seasons.episodes' => fn($q) => $q
                    ->where(fn($q) => $q->whereNotNull('video_path')->orWhereNotNull('streaming_path'))
                    ->orderBy('episode_number'),
            ])
            ->get(['id', 'title', 'uuid']);

        return view('pages.account.watch-party.create', compact('movies', 'series'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user->canUseWatchParty()) {
            return redirect()->route('pricing')
                ->with('error', 'Watch Parties require a paid plan with Watch Party access.');
        }

        $validated = $request->validate([
            'watchable_type' => ['required', 'in:movie,series'],
            'movie_id'       => ['required_if:watchable_type,movie', 'nullable', 'integer'],
            'series_id'      => ['required_if:watchable_type,series', 'nullable', 'integer'],
            'episode_ids'    => ['required_if:watchable_type,series', 'nullable', 'array', 'min:1'],
            'episode_ids.*'  => ['integer', 'exists:episodes,id'],
        ]);

        if ($validated['watchable_type'] === 'movie') {
            $model = Movie::findOrFail($validated['movie_id']);
            $party = WatchParty::create([
                'host_id'        => $user->id,
                'watchable_type' => Movie::class,
                'watchable_id'   => $model->id,
                'token'          => Str::random(12),
                'status'         => 'waiting',
                'expires_at'     => now()->addDays(7),
            ]);
        } else {
            $seriesModel = Series::findOrFail($validated['series_id']);
            $episodeIds  = $validated['episode_ids'];

            $party = WatchParty::create([
                'host_id'            => $user->id,
                'watchable_type'     => Series::class,
                'watchable_id'       => $seriesModel->id,
                'token'              => Str::random(12),
                'status'             => 'waiting',
                'current_episode_id' => $episodeIds[0],
                'expires_at'         => now()->addDays(7),
            ]);

            foreach ($episodeIds as $order => $episodeId) {
                WatchPartyEpisode::create([
                    'watch_party_id' => $party->id,
                    'episode_id'     => $episodeId,
                    'sort_order'     => $order,
                ]);
            }
        }

        WatchPartyMember::create([
            'watch_party_id' => $party->id,
            'user_id'        => $user->id,
            'joined_at'      => now(),
            'last_seen_at'   => now(),
        ]);

        return redirect()->route('watch-party.show', $party->token);
    }

    // ── Party room ────────────────────────────────────────────────────────────

    public function show(string $token)
    {
        $party = WatchParty::where('token', $token)
            ->with(['host', 'watchable', 'members.user', 'episodes.episode'])
            ->firstOrFail();

        $user  = Auth::user();
        $guest = !$user ? session('watch_party_guest') : null;

        $isHost   = $user && $user->id === $party->host_id;
        $isMember = $user
            ? $party->members->contains(fn($m) => $m->user_id === $user->id && is_null($m->kicked_at))
            : (bool) $guest;
        $canAccess = $user ? $user->canUseWatchParty() : (bool) $guest;
        $isGuest   = (bool) $guest;
        $guestName = $guest['name'] ?? null;
        $guestId   = $guest['id'] ?? null;

        $watchable = $party->watchable;
        $isMovie   = $watchable instanceof Movie;
        $isSeries  = $watchable instanceof Series;

        // "Watch alone" URL — carry the party's current position into the standalone player
        $watchAloneUrl = null;
        if ($isMovie) {
            $watchAloneUrl = route('movies.watch', $watchable) . '?t=' . floor($party->playback_position ?? 0);
        } elseif ($isSeries && $party->current_episode_id) {
            $ep = $party->currentEpisode;
            if ($ep) {
                $watchAloneUrl = route('series.watch', [$watchable, $ep]) . '?t=' . floor($party->playback_position ?? 0);
            }
        }

        // HLS auth token (valid 2 hours from page load)
        $uid      = $user?->id ?? 0;
        $exp      = time() + 7200;
        $sig      = hash_hmac('sha256', $uid . ':' . $exp, config('services.streaming.secret'));
        $hlsToken = base64_encode($uid . ':' . $exp . ':' . $sig);

        // Main video source
        $videoSrc = null;
        $useHls   = false;
        if ($isMovie) {
            $videoSrc = $watchable->streamingUrl() ?? $watchable->videoUrl();
            $useHls   = (bool) $watchable->streaming_path;
        } elseif ($isSeries && $party->current_episode_id) {
            $ep       = $party->currentEpisode;
            $videoSrc = $ep?->streamingUrl() ?? $ep?->videoUrl();
            $useHls   = (bool) $ep?->streaming_path;
        }

        // Trailer (for countdown screen)
        $trailerSrc = null;
        if ($isMovie && $watchable->trailerIsReady()) {
            $trailerSrc = $watchable->trailerUrl();
        } elseif ($isSeries && $watchable->trailerIsReady()) {
            $trailerSrc = $watchable->trailerUrl();
        }

        // Episode playlist for series parties
        $episodePlaylist = $isSeries
            ? $party->episodes->map(fn($e) => [
                'id'    => $e->episode_id,
                'label' => $e->episode?->label() ?? '—',
                'url'   => $e->episode ? route('series.watch', [$watchable, $e->episode]) : null,
            ])->values()
            : collect();

        $currentEpisodeId = $party->current_episode_id;
        $startsAt         = $party->starts_at?->timestamp;

        $streamingWsUrl = rtrim(
            str_replace(['https://', 'http://'], ['wss://', 'ws://'],
                config('app.url')
            ),
            '/'
        );

        $lobbyActive = (bool) $party->lobby_active;

        return view('pages.watch-party.show', compact(
            'party', 'isHost', 'isMember', 'canAccess',
            'isGuest', 'guestName', 'guestId',
            'isMovie', 'isSeries',
            'hlsToken', 'videoSrc', 'useHls',
            'trailerSrc', 'episodePlaylist', 'currentEpisodeId', 'startsAt',
            'streamingWsUrl', 'lobbyActive', 'watchAloneUrl'
        ));
    }

    // ── Guest join (session only, no account) ────────────────────────────────

    public function guestJoin(Request $request, string $token)
    {
        $party = WatchParty::where('token', $token)->firstOrFail();

        if ($party->isEnded()) {
            return redirect()->route('watch-party.show', $token)
                ->withErrors(['name' => 'This watch party has ended.']);
        }

        $request->validate(['name' => ['required', 'string', 'min:2', 'max:50']]);

        session(['watch_party_guest' => [
            'name' => trim($request->name),
            'id'   => 'g_' . Str::random(20),
        ]]);

        return redirect()->route('watch-party.show', $token);
    }

    // ── Join ─────────────────────────────────────────────────────────────────

    public function join(string $token)
    {
        $party = WatchParty::where('token', $token)->firstOrFail();

        if ($party->isEnded()) {
            return redirect()->route('watch-party.show', $token)
                ->with('error', 'This watch party has ended.');
        }

        $user = Auth::user();

        if (! $user->canUseWatchParty()) {
            return redirect()->route('pricing')
                ->with('error', 'Watch Parties require a paid plan with Watch Party access. Upgrade to join.');
        }

        $existing = WatchPartyMember::where('watch_party_id', $party->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing && $existing->kicked_at) {
            return redirect()->route('watch-party.show', $token)
                ->with('error', 'You have been removed from this watch party.');
        }

        WatchPartyMember::firstOrCreate(
            ['watch_party_id' => $party->id, 'user_id' => $user->id],
            ['joined_at' => now(), 'last_seen_at' => now()],
        );

        if ($party->status === 'waiting') {
            $party->update(['status' => 'live']);
        }

        return redirect()->route('watch-party.show', $token);
    }

    public function leave(string $token)
    {
        $party = WatchParty::where('token', $token)->firstOrFail();
        $user  = Auth::user();

        if ($user->id === $party->host_id) {
            $party->update(['status' => 'ended', 'ended_at' => now()]);
            $this->pushToWS($party->token, 'WatchPartyEnded', []);
        }

        return redirect()->route('account.watch-party.index');
    }

    public function end(string $token)
    {
        $party = WatchParty::where('token', $token)
            ->where('host_id', Auth::id())
            ->firstOrFail();

        $party->update(['status' => 'ended', 'ended_at' => now()]);

        $this->pushToWS($party->token, 'WatchPartyEnded', []);

        return redirect()->route('account.watch-party.index')
            ->with('success', 'Watch party ended.');
    }

    // ── Sync (host → server) ──────────────────────────────────────────────────

    public function sync(Request $request, string $token): JsonResponse
    {
        $party = WatchParty::where('token', $token)
            ->where('host_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'position'   => ['required', 'numeric', 'min:0'],
            'is_playing' => ['required', 'boolean'],
        ]);

        $party->update([
            'playback_position'   => $request->input('position'),
            'is_playing'          => $request->boolean('is_playing'),
            'playback_updated_at' => now(),
            'status'              => 'live',
        ]);

        WatchPartyMember::where('watch_party_id', $party->id)
            ->where('user_id', Auth::id())
            ->update(['last_seen_at' => now()]);

        $this->pushToWS($party->token, 'WatchPartySync', [
            'position'   => (float) $request->input('position'),
            'is_playing' => $request->boolean('is_playing'),
        ], Auth::id()); // exclude the host — they triggered the sync

        return response()->json(['ok' => true]);
    }

    // ── Lobby (host only) ────────────────────────────────────────────────────

    public function toggleLobby(string $token): JsonResponse
    {
        $party = WatchParty::where('token', $token)
            ->where('host_id', Auth::id())
            ->firstOrFail();

        $party->update(['lobby_active' => ! $party->lobby_active]);

        $this->pushToWS($party->token, 'WatchPartyLobbyChanged', [
            'active' => $party->lobby_active,
        ]);

        return response()->json(['lobby_active' => $party->lobby_active]);
    }

    // ── Countdown (host only) ─────────────────────────────────────────────────

    public function setCountdown(Request $request, string $token): JsonResponse
    {
        $party = WatchParty::where('token', $token)
            ->where('host_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'minutes' => ['required', 'integer', 'min:0', 'max:5'],
        ]);

        $minutes = $request->integer('minutes');
        $party->update([
            'starts_at' => $minutes > 0 ? now()->addMinutes($minutes) : null,
        ]);

        return response()->json(['starts_at' => $party->starts_at?->timestamp]);
    }

    // ── Next episode (host only) ──────────────────────────────────────────────

    public function nextEpisode(string $token): JsonResponse
    {
        $party = WatchParty::where('token', $token)
            ->where('host_id', Auth::id())
            ->firstOrFail();

        $playlist   = WatchPartyEpisode::where('watch_party_id', $party->id)->orderBy('sort_order')->get();
        $currentIdx = $playlist->search(fn($e) => $e->episode_id === $party->current_episode_id);
        $next       = $currentIdx !== false ? $playlist->get($currentIdx + 1) : null;

        if (! $next) {
            return response()->json(['error' => 'No more episodes in playlist.'], 422);
        }

        $episode = Episode::find($next->episode_id);

        $party->update([
            'current_episode_id'  => $next->episode_id,
            'playback_position'   => 0,
            'is_playing'          => false,
            'playback_updated_at' => now(),
        ]);

        $this->pushToWS($party->token, 'WatchPartyEpisodeChanged', [
            'current_episode_id' => $episode->id,
            'episode' => [
                'id'      => $episode->id,
                'label'   => $episode->label(),
                'src'     => $episode->streamingUrl() ?? $episode->videoUrl(),
                'use_hls' => (bool) $episode->streaming_path,
            ],
        ]);

        return response()->json([
            'current_episode_id' => $next->episode_id,
            'episode' => [
                'id'      => $episode->id,
                'label'   => $episode->label(),
                'src'     => $episode->streamingUrl() ?? $episode->videoUrl(),
                'use_hls' => (bool) $episode->streaming_path,
            ],
        ]);
    }

    // ── Jump to episode (host only) ───────────────────────────────────────────

    public function jumpToEpisode(Request $request, string $token): JsonResponse
    {
        $party = WatchParty::where('token', $token)
            ->where('host_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'episode_id' => ['required', 'integer'],
        ]);

        $entry = WatchPartyEpisode::where('watch_party_id', $party->id)
            ->where('episode_id', $request->integer('episode_id'))
            ->firstOrFail();

        $episode = Episode::findOrFail($entry->episode_id);

        $party->update([
            'current_episode_id'  => $episode->id,
            'playback_position'   => 0,
            'is_playing'          => false,
            'playback_updated_at' => now(),
        ]);

        $this->pushToWS($party->token, 'WatchPartyEpisodeChanged', [
            'current_episode_id' => $episode->id,
            'episode' => [
                'id'      => $episode->id,
                'label'   => $episode->label(),
                'src'     => $episode->streamingUrl() ?? $episode->videoUrl(),
                'use_hls' => (bool) $episode->streaming_path,
            ],
        ]);

        return response()->json([
            'current_episode_id' => $episode->id,
            'episode' => [
                'id'      => $episode->id,
                'label'   => $episode->label(),
                'src'     => $episode->streamingUrl() ?? $episode->videoUrl(),
                'use_hls' => (bool) $episode->streaming_path,
            ],
        ]);
    }

    // ── Chat message ─────────────────────────────────────────────────────────

    // ── Kick / unkick (host only) ─────────────────────────────────────────────

    public function kick(Request $request, string $token): JsonResponse
    {
        $party = WatchParty::where('token', $token)
            ->where('host_id', Auth::id())
            ->with('members.user')
            ->firstOrFail();

        $request->validate(['username' => ['required', 'string', 'max:255']]);

        $member = $party->members->first(
            fn($m) => strcasecmp($m->user->name, $request->input('username')) === 0
        );

        if (! $member) {
            return response()->json(['error' => 'User not found in party.'], 404);
        }

        if ($member->user_id === Auth::id()) {
            return response()->json(['error' => 'You cannot kick yourself.'], 422);
        }

        $member->update(['kicked_at' => now(), 'last_seen_at' => null]);

        $this->pushToWS($party->token, 'WatchPartyKicked', ['user_id' => $member->user_id]);

        $msg = WatchPartyMessage::create([
            'watch_party_id' => $party->id,
            'user_id'        => Auth::id(),
            'message'        => $member->user->name . ' was removed from the party.',
            'is_system'      => true,
        ]);
        $msg->load('user');

        $this->pushToWS($party->token, 'WatchPartyChatMessage', $this->formatMessage($msg, $party->host_id));

        return response()->json(['ok' => true, 'username' => $member->user->name]);
    }

    public function unkick(Request $request, string $token): JsonResponse
    {
        $party = WatchParty::where('token', $token)
            ->where('host_id', Auth::id())
            ->with('members.user')
            ->firstOrFail();

        $request->validate(['username' => ['required', 'string', 'max:255']]);

        $member = $party->members->first(
            fn($m) => strcasecmp($m->user->name, $request->input('username')) === 0
                   && $m->kicked_at !== null
        );

        if (! $member) {
            return response()->json(['error' => 'No banned user with that name.'], 404);
        }

        $member->update(['kicked_at' => null]);

        $msg = WatchPartyMessage::create([
            'watch_party_id' => $party->id,
            'user_id'        => Auth::id(),
            'message'        => $member->user->name . ' can rejoin the party.',
            'is_system'      => true,
        ]);
        $msg->load('user');

        $this->pushToWS($party->token, 'WatchPartyChatMessage', $this->formatMessage($msg, $party->host_id));

        return response()->json(['ok' => true, 'username' => $member->user->name]);
    }

    // ── Chat message ─────────────────────────────────────────────────────────

    public function sendMessage(Request $request, string $token): JsonResponse
    {
        $party = WatchParty::where('token', $token)->firstOrFail();
        $user  = Auth::user();
        $guest = !$user ? session('watch_party_guest') : null;

        if (!$user && !$guest) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate(['message' => ['required', 'string', 'max:500']]);

        if ($user) {
            $activeMember = $party->members->first(
                fn($m) => $m->user_id === $user->id && is_null($m->kicked_at)
            );

            if (!$activeMember) {
                return response()->json(['error' => 'Not a member.'], 403);
            }

            $msg = WatchPartyMessage::create([
                'watch_party_id' => $party->id,
                'user_id'        => $user->id,
                'message'        => $request->input('message'),
            ]);
            $msg->setRelation('user', $user);

            $this->pushToWS($party->token, 'WatchPartyChatMessage', $this->formatMessage($msg, $party->host_id));

            return response()->json([
                'ok'      => true,
                'message' => [
                    'id'        => $msg->id,
                    'user_id'   => $user->id,
                    'name'      => $user->name,
                    'message'   => $msg->message,
                    'is_host'   => $user->id === $party->host_id,
                    'time'      => $msg->created_at->format('H:i'),
                    'is_mine'   => true,
                    'is_system' => false,
                ],
            ]);
        }

        // Guest: push directly to WS — no DB record
        $msgId   = 'g_' . uniqid();
        $payload = [
            'id'        => $msgId,
            'user_id'   => $guest['id'],
            'name'      => $guest['name'],
            'message'   => $request->input('message'),
            'is_host'   => false,
            'time'      => now()->format('H:i'),
            'is_mine'   => false,
            'is_system' => false,
        ];
        $this->pushToWS($party->token, 'WatchPartyChatMessage', $payload);

        return response()->json(['ok' => true, 'message' => array_merge($payload, ['is_mine' => true])]);
    }

    // ── State (server → participants, polled every 2.5s) ─────────────────────

    public function state(Request $request, string $token): JsonResponse
    {
        $party = WatchParty::where('token', $token)->firstOrFail();
        $user  = Auth::user();
        $guest = !$user ? session('watch_party_guest') : null;

        if (!$user && !$guest) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($user) {
            $myMembership = WatchPartyMember::where('watch_party_id', $party->id)
                ->where('user_id', $user->id)
                ->first();

            if ($myMembership && $myMembership->kicked_at) {
                return response()->json(['status' => 'kicked']);
            }

            WatchPartyMember::where('watch_party_id', $party->id)
                ->where('user_id', $user->id)
                ->update(['last_seen_at' => now()]);
        }

        $online = WatchPartyMember::where('watch_party_id', $party->id)
            ->whereNull('kicked_at')
            ->where('last_seen_at', '>=', now()->subSeconds(15))
            ->with('user:id,name')
            ->get()
            ->map(fn($m) => ['name' => $m->user->name, 'is_host' => $m->user_id === $party->host_id]);

        $episodeData = null;
        if ($party->current_episode_id) {
            $ep = Episode::find($party->current_episode_id);
            if ($ep) {
                $episodeData = [
                    'id'      => $ep->id,
                    'label'   => $ep->label(),
                    'src'     => $ep->streamingUrl() ?? $ep->videoUrl(),
                    'use_hls' => (bool) $ep->streaming_path,
                ];
            }
        }

        $since    = $request->integer('since', 0);
        $messages = WatchPartyMessage::where('watch_party_id', $party->id)
            ->where('id', '>', $since)
            ->with('user:id,name')
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->map(fn($m) => [
                'id'        => $m->id,
                'name'      => $m->user->name,
                'message'   => $m->message,
                'is_host'   => $m->user_id === $party->host_id,
                'time'      => $m->created_at->format('H:i'),
                'is_mine'   => $user && $m->user_id === $user->id,
                'is_system' => (bool) $m->is_system,
            ])
            ->values();

        return response()->json([
            'status'             => $party->status,
            'lobby_active'       => (bool) $party->lobby_active,
            'is_playing'         => $party->is_playing,
            'position'           => $party->playback_position,
            'updated_at'         => $party->playback_updated_at?->timestamp,
            'starts_at'          => $party->starts_at?->timestamp,
            'current_episode_id' => $party->current_episode_id,
            'episode'            => $episodeData,
            'members'            => $online,
            'messages'           => $messages,
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function formatMessage(WatchPartyMessage $msg, int $hostId): array
    {
        return [
            'id'        => $msg->id,
            'user_id'   => $msg->user_id,
            'name'      => $msg->user->name,
            'message'   => $msg->message,
            'is_host'   => $msg->user_id === $hostId,
            'time'      => $msg->created_at->format('H:i'),
            'is_system' => (bool) $msg->is_system,
        ];
    }

    private function pushToWS(string $token, string $event, array $data, int $excludeUserId = 0): void
    {
        $payload = ['event' => $event, 'data' => $data];
        if ($excludeUserId) {
            $payload['exclude_user_id'] = $excludeUserId;
        }

        try {
            Http::withHeaders(['X-Api-Key' => config('services.streaming.secret')])
                ->timeout(3)
                ->post(config('services.streaming.url') . '/watch-party/' . $token . '/broadcast', $payload);
        } catch (\Throwable $e) {
            \Log::warning('Watch party WS push failed: ' . $e->getMessage());
        }
    }
}
