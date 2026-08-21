<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cast;

class CastSeeder extends Seeder
{

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
            [
                'name' => '吉田三郎',
                'gender' => 1,
                'birthday' => '1120-01-01',
                'occupation_id' => 3,
                'picture_path' => null,
                'is_publish' => true,
            ],
            [
                'name' => '田中史郎',
                'gender' => 1,
                'birthday' => '1110-01-01',
                'occupation_id' => 4,
                'picture_path' => null,
                'is_publish' => true,
            ],
            [
                'name' => '田中一花',
                'gender' => 2,
                'birthday' => '1110-01-01',
                'occupation_id' => 4,
                'picture_path' => null,
                'is_publish' => true,
            ],
            [
                'name' => '山下仁香',
                'gender' => 2,
                'birthday' => '1110-01-01',
                'occupation_id' => 4,
                'picture_path' => null,
                'is_publish' => true,
            ],
            [
                'name' => '吉田美香',
                'gender' => 2,
                'birthday' => '1110-01-01',
                'occupation_id' => 4,
                'picture_path' => null,
                'is_publish' => true,
            ],
            [
                'name' => '田中亭',
                'gender' => 1,
                'birthday' => '1110-01-01',
                'occupation_id' => 6,
                'picture_path' => null,
                'is_publish' => true,
            ],
            [
                'name' => 'Cassy',
                'gender' => 2,
                'birthday' => '1110-01-01',
                'occupation_id' => 3,
                'picture_path' => null,
                'is_publish' => true,
            ],
            [
                'name' => 'John',
                'gender' => 1,
                'birthday' => '1110-01-01',
                'occupation_id' => 2,
                'picture_path' => null,
                'is_publish' => true,
            ],
            [
                'name' => 'Java',
                'gender' => 1,
                'birthday' => '1110-01-01',
                'occupation_id' => 6,
                'picture_path' => null,
                'is_publish' => true,
            ],
            [
                'name' => 'React',
                'gender' => 1,
                'birthday' => '1110-01-01',
                'occupation_id' => 5,
                'picture_path' => null,
                'is_publish' => true,
            ],
        ];

        foreach ($casts as $cast) {
            Cast::updateOrCreate($cast);
        }
    }
}
