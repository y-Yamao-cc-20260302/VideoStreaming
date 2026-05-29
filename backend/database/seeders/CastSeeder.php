<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cast;

class CastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $casts = [
            [
                'name' => '田中太郎',
                'gender' => 1,
                'birthday' => '2020-01-01',
                'occupation_id' => 1,
                'picture_path' => null,
                'is_publish' => true,
            ],
            
            [
                'name' => '山下次郎',
                'gender' => 2,
                'birthday' => '1020-01-01',
                'occupation_id' => 2,
                'picture_path' => null,
                'is_publish' => true,
            ],
        ];
        
        foreach ($casts as $cast) {
            Cast::updateOrCreate($cast);
        }

    }
}
