<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Models\Cast;
use App\Models\Admin;
use Exception;
use App\Models\Occupation;
use Hash;

class SearchCastTest extends TestCase
{
    // テスト実行するたびにデータベースをロールバックまでしてくれる機能
    use RefreshDatabase;

    // 動作確認用　trueだけを返すテスト
    public function test_exsample()
    {
        $this->assertTrue(true);
    }

    protected function setUp(): void
    {
        //データベース使用のため必須.
        parent::setUp();

        // occupation_idを有効にするため、職業テーブルに値を登録(seedと同じものを登録している)
        Occupation::factory()->count(6)->sequence(
            ['id' => 1, 'name' => '俳優',],
            ['id' => 2, 'name' => 'お笑い芸人',],
            ['id' => 3, 'name' => 'アイドル',],
            ['id' => 4, 'name' => 'タレント',],
            ['id' => 5, 'name' => 'アナウンサー',],
            ['id' => 6, 'name' => '落語家',],
        )->create();

        // テストデータを登録
        Cast::factory()->create(['name' => '田中太郎', 'is_publish' => 1, 'occupation_id' => 1]);
        Cast::factory()->create(['name' => '非公開出演者', 'is_publish' => 0, 'occupation_id' => 1]);
        Cast::factory()->create(['name' => '田中次郎', 'is_publish' => '0', 'occupation_id' => '1', 'deleted_at' => '2026-06-05 02:30:20.000']);
    }

    // データプロバイダのアノテーション()が必要。　しゃーぷ[DataProvider('関数名')]と書き、インポートもする。
    // 引数は、データ型を特定するため、array型と定義

    #[DataProvider('searchDataProvider')]
    public function test_search(array $queryParams, array $assertSeeText = [], array $assertDontSeeText = [])
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        //認証を入れる
        $admin = Admin::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

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
