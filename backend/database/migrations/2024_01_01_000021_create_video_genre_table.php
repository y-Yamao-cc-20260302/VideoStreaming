<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_genre', function (Blueprint $table) {
            $table->foreignId('video_id')->constrained('videos')->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained('genres')->cascadeOnDelete();
            $table->primary(['video_id', 'genre_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_genre');
    }
};
