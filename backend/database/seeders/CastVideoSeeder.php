<?php

namespace Database\Seeders;


use App\Models\CastVideo;
use Illuminate\Database\Seeder;

class CastVideoSeeder extends Seeder
{
    public function run(): void
    {
        $cast_videos = [
            ['cast_id' => '1', 'video_id' => 1],
            ['cast_id' => '1', 'video_id' => 2],
            ['cast_id' => '1', 'video_id' => 3],
            ['cast_id' => '1', 'video_id' => 4],
            ['cast_id' => '1', 'video_id' => 5],
            ['cast_id' => '1', 'video_id' => 6],
            ['cast_id' => '1', 'video_id' => 7],
            ['cast_id' => '1', 'video_id' => 8],
            ['cast_id' => '1', 'video_id' => 9],
            ['cast_id' => '1', 'video_id' => 10],
            ['cast_id' => '1', 'video_id' => 11],
            ['cast_id' => '2', 'video_id' => 1],
            ['cast_id' => '2', 'video_id' => 2],
            ['cast_id' => '2', 'video_id' => 3],
            ['cast_id' => '2', 'video_id' => 4],
            ['cast_id' => '2', 'video_id' => 5],
            ['cast_id' => '2', 'video_id' => 6],
            ['cast_id' => '2', 'video_id' => 7],
            ['cast_id' => '2', 'video_id' => 8],
            ['cast_id' => '2', 'video_id' => 9],
            ['cast_id' => '3', 'video_id' => 1],
            ['cast_id' => '3', 'video_id' => 2],
            ['cast_id' => '3', 'video_id' => 3],
            ['cast_id' => '3', 'video_id' => 4],
            ['cast_id' => '3', 'video_id' => 5],
            ['cast_id' => '4', 'video_id' => 6],
            ['cast_id' => '5', 'video_id' => 7],
            ['cast_id' => '6', 'video_id' => 8],
            ['cast_id' => '7', 'video_id' => 9],
            ['cast_id' => '8', 'video_id' => 10],
            ['cast_id' => '9', 'video_id' => 11],
        ];

        foreach ($cast_videos as $cast_video) {
            CastVideo::updateOrCreate(
                ['cast_id' => $cast_video['cast_id'], 'video_id' => $cast_video['video_id']],
                $cast_video
            );
        }
    }
}
