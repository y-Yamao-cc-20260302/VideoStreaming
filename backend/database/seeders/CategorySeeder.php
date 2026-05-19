<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => '映画', 'slug' => 'movie', 'sort_order' => 1],
            ['name' => 'ドラマ', 'slug' => 'drama', 'sort_order' => 2],
            ['name' => 'アニメ', 'slug' => 'anime', 'sort_order' => 3],
            ['name' => 'バラエティ', 'slug' => 'variety', 'sort_order' => 4],
            ['name' => 'ドキュメンタリー', 'slug' => 'documentary', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
