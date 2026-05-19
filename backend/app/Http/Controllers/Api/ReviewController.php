<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Review\StoreReviewRequest;
use App\Http\Requests\Api\Review\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewController extends Controller
{
    public function index(int $videoId): AnonymousResourceCollection
    {
        $video = Video::published()->findOrFail($videoId);
        $reviews = $video->reviews()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(20);

        return ReviewResource::collection($reviews);
    }

    public function store(StoreReviewRequest $request, int $videoId): JsonResponse
    {
        $video = Video::published()->findOrFail($videoId);
        $user = auth('api')->user();

        $review = Review::updateOrCreate(
            ['user_id' => $user->id, 'video_id' => $video->id],
            ['rating' => $request->rating, 'comment' => $request->comment]
        );
        $review->load('user');

        return response()->json(new ReviewResource($review), 201);
    }

    public function update(UpdateReviewRequest $request, int $id): JsonResponse
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== auth('api')->id()) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $review->fill($request->validated());
        $review->save();
        $review->load('user');

        return response()->json(new ReviewResource($review));
    }

    public function destroy(int $id): JsonResponse
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== auth('api')->id()) {
            return response()->json(['message' => '権限がありません'], 403);
        }

        $review->delete();

        return response()->json(null, 204);
    }
}
