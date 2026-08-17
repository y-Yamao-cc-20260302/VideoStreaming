<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CastFavorite\StoreCastFavoriteRequest;
use App\Models\CastFavorite;
use Illuminate\Http\JsonResponse;

class CastFavoriteController extends Controller
{
    public function favorite(StoreCastFavoriteRequest $request): JsonResponse
    {
        // ユーザーを取得
        $user = auth('api')->user();

        // castfavoritesテーブルから、ユーザーと出演者の組み合わせで検索、テーブルに存在するなら取得
        $cast_favorite = CastFavorite::where('user_id', $user->id)
            ->where('cast_id', $request->cast_id)
            ->first();

        // テーブルに存在するか判定
        if ($cast_favorite) {
            // 登録済みの場合、解除
            $cast_favorite->delete();
            return response()->json(null, 204);
        } else {
            // 未登録の場合、登録
            CastFavorite::create([
                'user_id' => $user->id,
                'cast_id' => $request->cast_id,
            ]);
            return response()->json([
                'cast_id' => (int) $request->cast_id,
                'favored_at' => now()->toIso8601String(),
            ], 201);
        }
    }
}
