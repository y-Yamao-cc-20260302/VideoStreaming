<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CastController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login',  [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.dashboard'));
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('videos',                [VideoController::class, 'index'])->name('videos.index');
        Route::get('videos/create',         [VideoController::class, 'create'])->name('videos.create');
        Route::post('videos',               [VideoController::class, 'store'])->name('videos.store');
        Route::get('videos/{video}/edit',   [VideoController::class, 'edit'])->name('videos.edit');
        Route::patch('videos/{video}',      [VideoController::class, 'update'])->name('videos.update');
        Route::delete('videos/{video}',     [VideoController::class, 'destroy'])->name('videos.destroy');
        Route::patch('videos/{video}/publish', [VideoController::class, 'publish'])->name('videos.publish');

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('genres', GenreController::class)->except(['show']);
        Route::resource('subscription-plans', SubscriptionPlanController::class)->except(['show']);
        Route::resource('notices', NoticeController::class)->except(['show']);

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::patch('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.status');

        Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

        // 出演者一覧表示 (基本はrequest空で遷移してるだけ。)
        Route::get('casts',[CastController::class,'index'])->name('casts.index');
        // 出演者登録
        Route::get('casts/new',[CastController::class,'new'])->name('casts.new');
        // 登録ページ、保存押下
        Route::post('casts',[CastController::class,'store'])->name('casts.store');
        // 出演者編集
        Route::get('casts/{cast}/edit',[CastController::class,'edit'])->name('casts.edit');
        // 編集ページ、保存押下
        Route::patch('casts/{cast}',[CastController::class,'update'])->name('casts.update');
        // 出演者削除
        Route::delete('casts/{cast}',[CastController::class,'destroy'])->name('casts.destroy');
        // 公開設定押下
        Route::patch('casts/{cast}/publish', [CastController::class, 'publish'])->name('casts.publish');

    });
});
