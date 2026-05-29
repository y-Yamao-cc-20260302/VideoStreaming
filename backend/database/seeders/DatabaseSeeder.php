<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            CategorySeeder::class,
            GenreSeeder::class,
            SubscriptionPlanSeeder::class,
            VideoSeeder::class,
            NoticeSeeder::class,
            OccupationSeeder::class,
            CastSeeder::class,
        ]);
    }
}
