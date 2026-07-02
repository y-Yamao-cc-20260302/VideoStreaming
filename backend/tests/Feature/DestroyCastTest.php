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
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyCastTest extends TestCase
{
    // テスト実行するたびにデータベースをロールバックまでしてくれる機能
    use RefreshDatabase;

    // occupation_idを有効にするため、職業テーブルに値を登録(seedと同じものを登録している)
    protected $seeder = OccupationSeeder::class;
    private string $dummyPicturePath = '';

    protected function setUp(): void
    {
        //データベース使用のため必須
        parent::setUp();
        // 認証を入れる
        Admin::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Laravel11からの書き方、csrfトークンの無効化
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        // 画像アップロード含め、ダミーデータを作成する
        // 仮ストレージを生成
        Storage::fake('public');
        // 仮データの画像を作成する
        $picture = UploadedFile::fake()->image('image.png', 200, 200)->size(3072);
        // ファイルネームを作る
        $filename = $picture->hashName();
        // pictureストレージに作成したファイル名で格納する
        Storage::disk('public')->putFileAs('picture', $picture, $filename);
        // 写真パスを作成する
        $this->dummyPicturePath = 'picture/' . $filename;
    }

    // データプロバイダのアノテーション()が必要。　しゃーぷ[DataProvider('関数名')]と書き、インポートもする。
    // 引数は、データ型を特定するため、array型と定義
    // 削除可能、画像有
    #[DataProvider('DestroyDataProvider')]
    public function test_destroy(array $queryParams, array $assertDatabaseHas = [])
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();

        // ダミーデータ生成のため、picture_pathカラムに写真パスを入れる
        $queryParams['picture_path'] = $this->dummyPicturePath;
        // 削除する用のダミーデータを生成
        $cast = Cast::create($queryParams);
        // 期待結果にも写真パスを入れる
        $assertDatabaseHas['picture_path'] = $this->dummyPicturePath;
        // 管理者を取得
        $admin = Admin::where('name', 'admin')->first();
        // 変数urlを作成
        $url = "admin/casts/{$cast->id}";
        // 管理者権限を付与してgetアクションする
        $response = $this->actingAs($admin, 'admin')->delete($url);
        // 通信成功チェック
        $response->assertStatus(302);
        // 論理削除されているかを確認する
        $this->assertSoftDeleted('casts', $assertDatabaseHas);
        // 画像が削除されているか確認
        Storage::disk('public')->assertMissing($cast->picture_path);
    }

    // データプロバイダのアノテーション()が必要。　しゃーぷ[DataProvider('関数名')]と書き、インポートもする。
    // 引数は、データ型を特定するため、array型と定義
    // 削除可能、画像なし
    #[DataProvider('DestroyNoImageDataProvider')]
    public function test_no_image_destroy(array $queryParams, array $assertDatabaseHas = [])
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();

        // 削除する用のダミーデータを生成
        $cast = Cast::create($queryParams);
        // 管理者を取得
        $admin = Admin::where('name', 'admin')->first();
        // 変数urlを作成
        $url = "admin/casts/{$cast->id}";
        // 管理者権限を付与してgetアクションする
        $response = $this->actingAs($admin, 'admin')->delete($url);
        // 通信成功チェック
        $response->assertStatus(302);
        // 論理削除されているかを確認する
        $this->assertSoftDeleted('casts', $assertDatabaseHas);
    }

    // 削除に失敗する場合
    #[DataProvider('DontDestroyDataProvider')]
    public function test_dont_destroy(array $queryParams, array $assertDatabaseHas)
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();

        // データベースからデータを取得する際の、該当データがない場合のエラーを確認する
        $this->expectException(ModelNotFoundException::class);
        // ダミーデータ生成のため、picture_pathカラムに写真パスを入れる
        $queryParams['picture_path'] = $this->dummyPicturePath;
        // 期待結果にも写真パスを入れる
        $assertDatabaseHas['picture_path'] = $this->dummyPicturePath;
        // // 削除する用のダミーデータを生成
        $cast = Cast::create($queryParams);
        // // 画像が正しくアップロードされているか
        Storage::disk('public')->assertExists($this->dummyPicturePath);

        // データを作成してすぐに削除することで、該当データが取得できないようにする
        $cast->delete();
        // この消し方では画像は同時に消えないため、個別に画像を削除している
        Storage::disk('public')->delete($this->dummyPicturePath);
        // すでに論理削除されているかを確認する
        $this->assertSoftDeleted('casts', $assertDatabaseHas);
        // すでに画像が削除されているか確認
        Storage::disk('public')->assertMissing($this->dummyPicturePath);

        // 管理者を取得
        $admin = Admin::where('name', 'admin')->first();
        // 変数urlを作成
        $url = "admin/casts/{$cast->id}";
        // 管理者権限を付与してgetアクションする
        $response = $this->actingAs($admin, 'admin')->delete($url);
        // 通信成功チェック
        $response->assertStatus(302);
        // 論理削除されているかを確認する
        $this->assertSoftDeleted('casts', $assertDatabaseHas);
        // 画像が削除されているか確認
        Storage::disk('public')->assertMissing($this->dummyPicturePath);
    }

    // データプロバイダーはstaticとarrayが必須
    public static function destroyDataProvider(): array
    {
        return [
            // テストケース18に相当 if文true、DB登録可能
            'allTrue' => [
                'queryParams'    => [
                    'name' => '田中涼子',
                    'gender' => 2,
                    'birthday' => "1987-06-11",
                    'is_publish' => true,
                    'occupation_id' => 1,
                    // ファイル名.png ,幅,高さでダミー画像を生成する
                    'picture' => UploadedFile::fake()->image('image.png', 200, 200),
                ],
                'assertDatabaseHas' => [
                    'name' => '田中涼子',
                    'gender' => 2,
                    'birthday' => "1987-06-11",
                    'is_publish' => true,
                    'occupation_id' => 1,
                    'picture_path' => '',
                ],

            ],
        ];
    }

    // データプロバイダーはstaticとarrayが必須
    public static function destroyNoImageDataProvider(): array
    {
        return [
            // テストケース19に相当 旧写真なし、DB登録可能
            'oldImageNo' => [
                'queryParams'    => [
                    'name' => '山下涼子',
                    'gender' => 2,
                    'birthday' => "1987-06-11",
                    'is_publish' => true,
                    'occupation_id' => 1,
                    'picture_path' => '',
                ],
                'assertDatabaseHas' => [
                    'name' => '山下涼子',
                    'gender' => 2,
                    'birthday' => "1987-06-11",
                    'is_publish' => true,
                    'occupation_id' => 1,
                    'picture_path' => ''
                ],
            ],
        ];
    }

    // データプロバイダーはstaticとarrayが必須。
    public static function DontDestroyDataProvider(): array
    {
        return [
            // テストケース20に相当 データ登録失敗のテスト
            'destroyFalse' => [
                'queryParams'    => [
                    'name' => '北田太郎',
                    'gender' => 1,
                    'birthday' => "1989-08-09",
                    'is_publish' => true,
                    'occupation_id' => 2,
                    'picture' => UploadedFile::fake()->image('image.png', 200, 200),
                ],
                'assertDatabaseHas' => [
                    'name' => '北田太郎',
                    'gender' => 1,
                    'birthday' => "1989-08-09",
                    'is_publish' => true,
                    'occupation_id' => 2,
                    'picture_path' => ''
                ],
            ],
        ];
    }
}
