<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cast_videos', function (Blueprint $table) {
            $table->id();
            //foreignId(カラム名) constrained(テーブル名)の書き方で、外部キー制約を作成できる
            $table->foreignId('cast_id')->constrained('casts');
            // constrained()と空でも、video_idのvideoの部分からvideosテーブルを自動で探す
            $table->foreignId('video_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cast_videos');
    }
};
