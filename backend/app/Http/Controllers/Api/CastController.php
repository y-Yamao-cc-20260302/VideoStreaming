<?php

namespace App\Http\Controllers\Api;

use App\Models\Cast;
use App\Http\Controllers\Controller;
use App\Http\Resources\CastSummaryResource;
use App\Http\Resources\CastDetailResource;
use App\Http\Requests\Api\Cast\SearchCastRequest;

// リクエスト処理
use Illuminate\Http\JsonResponse;

//作品取得
use App\Http\Resources\VideoSummaryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CastController extends Controller
{
    public function index(SearchCastRequest $request): AnonymousResourceCollection
    {
        $query = Cast::publish()->with(['occupation']);
        if ($slug = $request->string('occupation')->toString()) {
            $query->whereHas('occupation', fn($q) => $q->where('slug', $slug));
        }

        if ($keyword = $request->string('keyword')->toString()) {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $keyword) . '%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'ILIKE', $like);
            });
        }

        $sort = $request->string('sort', 'name')->toString();
        match ($sort) {
            'birthday' => $query->orderByDesc('birthday'),
            'occupation' => $query->orderBy('occupation_id'),
            default => $query->orderBy('name'),
        };

        $casts = $query->paginate(10);
        return CastSummaryResource::collection($casts);
    }

    public function show(int $id): JsonResponse
    {
        $cast = Cast::publish()->with('occupation')->findOrFail($id);

        if ($user = auth('api')->user()) {
            $cast->is_favored = $cast->castfavorites()->where('user_id', $user->id)->exists();
        } else {
            $cast->is_favored = false;
        }

        return response()->json(new CastDetailResource($cast));
    }

    public function video(int $id)
    {
        $cast = Cast::publish()->findOrFail($id);
        $videos = $cast->video()->published()->get();

        return VideoSummaryResource::collection($videos);
    }
}
