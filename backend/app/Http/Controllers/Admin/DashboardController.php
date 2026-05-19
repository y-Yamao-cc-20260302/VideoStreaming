<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Video;
use App\Models\WatchHistory;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalUsers = User::where('status', '!=', 'withdrawn')->count();
        $paidUsers = Subscription::where('status', 'active')
            ->whereHas('plan', fn ($q) => $q->where('price_jpy', '>', 0))
            ->distinct('user_id')
            ->count('user_id');

        $startOfMonth = now()->startOfMonth();
        $monthlyWatchCount = WatchHistory::where('watched_at', '>=', $startOfMonth)->count();
        $monthlyNewUsers = User::where('created_at', '>=', $startOfMonth)->count();

        $popularVideos = Video::published()
            ->withCount(['watchHistories as recent_watch_count' => function ($q) {
                $q->where('watched_at', '>=', now()->subDays(7));
            }])
            ->orderByDesc('recent_watch_count')
            ->limit(10)
            ->get();

        $recentReviews = Review::with(['user', 'video'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalUsers',
            'paidUsers',
            'monthlyWatchCount',
            'monthlyNewUsers',
            'popularVideos',
            'recentReviews'
        ));
    }
}
