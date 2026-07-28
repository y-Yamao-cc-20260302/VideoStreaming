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

class UpdateCastTest extends TestCase
{
    // occupation_idを有効にするため、職業テーブルに値を登録(seedと同じものを登録している)
    protected $seeder = OccupationSeeder::class;
    // テスト実行するたびにデータベースをロールバックまでしてくれる機能
    use RefreshDatabase;
    // クラスのプロパティとして宣言
    private string $dummyPicturePath = '';

    protected function setUp(): void
    {
        //データベース使用のため必須
        parent::setUp();

        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // Laravel11からの書き方、csrfトークンの無効化
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        //認証を入れる
        Admin::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // ダミー画像作成の流れ
        // ストレージを作成
        Storage::fake('public');
        // ダミー画像を生成
        $picture = UploadedFile::fake()->image('image.png', 200, 200)->size(3072);
        // ファイル名を生成し
        $filename = $picture->hashName();
        // public/pictureフォルダに保存する
        Storage::disk('public')->putFileAs('picture', $picture, $filename);
        // ダミー画像のファイルパスを作成
        $this->dummyPicturePath = 'picture/' . $filename;

        // 更新画面に遷移するためのダミーデータを作成
        // テストケース15のデータ兼テストケース17で画面遷移するためのデータ
        Cast::factory()->create(['name' => '田中太郎', 'gender' => 1, 'birthday' => "1987-06-11", 'is_publish' => 0, 'occupation_id' => 1, 'picture_path' => $this->dummyPicturePath]);
        // テストケース16のデータ
        Cast::factory()->create(['name' => '西野涼子', 'gender' => 2, 'birthday' => "1987-06-11", 'is_publish' => 0, 'occupation_id' => 1, 'picture_path' => '']);
    }

    #[DataProvider('UpdateDataProvider')]
    public function test_old_image_update(array $queryParams, string $updateName, array $assertDatabaseHas = [])
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // 管理者を追加
        $admin = Admin::where('name', 'admin')->first();

        // 引数$updateNameから、データを取得する
        $cast = Cast::where('name', $updateName)->first();
        $oldPath = $cast->picture_path;
        // コントローラを直接呼ばず、Laravelのpatchで送信する。patchの場合は第二引数をqueryParamsにする
        $response = $this->actingAs($admin, 'admin')
            // htmlコードも一緒に出力され、エラーメッセージがわかるオプション
            ->followingRedirects()
            ->patch("admin/casts/{$cast->id}", $queryParams);

