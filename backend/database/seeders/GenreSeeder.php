<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            ['name' => 'アクション', 'slug' => 'action'],
            ['name' => 'コメディ', 'slug' => 'comedy'],
            ['name' => 'ロマンス', 'slug' => 'romance'],
            ['name' => 'SF', 'slug' => 'sf'],
            ['name' => 'ホラー', 'slug' => 'horror'],
            ['name' => 'ファンタジー', 'slug' => 'fantasy'],
            ['name' => 'ミステリー', 'slug' => 'mystery'],
            ['name' => 'スポーツ', 'slug' => 'sports'],
        ];

        foreach ($genres as $genre) {
            Genre::updateOrCreate(['slug' => $genre['slug']], $genre);
        }
    }
}
