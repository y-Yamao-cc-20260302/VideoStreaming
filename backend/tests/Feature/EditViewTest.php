<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Cast;
use App\Models\Admin;
use App\Models\Occupation;
use PHPUnit\Framework\Attributes\DataProvider;

use Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;

// 編集画面への遷移確認
class EditViewTest extends TestCase
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
    // テストケース11に該当
    #[DataProvider('EditDataProvider')]
    public function test_edit_view(array $assertSee)
    {
        // 画像アップロードを含め、ダミーデータを作成する
        // 仮ストレージ生成
        Storage::fake('public');
        // 仮写真を生成
        $picture = UploadedFile::fake()->image('image.png', 200, 200)->size(3072);
        // ファイル名を生成し、public/pictureフォルダに保存する
        $filename = $picture->hashName();
        Storage::disk('public')->putFileAs('picture', $picture, $filename);
        // ダミーデータのための画像パスを作成
        $picturePath = 'picture/' . $filename;
        // 編集画面に遷移するためのダミーデータを生成
        $cast = Cast::factory()->create(['name' => '北田太郎', 'gender' => 1, 'birthday' => "1987-06-11", 'occupation_id' => 1, 'is_publish' => 1, 'picture_path' => $picturePath]);
        // エラー出力用
        $this->withoutExceptionHandling();
        //認証を入れる
        $admin = Admin::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        // 変数urlを作成
        $url = "admin/casts/{$cast->id}/edit";
        // 管理者権限を付与してgetアクションする
        $response = $this->actingAs($admin, 'admin')->get($url);
        // 通信が成功したか確認
        $response->assertStatus(200);
        // 開いた編集ページに期待結果が含まれているか確認　今回はvalue=などにhtml要素に含まれているため、assertSeeで確認する
        $response->assertSee($assertSee);
    }

    // テストケース12に相当
    public function test_dont_edit_view()
    {
        // エラー出力用
        $this->withoutExceptionHandling();
        // データベースからデータを取得する際の、該当データがない場合のエラーを確認する
        $this->expectException(ModelNotFoundException::class);
        //認証を入れる
        $admin = Admin::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        // データを作成し、すぐに論理削除することで、該当データを取得できないようにしている
        $cast = Cast::factory()->create(['name' => '南野洋子', 'gender' => 2, 'birthday' => "1987-06-11", 'occupation_id' => 1, 'is_publish' => 0,]);
        $cast->delete();
        // アクセス確認
        $this->actingAs($admin, 'admin')->get(route('admin.casts.edit', $cast));
    }

    public static function EditDataProvider(): array
    {
        return [
            'getEdit' => [
                'assertSee' => [
                    '出演者編集',
                    '北田太郎',
                    '男性',
                    '1987-06-11',
                    '俳優',
                ],
            ],
        ];
    }
}
