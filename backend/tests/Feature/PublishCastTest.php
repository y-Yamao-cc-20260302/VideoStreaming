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

class PublishCastTest extends TestCase
{
    // テスト実行するたびにデータベースをロールバックまでしてくれる機能.
    use RefreshDatabase;
    protected $seeder = OccupationSeeder::class;
    private string $dummyPicturePath = '';

    protected function setUp(): void
    {
        //データベース使用のため必須
        parent::setUp();
        //認証を入れる
        Admin::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // Laravel11からの書き方、csrfトークンの無効化
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        // 画像アップロード用のストレージを作成
        Storage::fake('public');
        // ダミー画像を生成
        $picture = UploadedFile::fake()->image('image.png', 200, 200)->size(3072);
        // ファイル名を生成し、public/pictureフォルダに保存する
        $filename = $picture->hashName();
        Storage::disk('public')->putFileAs('picture', $picture, $filename);

        // ダミー画像のファイルパスを作成
        $this->dummyPicturePath = 'picture/' . $filename;
    }

    // データプロバイダのアノテーション()が必要。　しゃーぷ[DataProvider('関数名')]と書き、インポートもする。
    // 引数は、データ型を特定するため、array型と定義

    // 公開→非公開
    #[DataProvider('PublishToFalse')]
    public function test_publish_to_false(array $queryParams)
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // ファイルパスをクエリに入れる
        $queryParams['picture_path'] = $this->dummyPicturePath;
        // ダミーデータを作成する
        $cast = Cast::factory()->create($queryParams);
        // 管理者を取得
        $admin = Admin::where('name', 'admin')->first();

        // コントローラを直接呼ばず、Laravelのpatchで送信する。patchの場合は第二引数をqueryParamsにする
        $response = $this->actingAs($admin, 'admin')
            // htmlコードも一緒に出力され、エラーメッセージがわかるオプション
            ->followingRedirects()
            ->patch("admin/casts/{$cast->id}/publish", $queryParams);

        // 通信ができたかどうか
        $response->assertStatus(200);
        // 書き換わった値を再取得する
        $cast = Cast::where('id', $cast->id)->first();
        //// 公開設定が　False　に更新されているかを確認する
        $this->assertFalse($cast->is_publish);
        // 画像が正しく保存されたままか
        Storage::disk('public')->assertExists($cast->picture_path);
    }

    // 非公開→公開
    #[DataProvider('PublishToTrue')]
    public function test_publish_to_true(array $queryParams)
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // ファイルパスをクエリに入れる
        $queryParams['picture_path'] = $this->dummyPicturePath;
        // 管理者を取得
        $admin = Admin::where('name', 'admin')->first();
        // ダミーデータを作成する
        $cast = Cast::factory()->create($queryParams);

        // コントローラを直接呼ばず、Laravelのpatchで送信する。patchの場合は第二引数をqueryParamsにする
        $response = $this->actingAs($admin, 'admin')
            // htmlコードも一緒に出力され、エラーメッセージがわかるオプション
            ->followingRedirects()
            ->patch("admin/casts/{$cast->id}/publish", $queryParams);

        // 通信ができたかどうか
        $response->assertStatus(200);
        // 書き換わった値を再取得する
        $cast = Cast::where('id', $cast->id)->first();
        // 公開設定が　True　に更新されているかを確認する
        $this->assertTrue($cast->is_publish);
        // 画像が正しく保存されたままか
        Storage::disk('public')->assertExists($cast->picture_path);
    }

    // 公開設定の更新に失敗する場合
    #[DataProvider('DontPublishDataProvider')]
    public function test_dont_publish(array $queryParams,)
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();

        // DBからデータが見つからないエラーを期待する
        $this->expectException(ModelNotFoundException::class);
        // 管理者を取得
        $admin = Admin::where('name', 'admin')->first();

        // ダミーデータを作成する
        $cast = Cast::factory()->create($queryParams);
        $cast->delete();

        // コントローラを直接呼ばず、Laravelのpatchで送信する。patchの場合は第二引数をqueryParamsにする
        $this->actingAs($admin, 'admin')
            // htmlコードも一緒に出力され、エラーメッセージがわかるオプション
            ->followingRedirects()
            ->patch("admin/casts/{$cast->id}/publish", $queryParams);
    }

    // データプロバイダーはstaticとarrayが必須
    // 登録に失敗する場合
    public static function PublishToFalse(): array
    {
        return [
            // テストケース21に相当 公開→非公開に変更
            'publishToFalse' => [
                'queryParams'    => [
                    'name' => '東田藤子',
                    'gender' => 2,
                    'birthday' => "1987-12-12",
                    'is_publish' => true,
                    'occupation_id' => 3,
                    'picture_path' => '',
                ],
            ],
        ];
    }

    public static function PublishToTrue(): array
    {
        return [
            // テストケース22に相当 非公開→公開に変更
            'publishToTrue' => [
                'queryParams'    => [
                    'name' => '東田藤子',
                    'gender' => 2,
                    'birthday' => "1987-12-12",
                    'is_publish' => false,
                    'occupation_id' => 3,
                    'picture_path' => '',
                ],
            ],
        ];
    }

    // データプロバイダーはstaticとarrayが必須
    public static function DontPublishDataProvider(): array
    {
        return [
            // テストケース23に相当 公開設定更新失敗のテスト
            'dontPublishDataProvider' => [
                'queryParams'    => [
                    'name' => '田中涼子',
                    'gender' => 2,
                    'birthday' => "1989-08-09",
                    'is_publish' => true,
                    'occupation_id' => 2,
                    'picture_path' => '',
                ],
            ],
        ];
    }
}
