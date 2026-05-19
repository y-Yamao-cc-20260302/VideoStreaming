<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'code' => 'free',
                'name' => '無料プラン',
                'price_jpy' => 0,
                'description' => '広告あり・一部作品のみ視聴可能',
                'is_active' => true,
            ],
            [
                'code' => 'standard',
                'name' => 'スタンダード',
                'price_jpy' => 980,
                'description' => '全作品見放題・HD 画質・同時 2 デバイス',
                'is_active' => true,
            ],
            [
                'code' => 'premium',
                'name' => 'プレミアム',
                'price_jpy' => 1980,
                'description' => '全作品見放題・4K 画質・同時 4 デバイス',
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['code' => $plan['code']], $plan);
        }
    }
}
