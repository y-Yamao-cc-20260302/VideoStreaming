<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Models\Cast;
use App\Models\Admin;
use Database\Seeders\OccupationSeeder;
use Hash;
use Storage;

class SearchCastRequestTest extends TestCase
{
    // occupation_idを有効にするため、職業テーブルに値を登録(seedと同じものを登録している)
    protected $seeder = OccupationSeeder::class;
    // テスト実行するたびにデータベースをロールバックまでしてくれる機能.
    use RefreshDatabase;

    protected function setUp(): void
    {
        //データベース使用のため必須
        parent::setUp();

        // 画像ストレージを作成
        Storage::fake('public');

        // Laravel11からの書き方、csrfトークンの無効化
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        //認証を入れる
        Admin::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        // テストデータを登録
        Cast::factory()->create(['name' => '田中太郎', 'is_publish' => 1, 'occupation_id' => 1]);
    }

    // データプロバイダのアノテーション()が必要。　しゃーぷ[DataProvider('関数名')]と書き、インポートもする。
    // 引数は、データ型を特定するため、array型と定義
    // バリデーションチェックが通り、検索ができる場合
    #[DataProvider('SearchDataProvider')]
    public function test_search_request(array $queryParams, array $assertSeeText)
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // 管理者を取得
        $admin = Admin::where('name', 'admin')->first();
        // route関数は、第二引数にqueryParams用の配列を渡すと、自動でクエリ文字列に変換してくれる
        $url = route('admin.casts.index', $queryParams);
        // コントローラを直接呼ばず、Laravelのget()を使用する。request型に変換し、コントローラに送信してくれる
        $response = $this->actingAs($admin, 'admin')->get($url);
        // 通信ができたかどうか
        $response->assertStatus(200);
        // 期待結果がが返ってくるかどうか
        $response->assertSeeText($assertSeeText);
    }

    // バリデーションエラーの場合
    #[DataProvider('DontSearchDataProvider')]
    public function test_dont_search_request(array $queryParams)
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // 管理者を取得
        $admin = Admin::where('name', 'admin')->first();
        // 期待するエラー文
        $this->expectExceptionMessage('キーワードは255文字以内で入力してください[ERR-CAST-001]');
        // route関数は、第二引数にqueryParams用の配列を渡すと、自動でクエリ文字列に変換してくれる
        $url = route('admin.casts.index', $queryParams);
        // コントローラを直接呼ばず、Laravelのget()を使用する。request型に変換し、コントローラに送信してくれる
        $this->actingAs($admin, 'admin')->get($url);
    }

    // データプロバイダーはstaticとarrayが必須
    public static function SearchDataProvider(): array
    {
        return [
            // テストケース31に相当 バリデーションが通り、検索結果が表示される場合
            'allTrue' => [
                'queryParams'    => [
                    'keyword' => '田中太郎',
                    'publish' => '1',
                    'occupation_id' => '1',
                ],
                'assertSeeText' => [
                    '田中太郎',
                    '公開中',
                    '俳優'
                ],
            ],
        ];
    }

    // データプロバイダーはstaticとarrayが必須
    public static function DontSearchDataProvider(): array
    {
        return [
            // テストケース32に相当 バリデーションが通らない場合のテスト
            'publishFalse' => [
                'queryParams'    => [
                    'keyword' => '256文字の文字列ですあああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ',
                    'publish' => '公開',
                    'occupation_id' => '俳優',
                ],
            ],
        ];
    }
}