        // 通信ができたかどうか
        $response->assertStatus(200);
        // 最後に登録されたデータを引っ張ってくる(nameのカラムが、引数のnameと同じものを探している書き方)
        $cast = Cast::where('name', $queryParams['name'])->latest()->first();
        // 登録した写真パスを取得
        $picture_path = $cast->picture_path;
        // 期待結果の写真パスに、取得した写真パスを格納
        $assertDatabaseHas['picture_path'] = $picture_path;
        // DBの内容が期待結果と一致しているかを検証
        $this->assertDatabaseHas('casts', $assertDatabaseHas);
        // 画像が正しくアップロードされているか
        Storage::disk('public')->assertExists($cast->picture_path);
        // 画像が正しく削除されているか
        Storage::disk('public')->assertMissing($oldPath);
    }

    // データプロバイダのアノテーション()が必要。　しゃーぷ[DataProvider('関数名')]と書き、インポートもする。
    // 引数は、データ型を特定するため、array型と定義
    // 旧画像ありの更新関数
    #[DataProvider('UpdateNoPictureDataProvider')]
    public function test_image_update(array $queryParams, string $updateName, array $assertDatabaseHas = [])
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // 管理者を追加
        $admin = Admin::where('name', 'admin')->first();

        // 引数$updateNameから、データを取得する
        $cast = Cast::where('name', $updateName)->first();
        // コントローラを直接呼ばず、Laravelのpatchで送信する。patchの場合は第二引数をqueryParamsにする
        $response = $this->actingAs($admin, 'admin')
            // htmlコードも一緒に出力され、エラーメッセージがわかるオプション
            ->followingRedirects()
            ->patch("admin/casts/{$cast->id}", $queryParams);

        // 通信ができたかどうか
        $response->assertStatus(200);
        // 最後に登録されたデータを引っ張ってくる(nameのカラムが、引数のnameと同じものを探している書き方)
        $cast = Cast::where('name', $queryParams['name'])->latest()->first();
        // 登録した写真パスを取得
        $picture_path = $cast->picture_path;
        // 期待結果の写真パスに、取得した写真パスを格納
        $assertDatabaseHas['picture_path'] = $picture_path;
        // DBの内容が期待結果と一致しているかを検証
        $this->assertDatabaseHas('casts', $assertDatabaseHas);
        // 画像が正しくアップロードされているか
        Storage::disk('public')->assertExists($cast->picture_path);
    }

    // 登録に失敗する場合
    #[DataProvider('DontUpdateDataProvider')]
    public function test_dont_update(array $queryParams, string $updateName, array $assertDatabaseHas)
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // 管理者を取得
        $admin = Admin::where('name', 'admin')->first();
        // nameがuniqueのため、更新失敗を引き起こすためのデータを作成(テストケース17専用)
        Cast::factory()->create(['name' => '田中涼子', 'gender' => 2, 'birthday' => "1987-06-11", 'is_publish' => 0, 'occupation_id' => 1, 'picture_path' => $this->dummyPicturePath]);
        // 一致確認のため、ダミー画像のファイルパスを、引数のファイルパスに保存
        $assertDatabaseHas['picture_path'] = $this->dummyPicturePath;
        // 画面遷移のため、引数$updateNameから、データを取得する
        $cast = Cast::where('name', $updateName)->first();
        // 登録に失敗し、ERR-CAST-007のエラーメッセージを出力することを確認する
        $this->expectExceptionMessage('入力された名前は登録済みです[ERR-CAST-007]');
        // コントローラを直接呼ばず、Laravelのpatchで送信する。patchの場合は第二引数をqueryParamsにする
        $this->actingAs($admin, 'admin')
            // htmlコードも一緒に出力され、エラーメッセージがわかるオプション
            ->followingRedirects()
            ->patch("admin/casts/{$cast->id}", $queryParams);
    }

    // データプロバイダーはstaticとarrayが必須
    public static function UpdateDataProvider(): array
    {
        return [
            // テストケース15に相当 if文true、DB登録可能
            'allTrue' => [
                'queryParams'    => [
                    'name' => '東田藤子',
                    'gender' => 2,
                    'birthday' => "1987-12-12",
                    'is_publish' => true,
                    'occupation_id' => 3,
                    // ファイル名.png ,幅,高さでダミー画像を生成する
                    'picture' => UploadedFile::fake()->image('image.png', 200, 200),
                ],
                'updateName' => '田中太郎',
                'assertDatabaseHas' => [
                    'name' => '東田藤子',
                    'gender' => 2,
                    'birthday' => "1987-12-12",
                    'is_publish' => true,
                    'occupation_id' => 3,
                    'picture_path' => ''
                ],

            ],
        ];
    }

    // データプロバイダーはstaticとarrayが必須
    public static function UpdateNoPictureDataProvider(): array
    {
        return [
            // テストケース16に相当 旧写真なし、DB登録可能
            'oldImageNo' => [
                'queryParams'    => [
                    'name' => '田中涼子',
                    'gender' => 2,
                    'birthday' => "1987-06-11",
                    'is_publish' => true,
                    'occupation_id' => 1,
                    // ファイル名.png ,幅,高さでダミー画像を生成する
                    'picture' => '',
                ],
                'updateName' => '西野涼子',
                'assertDatabaseHas' => [
                    'name' => '田中涼子',
                    'gender' => 2,
                    'birthday' => "1987-06-11",
                    'is_publish' => true,
                    'occupation_id' => 1,
                    'picture_path' => ''
                ],

            ],
        ];
    }

    // データプロバイダーはstaticとarrayが必須
    public static function DontUpdateDataProvider(): array
    {
        return [
            // テストケース17に相当 データ登録失敗のテスト
            'updateFalse' => [
                'queryParams'    => [
                    'name' => '田中涼子',
                    'gender' => 2,
                    'birthday' => "1989-08-09",
                    'is_publish' => true,
                    'occupation_id' => 2,
                    'picture_path' => ''
                ],
                'updateName' => '田中太郎',
                'assertDatabaseHas' => [
                    'name' => '田中涼子',
                    'gender' => 2,
                    'birthday' => "1987-06-11",
                    'is_publish' => false,
                    'occupation_id' => 1,
                    'picture_path' => '',
                ],
            ],
        ];
    }
}
