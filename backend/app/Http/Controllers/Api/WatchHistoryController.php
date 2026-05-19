<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WatchHistoryResource;
use App\Models\WatchHistory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WatchHistoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $user = auth('api')->user();
        $histories = WatchHistory::with(['video.category', 'video.genres'])
            ->where('user_id', $user->id)
            ->orderByDesc('watched_at')
            ->paginate(20);

        return WatchHistoryResource::collection($histories);
    }
}
