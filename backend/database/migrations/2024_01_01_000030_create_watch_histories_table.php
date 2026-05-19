<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('video_id')->constrained('videos')->cascadeOnDelete();
            $table->integer('progress_sec')->default(0);
            $table->timestampTz('watched_at');
            $table->timestamps();

            $table->unique(['user_id', 'video_id']);
            $table->index(['user_id', 'watched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_histories');
    }
};
