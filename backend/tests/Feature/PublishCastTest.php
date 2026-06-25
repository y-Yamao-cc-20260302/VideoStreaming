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
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PublishCastTest extends TestCase
{
    // テスト実行するたびにデータベースをロールバックまでしてくれる機能.
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

    // 公開→非公開
    #[DataProvider('PublishToFalse')]
    public function test_publish_to_false(array $queryParams)
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // Laravel11からの書き方、csrfトークンの無効化
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        // 画像アップロード用のストレージを作成
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
        // ファイルパスをクエリに入れる
        $queryParams['picture_path'] = $picturePath;
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
        //// 公開設定が　False　に更新されているかを確認する
        $this->assertFalse($cast->is_publish);
        // 画像が正しくアップロードされているか
        Storage::disk('public')->assertExists($cast->picture_path);
    }

    // 非公開→公開
    #[DataProvider('PublishToTrue')]
    public function test_publish_to_true(array $queryParams)
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // Laravel11からの書き方、csrfトークンの無効化
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        // 画像アップロード用のストレージを作成
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
        // ファイルパスをクエリに入れる
        $queryParams['picture_path'] = $picturePath;
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
        // 画像が正しくアップロードされているか
        Storage::disk('public')->assertExists($cast->picture_path);
    }

    // 公開設定の更新に失敗する場合
    #[DataProvider('DontPublishDataProvider')]
    public function test_dont_publish(array $queryParams,)
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // Laravel11からの書き方、csrfトークンの無効化
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        // DBからデータが見つからないエラーを期待する
        $this->expectException(ModelNotFoundException::class);

        //認証を入れる
        $admin = Admin::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // ダミーデータを作成する
        $cast = Cast::factory()->create($queryParams);
        $cast->delete();

        // コントローラを直接呼ばず、Laravelのpatchで送信する。patchの場合は第二引数をqueryParamsにする
        $response = $this->actingAs($admin, 'admin')
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
