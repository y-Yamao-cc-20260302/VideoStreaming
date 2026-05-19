<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Favorite\StoreFavoriteRequest;
use App\Http\Resources\VideoSummaryResource;
use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FavoriteController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $user = auth('api')->user();
        $videos = $user->favoriteVideos()
            ->with(['category', 'genres'])
            ->wherePivot('user_id', $user->id)
            ->orderByPivot('created_at', 'desc')
            ->paginate(20);

        return VideoSummaryResource::collection($videos);
    }

    public function store(StoreFavoriteRequest $request): JsonResponse
    {
        $user = auth('api')->user();

        Favorite::firstOrCreate([
            'user_id'  => $user->id,
            'video_id' => $request->video_id,
        ]);

        return response()->json([
            'video_id'   => (int) $request->video_id,
            'favored_at' => now()->toIso8601String(),
        ], 201);
    }

    public function destroy(int $videoId): JsonResponse
    {
        $user = auth('api')->user();

        Favorite::where('user_id', $user->id)
            ->where('video_id', $videoId)
            ->delete();

        return response()->json(null, 204);
    }
}
