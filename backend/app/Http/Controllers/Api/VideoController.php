<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Video\ProgressRequest;
use App\Http\Resources\VideoDetailResource;
use App\Http\Resources\VideoSummaryResource;
use App\Models\Video;
use App\Models\WatchHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Video::published()->with(['category', 'genres']);

        if ($slug = $request->string('category')->toString()) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
        }

        if ($slug = $request->string('genre')->toString()) {
            $query->whereHas('genres', fn ($q) => $q->where('slug', $slug));
        }

        if ($keyword = $request->string('keyword')->toString()) {
            $like = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $keyword).'%';
            $query->where(function ($q) use ($like) {
                $q->where('title', 'ILIKE', $like)
                    ->orWhere('description', 'ILIKE', $like);
            });
        }

        $sort = $request->string('sort', 'new')->toString();
        match ($sort) {
            'release_date' => $query->orderByDesc('release_date'),
            'popular'      => $query->withCount(['watchHistories as recent_watch_count' => function ($q) {
                $q->where('watched_at', '>=', now()->subDays(7));
            }])->orderByDesc('recent_watch_count'),
            default        => $query->orderByDesc('release_date')->orderByDesc('id'),
        };

        $perPage = min(max((int) $request->integer('per_page', 20), 1), 100);
        $videos = $query->paginate($perPage);

        return VideoSummaryResource::collection($videos);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $video = Video::published()
            ->with(['category', 'genres'])
            ->findOrFail($id);

        $ratings = $video->reviews()
            ->selectRaw('AVG(rating) as avg, COUNT(*) as cnt')
            ->first();

        $video->rating_avg = $ratings?->avg !== null ? (float) $ratings->avg : null;
        $video->rating_count = (int) ($ratings?->cnt ?? 0);

        if ($user = auth('api')->user()) {
            $video->is_favored = $video->favorites()->where('user_id', $user->id)->exists();
            $history = WatchHistory::where('user_id', $user->id)
                ->where('video_id', $video->id)
                ->first();
            $video->progress_sec = $history?->progress_sec ?? 0;
        } else {
            $video->is_favored = false;
            $video->progress_sec = 0;
        }

        return response()->json(new VideoDetailResource($video));
    }

    public function newReleases(): AnonymousResourceCollection
    {
        $videos = Video::published()
            ->with(['category', 'genres'])
            ->orderByDesc('release_date')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return VideoSummaryResource::collection($videos);
    }

    public function popular(): AnonymousResourceCollection
    {
        $videos = Video::published()
            ->with(['category', 'genres'])
            ->withCount(['watchHistories as recent_watch_count' => function ($q) {
                $q->where('watched_at', '>=', now()->subDays(7));
            }])
            ->orderByDesc('recent_watch_count')
            ->limit(20)
            ->get();

        return VideoSummaryResource::collection($videos);
    }

    public function recommended(): AnonymousResourceCollection
    {
        $user = auth('api')->user();

        $watchedCategoryIds = DB::table('watch_histories')
            ->join('videos', 'videos.id', '=', 'watch_histories.video_id')
            ->where('watch_histories.user_id', $user->id)
            ->pluck('videos.category_id')
            ->unique()
            ->values()
            ->all();

        $watchedVideoIds = $user->watchHistories()->pluck('video_id')->all();

        $query = Video::published()->with(['category', 'genres'])
            ->whereNotIn('id', $watchedVideoIds);

        if ($watchedCategoryIds) {
            $query->whereIn('category_id', $watchedCategoryIds);
        }

        $videos = $query->orderByDesc('release_date')->limit(20)->get();

        if ($videos->count() < 10) {
            $extra = Video::published()
                ->with(['category', 'genres'])
                ->whereNotIn('id', $videos->pluck('id'))
                ->whereNotIn('id', $watchedVideoIds)
                ->orderByDesc('release_date')
                ->limit(20 - $videos->count())
                ->get();
            $videos = $videos->concat($extra);
        }

        return VideoSummaryResource::collection($videos);
    }

    public function progress(ProgressRequest $request, int $id): JsonResponse
    {
        $video = Video::published()->findOrFail($id);
        $user = auth('api')->user();

        WatchHistory::updateOrCreate(
            ['user_id' => $user->id, 'video_id' => $video->id],
            [
                'progress_sec' => $request->progress_sec,
                'watched_at'   => now(),
            ]
        );

        return response()->json(null, 204);
    }
}
