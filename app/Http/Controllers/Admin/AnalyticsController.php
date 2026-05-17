<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\MpesaPayment;
use App\Models\Movie;
use App\Models\User;
use App\Models\UserSubscriptions;
use App\Models\WatchProgress;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
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

        // ── Active subscribers ────────────────────────────────────────
        $activeTotal = UserSubscriptions::where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->count();

        $byPackage = UserSubscriptions::with('package')
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->select('subscription_package_id', DB::raw('COUNT(*) as count'))
            ->groupBy('subscription_package_id')
            ->get();

        // ── Revenue — sourced from actual M-Pesa payments ─────────────
        $currency = DB::table('subscription_packages')->value('currency') ?? 'KES';

        $revenueAllTime = (int) MpesaPayment::where('status', 'paid')->sum('amount');

        $revenueThisMonth = (int) MpesaPayment::where('status', 'paid')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount');

        // Monthly revenue for the last 12 months
        $revenueMonthRows = DB::table('mpesa_payments')
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, SUM(amount) as revenue")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month');

        $revenueLabels = [];
        $revenueData   = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $revenueLabels[] = now()->subMonths($i)->format('M Y');
            $revenueData[]   = (int) ($revenueMonthRows[$key] ?? 0);
        }

        // Revenue by plan (all time, from actual payments)
        $revenueByPlan = DB::table('mpesa_payments')
            ->join('subscription_packages', 'mpesa_payments.subscription_package_id', '=', 'subscription_packages.id')
            ->where('mpesa_payments.status', 'paid')
            ->select('subscription_packages.name', DB::raw('SUM(mpesa_payments.amount) as revenue'), DB::raw('COUNT(*) as sales'))
            ->groupBy('subscription_packages.id', 'subscription_packages.name')
            ->orderByDesc('revenue')
            ->get();

        // ── Most watched content (top 15) ─────────────────────────────
        $watchRows = WatchProgress::select('watchable_type', 'watchable_id', DB::raw('COUNT(*) as views'))
            ->groupBy('watchable_type', 'watchable_id')
            ->orderByDesc('views')
            ->limit(15)
            ->get();

        $movieIds   = $watchRows->where('watchable_type', 'movie')->pluck('watchable_id');
        $episodeIds = $watchRows->where('watchable_type', 'episode')->pluck('watchable_id');

        $movies   = Movie::whereIn('id', $movieIds)->pluck('title', 'id');
        $episodes = Episode::with('season.series')->whereIn('id', $episodeIds)->get()->keyBy('id');

        $mostWatched = $watchRows->map(function ($row) use ($movies, $episodes) {
            if ($row->watchable_type === 'movie') {
                $title = $movies[$row->watchable_id] ?? 'Unknown';
                $type  = 'Movie';
            } else {
                $ep     = $episodes[$row->watchable_id] ?? null;
                $series = $ep?->season?->series?->title ?? 'Unknown';
                $title  = $ep ? "{$series} — S{$ep->season->season_number}E{$ep->episode_number}" : 'Unknown';
                $type   = 'Episode';
            }
            return ['title' => $title, 'type' => $type, 'views' => $row->views];
        });

        // ── Quick stats ───────────────────────────────────────────────
        $totalUsers   = User::count();
        $newThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();

        return view('pages.admin.analytics', compact(
            'signupLabels', 'signupData',
            'activeTotal', 'byPackage',
            'revenueAllTime', 'revenueThisMonth', 'revenueLabels', 'revenueData', 'revenueByPlan', 'currency',
            'mostWatched',
            'totalUsers', 'newThisMonth',
        ));
    }
}
