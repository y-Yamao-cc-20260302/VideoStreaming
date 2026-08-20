<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Occupation;

class OccupationSeeder extends Seeder
{
    public function run(): void
    {
        $occupations = [
            ['name' => '俳優', 'slug' => 'actor', 'sort_order' => 1],
            ['name' => 'お笑い芸人', 'slug' => 'comedian', 'sort_order' => 2],
            ['name' => 'アイドル', 'slug' => 'idle', 'sort_order' => 3],
            ['name' => 'タレント', 'slug' => 'talent', 'sort_order' => 4],
            ['name' => 'アナウンサー', 'slug' => 'announcer', 'sort_order' => 5],
            ['name' => '落語家', 'slug' => 'rakugo', 'sort_order' => 6],
        ];

        foreach ($occupations as $occupation) {
            Occupation::updateOrCreate(
                // カラム追加に伴い、既存DB更新不整合を防ぐため、nameの二重登録チェックを追加
                ['name' => $occupation['name']],
                $occupation
            );
        }
    }
}
