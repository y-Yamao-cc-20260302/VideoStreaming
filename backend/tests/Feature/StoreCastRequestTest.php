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
use Illuminate\Http\UploadedFile;

class StoreCastRequestTest extends TestCase
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
    }

    // データプロバイダのアノテーション()が必要。　しゃーぷ[DataProvider('関数名')]と書き、インポートもする。
    // 引数は、データ型を特定するため、array型と定義

    #[DataProvider('StoreDataProvider')]
    public function test_store_request(array $queryParams, array $assertDatabaseHas = [])
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // 管理者を取得
        $admin = Admin::where('name', 'admin')->first();
        // コントローラを直接呼ばず、Laravelのpostで送信する。postの場合は第二引数をqueryParamsにする
        $response = $this->actingAs($admin, 'admin')
            // htmlコードも一緒に出力され、エラーメッセージがわかるオプション
            ->followingRedirects()
            ->post(route('admin.casts.store'), $queryParams);
        //通信ができたかどうか
        $response->assertStatus(200);
        // 最後に登録されたデータを引っ張ってくる(nameのカラムが、引数のnameと同じものを探している書き方)
        $cast = Cast::where('name', $queryParams['name'])->latest()->first();
        // 写真パスを取得
        $picture_path = $cast->picture_path;
        // 期待結果のほうの写真パスに、取得した写真パスを格納
        $assertDatabaseHas['picture_path'] = $picture_path;
        // DBの内容が期待結果と一致しているかを検証
        $this->assertDatabaseHas('casts', $assertDatabaseHas);
        // 画像が正しくアップロードされているか
        Storage::disk('public')->assertExists($cast->picture_path);
    }


    // 登録に失敗する場合
    #[DataProvider('DontStoreDataProvider')]
    public function test_dont_store_request(array $queryParams)
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // 管理者を取得
        $admin = Admin::where('name', 'admin')->first();
        $this->expectExceptionMessage('名前は255文字以内で入力してください[ERR-CAST-005]');
        // コントローラを直接呼ばず、Laravelのpostで送信する。postの場合は第二引数をqueryParamsにする
        $this->actingAs($admin, 'admin')
            // htmlコードも一緒に出力され、エラーメッセージがわかるオプション
            ->followingRedirects()
            ->post(route('admin.casts.store'), $queryParams);
    }

    // データプロバイダーはstaticとarrayが必須
    public static function StoreDataProvider(): array
    {
        return [
            // テストケース7に相当バリデーションを通り、DB登録可能
            'allTrue' => [
                'queryParams'    => [
                    'name' => '吉田太郎',
                    'gender' => 1,
                    'birthday' => "1987-06-11",
                    'is_publish' => true,
                    'occupation_id' => 1,
                    // ファイル名.png ,幅,高さでダミー画像を生成する
                    'picture' => UploadedFile::fake()->image('image.png', 200, 200),
                ],
                'assertDatabaseHas' => [
                    'name' => '吉田太郎',
                    'gender' => 1,
                    'occupation_id' => 1,
                    'birthday' => "1987-06-11",
                    'is_publish' => true,
                    'picture_path' => ''
                ],
            ],
        ];
    }

    // データプロバイダーはstaticとarrayが必須
    public static function DontStoreDataProvider(): array
    {
        return [
            // テストケース8に相当 バリデーション失敗のテスト
            'validateFalse' => [
                'queryParams'    => [
                    // バリデーションエラーが起きる256文字の文字列
                    'name' => '256文字の文字列ですあああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ',
                    'gender' => 1,
                    'birthday' => "1987-06-11",
                    'publish' => false,
                    'occupation_id' => 1,
                    'picture' => UploadedFile::fake()->image('image.png', 200, 200)->size(5121),
                ],
            ],
        ];
    }
}
