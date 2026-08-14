<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $movie = Category::where('slug', 'movie')->first();
        $drama = Category::where('slug', 'drama')->first();
        $anime = Category::where('slug', 'anime')->first();

        $action = Genre::where('slug', 'action')->first();
        $comedy = Genre::where('slug', 'comedy')->first();
        $sf = Genre::where('slug', 'sf')->first();
        $fantasy = Genre::where('slug', 'fantasy')->first();
        $mystery = Genre::where('slug', 'mystery')->first();

        $sampleStream = 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8';

        $samples = [
            [
                'category' => $movie,
                'genres' => [$action, $sf],
                'data' => [
                    'title' => '宇宙の果てへ',
                    'description' => '人類最後の希望を背負った宇宙船の壮大な旅路を描く SF アクション大作。',
                    'thumbnail_path' => null,
                    'stream_url' => $sampleStream,
                    'duration_sec' => 7200,
                    'release_date' => '2025-12-01',
                    'is_published' => true,
                ],
            ],
            [
                'category' => $movie,
                'genres' => [$comedy],
                'data' => [
                    'title' => 'コメディ・ナイト',
                    'description' => '笑いと涙のクリスマス・コメディ。',
                    'thumbnail_path' => null,
                    'stream_url' => $sampleStream,
                    'duration_sec' => 6000,
                    'release_date' => '2025-11-20',
                    'is_published' => true,
                ],
            ],
            [
                'category' => $drama,
                'genres' => [$mystery],
                'data' => [
                    'title' => '消えた手紙',
                    'description' => '小さな村で起こった消失事件を追う、心理サスペンスドラマ。',
                    'thumbnail_path' => null,
                    'stream_url' => $sampleStream,
                    'duration_sec' => 3000,
                    'release_date' => '2026-01-10',
                    'is_published' => true,
                ],
            ],
            [
                'category' => $anime,
                'genres' => [$fantasy, $action],
                'data' => [
                    'title' => '魔法学院の冒険',
                    'description' => '魔法学院に入学した少年少女たちが繰り広げる成長と冒険の物語。',
                    'thumbnail_path' => null,
                    'stream_url' => $sampleStream,
                    'duration_sec' => 1440,
                    'release_date' => '2026-02-05',
                    'is_published' => true,
                ],
            ],
            [
                'category' => $anime,
                'genres' => [$sf],
                'data' => [
                    'title' => 'メカニカル・サマー',
                    'description' => '近未来の都市で目覚めた巨大ロボと少女の絆。',
                    'thumbnail_path' => null,
                    'stream_url' => $sampleStream,
                    'duration_sec' => 1440,
                    'release_date' => '2026-03-15',
                    'is_published' => true,
                ],
            ],
            [
                'category' => $drama,
                'genres' => [$comedy],
                'data' => [
                    'title' => 'カフェの片隅で',
                    'description' => '小さなカフェに集う人々の人間ドラマ。',
                    'thumbnail_path' => null,
                    'stream_url' => $sampleStream,
                    'duration_sec' => 2700,
                    'release_date' => '2026-04-01',
                    'is_published' => true,
                ],
            ],
            [
                'category' => $drama,
                'genres' => [$comedy],
                'data' => [
                    'title' => 'Java',
                    'description' => 'バックエンド',
                    'thumbnail_path' => null,
                    'stream_url' => $sampleStream,
                    'duration_sec' => 2700,
                    'release_date' => '2026-04-01',
                    'is_published' => true,
                ],
            ],
            [
                'category' => $drama,
                'genres' => [$comedy],
                'data' => [
                    'title' => 'テストコメディ',
                    'description' => 'テストコメディ',
                    'thumbnail_path' => null,
                    'stream_url' => $sampleStream,
                    'duration_sec' => 2700,
                    'release_date' => '2026-04-01',
                    'is_published' => true,
                ],
            ],
            [
                'category' => $drama,
                'genres' => [$comedy],
                'data' => [
                    'title' => 'テストドラマ',
                    'description' => 'テストドラマ',
                    'thumbnail_path' => null,
                    'stream_url' => $sampleStream,
                    'duration_sec' => 2700,
                    'release_date' => '2026-04-01',
                    'is_published' => true,
                ],
            ],
            [
                'category' => $anime,
                'genres' => [$comedy],
                'data' => [
                    'title' => 'テストアニメ',
                    'description' => 'テストアニメ',
                    'thumbnail_path' => null,
                    'stream_url' => $sampleStream,
                    'duration_sec' => 2700,
                    'release_date' => '2026-04-01',
                    'is_published' => true,
                ],
            ],
            [
                'category' => $drama,
                'genres' => [$comedy],
                'data' => [
                    'title' => 'テスト',
                    'description' => 'テスト',
                    'thumbnail_path' => null,
                    'stream_url' => $sampleStream,
                    'duration_sec' => 2700,
                    'release_date' => '2026-04-01',
                    'is_published' => true,
                ],
            ],
            [
                'category' => $drama,
                'genres' => [$comedy],
                'data' => [
                    'title' => 'React',
                    'description' => 'フロント',
                    'thumbnail_path' => null,
                    'stream_url' => $sampleStream,
                    'duration_sec' => 2700,
                    'release_date' => '2026-04-01',
                    'is_published' => true,
                ],
            ],
            [
                'category' => $drama,
                'genres' => [$comedy],
                'data' => [
                    'title' => 'あ',
                    'description' => 'あ',
                    'thumbnail_path' => null,
                    'stream_url' => $sampleStream,
                    'duration_sec' => 2700,
                    'release_date' => '2026-04-01',
                    'is_published' => true,
                ],
            ],
        ];

        foreach ($samples as $sample) {
            if (! $sample['category']) {
                continue;
            }

            $video = Video::firstOrCreate(
                ['title' => $sample['data']['title']],
                array_merge($sample['data'], ['category_id' => $sample['category']->id])
            );

            $genreIds = collect($sample['genres'])->filter()->pluck('id')->all();
            if ($genreIds) {
                $video->genres()->sync($genreIds);
            }
        }
    }
}
