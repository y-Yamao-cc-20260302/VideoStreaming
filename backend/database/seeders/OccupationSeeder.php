<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Occupation;

class OccupationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $occupations = [
            ['name' => '俳優', ],
            ['name' => 'お笑い芸人',],
            ['name' => 'アイドル',],
            ['name' => 'タレント',],
        ];

        foreach ($occupations as $occupation) {
            Occupation::updateOrCreate( $occupation);
        }
    }
}
