<?php

// 使用するコントローラのuse文を追加する
use App\Http\Controllers\Api\CastController;
use App\Http\Controllers\Api\OccupationController;
use App\Http\Controllers\Api\CastFavoriteController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\NoticeController;
use App\Http\Controllers\Api\PaymentHistoryController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\WatchHistoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 認証不要
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::get('categories', [CategoryController::class, 'index']);
Route::get('genres', [GenreController::class, 'index']);

Route::get('videos', [VideoController::class, 'index']);
Route::get('videos/new', [VideoController::class, 'newReleases']);
Route::get('videos/popular', [VideoController::class, 'popular']);
Route::get('videos/{id}', [VideoController::class, 'show'])->whereNumber('id');
Route::get('videos/{id}/reviews', [ReviewController::class, 'index'])->whereNumber('id');

Route::get('subscription-plans', [SubscriptionPlanController::class, 'index']);

Route::get('notices', [NoticeController::class, 'index']);
Route::get('notices/{id}', [NoticeController::class, 'show'])->whereNumber('id');

// アクセスするためのルートを追記する
Route::get('casts', [CastController::class, 'index']);
Route::get('casts/{id}', [CastController::class, 'show'])->whereNumber('id');
Route::get('casts/{id}/video', [CastController::class, 'video'])->whereNumber('id');
// 職業データにアクセスするためのルートを追記する
Route::get('occupations', [OccupationController::class, 'index']);

/*
|--------------------------------------------------------------------------
| 認証必要（JWT）
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::patch('me', [AuthController::class, 'updateProfile']);
        Route::patch('me/password', [AuthController::class, 'changePassword']);
        Route::delete('me', [AuthController::class, 'withdraw']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    Route::get('videos/recommended', [VideoController::class, 'recommended']);
    Route::post('videos/{id}/progress', [VideoController::class, 'progress'])->whereNumber('id');

    Route::get('favorites', [FavoriteController::class, 'index']);
    Route::post('favorites', [FavoriteController::class, 'store']);
    Route::delete('favorites/{video_id}', [FavoriteController::class, 'destroy'])->whereNumber('video_id');

    Route::post('videos/{id}/reviews', [ReviewController::class, 'store'])->whereNumber('id');
    Route::patch('reviews/{id}', [ReviewController::class, 'update'])->whereNumber('id');
    Route::delete('reviews/{id}', [ReviewController::class, 'destroy'])->whereNumber('id');

    Route::get('watch-histories', [WatchHistoryController::class, 'index']);

    Route::get('subscriptions/current', [SubscriptionController::class, 'current']);
    Route::post('subscriptions', [SubscriptionController::class, 'store']);
    Route::delete('subscriptions/current', [SubscriptionController::class, 'destroy']);

    Route::get('payment-histories', [PaymentHistoryController::class, 'index']);

    Route::post('/casts/{cast_id}/favorite', [CastFavoriteController::class, 'favorite'])->whereNumber('cast_id');
});
