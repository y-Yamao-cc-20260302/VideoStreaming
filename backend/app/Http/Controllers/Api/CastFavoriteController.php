<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CastFavorite\StoreCastFavoriteRequest;
use App\Models\CastFavorite;
use Illuminate\Http\JsonResponse;

class CastFavoriteController extends Controller
{
    public function index() {}

    public function store(StoreCastFavoriteRequest $request): JsonResponse
    {
        $user = auth('api')->user();

        CastFavorite::firstOrCreate([
            'user_id' => $user->id,
            'cast_id' => $request->cast_id,
        ]);

        return response()->json([
            'cast_id' => (int) $request->cast_id,
            'favored_at' => now()->toIso8601String(),
        ], 201);
    }

    public function destroy(int $castId): JsonResponse
    {
        $user = auth('api')->user();

        CastFavorite::where('user_id', $user->id)
            ->where('cast_id', $castId)
            ->delete();

        return response()->json(null, 204);
    }
}
