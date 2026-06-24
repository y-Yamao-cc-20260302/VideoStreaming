<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Models\Cast;
use App\Models\Admin;
use App\Models\Occupation;
use Hash;
use Storage;
use Illuminate\Http\UploadedFile;

class UpdateCastTest extends TestCase
{
    // テスト実行するたびにデータベースをロールバックまでしてくれる機能
    use RefreshDatabase;

    protected function setUp(): void
    {
        //データベース使用のため必須
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
    }

    // データプロバイダのアノテーション()が必要。　しゃーぷ[DataProvider('関数名')]と書き、インポートもする。
    // 引数は、データ型を特定するため、array型と定義

    // 旧画像ありの更新関数
    #[DataProvider('UpdateDataProvider')]
    public function test_image_update(array $queryParams, string $updateName, array $assertDatabaseHas = [])
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // Laravel11からの書き方、csrfトークンの無効化
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        Storage::fake('public');
        //認証を入れる
        $admin = Admin::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // ダミー画像を生成
        $picture = UploadedFile::fake()->image('image.png', 200, 200)->size(3072);
        // ファイル名を生成し、public/pictureフォルダに保存する
        $filename = $picture->hashName();
        Storage::disk('public')->putFileAs('picture', $picture, $filename);

        // ダミー画像のファイルパスを作成
        $picturePath = 'picture/' . $filename;
        // 更新画面に遷移するためのダミーデータを作成
        // テストケース15のデータ
        Cast::factory()->create(['name' => '田中太郎', 'gender' => 1, 'birthday' => "1987-06-11", 'is_publish' => 0, 'occupation_id' => 1, 'picture_path' => $picturePath]);
        // テストケース16のデータ
        Cast::factory()->create(['name' => '西野涼子', 'gender' => 2, 'birthday' => "1987-06-11", 'is_publish' => 0, 'occupation_id' => 1, 'picture_path' => $picturePath]);

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
    #[DataProvider('DontUpdateDataProvider')]
    public function test_dont_update(array $queryParams, array $assertDatabaseHas)
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // Laravel11からの書き方、csrfトークンの無効化
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        Storage::fake('public');
        //認証を入れる
        $admin = Admin::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        // ダミー画像を生成
        $picture = UploadedFile::fake()->image('image.png', 200, 200)->size(3072);
        // ファイル名を生成し、public/pictureフォルダに保存する
        $filename = $picture->hashName();
        Storage::disk('public')->putFileAs('picture', $picture, $filename);
        // ダミー画像のファイルパスを作成
        $picturePath = 'picture/' . $filename;

        // 更新画面に遷移するためのダミーデータを作成
        $cast = Cast::factory()->create(['name' => '田中太郎', 'gender' => 1, 'birthday' => "1987-06-11", 'is_publish' => 0, 'occupation_id' => 1, 'picture_path' => $picturePath]);
        // nameがuniqueのため、更新失敗を引き起こすデータを作成
        Cast::factory()->create(['name' => '田中涼子', 'gender' => 2, 'birthday' => "1987-06-11", 'is_publish' => 0, 'occupation_id' => 1, 'picture_path' => $picturePath]);

        // コントローラを直接呼ばず、Laravelのpatchで送信する。patchの場合は第二引数をqueryParamsにする
        $response = $this->actingAs($admin, 'admin')
            // htmlコードも一緒に出力され、エラーメッセージがわかるオプション
            ->followingRedirects()
            ->patch("admin/casts/{$cast->id}", $queryParams);

        // 通信ができたかどうか(登録に失敗し、管理画面に戻るのでステータスは200)
        $response->assertStatus(200);
        // データベースの件数を確認する。今回は登録失敗して、1件のままならtrueという意味
        // DBの内容が期待結果と一致しているかを検証(更新前の値を引数にとり、更新されていないことを確認)
        // ダミー画像のファイルパスを、引数のファイルパスに保存
        $assertDatabaseHas['picture_path'] = $picturePath;
        $this->assertDatabaseHas('casts', $assertDatabaseHas);
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
