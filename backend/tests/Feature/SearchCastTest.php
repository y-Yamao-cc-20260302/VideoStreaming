<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Models\Cast;
use App\Models\Admin;
use Database\Seeders\OccupationSeeder;
use Hash;

class SearchCastTest extends TestCase
{
    // テスト実行するたびにデータベースをロールバックまでしてくれる機能
    use RefreshDatabase;

    // Seedファイルを実行し職業データをDBに入れる(seedと同じものを登録している)
    protected $seeder = OccupationSeeder::class;
    protected function setUp(): void
    {
        //データベース使用のため必須.
        parent::setUp();

        //認証を入れる
        Admin::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // テストデータを登録
        Cast::factory()->create(['name' => '田中太郎', 'is_publish' => 1, 'occupation_id' => 1]);
        Cast::factory()->create(['name' => '非公開出演者', 'is_publish' => 0, 'occupation_id' => 1]);
        Cast::factory()->create(['name' => '田中次郎', 'is_publish' => '0', 'occupation_id' => 1, 'deleted_at' => '2026-06-05 02:30:20.000']);
    }

    // データプロバイダのアノテーション()が必要。　しゃーぷ[DataProvider('関数名')]と書き、インポートもする。
    // 引数は、データ型を特定するため、array型と定義
    // assertText=[]の空白は、無いなら空にするという意味
    #[DataProvider('searchDataProvider')]
    public function test_search(array $queryParams, array $assertSeeText = [], array $assertDontSeeText = [])
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
        // 不要部分が出ないことを確認
        $response->assertDontSeeText($assertDontSeeText);
    }

    // データプロバイダーはstaticとarrayが必須
    public static function searchDataProvider(): array
    {
        return [
            // テストケース1に相当 全if文trueのテスト
            'allTrue' => [
                'queryParams'    => ['keyword' => '田中太郎', 'publish' => '1', 'occupation_id' => 1,],
                'assertSeeText' => ['田中太郎', '俳優', '公開中'],
            ],
            // テストケース２に相当 公開設定elseのテスト
            'publishFalse' => [
                'queryParams'    => ['keyword' => '非公開出演者', 'publish' => '0', 'occupation_id' => 1,],
                'assertSeeText'     => ['非公開出演者', '俳優', '非公開'],
            ],
            // テストケース3に相当　あいまい検索のテスト
            'fuzzySearch' => [
                'queryParams'    => ['keyword' => '田中',],
                'assertSeeText'     => ['田中太郎', '俳優', '公開中'],
            ],
            // テストケース４に相当 論理削除済みの値が出ないテスト
            'deleteNoSearch' => [
                'queryParams'    => ['keyword' => '田中次郎'],
                'assertSeeText'     => [],
                'assertDontSeeText' => ['name' => '田中次郎'],
            ],
        ];
    }
}
