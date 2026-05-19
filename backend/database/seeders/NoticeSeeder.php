<?php

namespace Database\Seeders;

use App\Models\Notice;
use Illuminate\Database\Seeder;

class NoticeSeeder extends Seeder
{
    public function run(): void
    {
        Notice::firstOrCreate(
            ['title' => 'サービス開始のお知らせ'],
            [
                'body' => "この度、動画配信サービスをご利用いただきありがとうございます。\nさまざまな作品をお楽しみください。",
                'published_at' => now()->subDays(7),
                'expired_at' => null,
            ]
        );

        Notice::firstOrCreate(
            ['title' => '新作映画リリース'],
            [
                'body' => "今月の新作映画が追加されました。ぜひチェックしてください。",
                'published_at' => now()->subDays(2),
                'expired_at' => now()->addDays(30),
            ]
        );
    }
}
