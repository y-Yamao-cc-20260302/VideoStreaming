<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Storage;
use Illuminate\Http\UploadedFile;
use App\Helpers\ImageHelper;

class DeletePictureTest extends TestCase
{
    // テスト実行するたびにデータベースをロールバックまでしてくれる機能
    use RefreshDatabase;

    private string $dummyPicturePash = '';

    protected function setUp(): void
    {
        //データベース使用のため必須
        parent::setUp();
        // 画像アップロード用のストレージを作成
        Storage::fake('public');

        // ダミー画像を作成
        $picture = UploadedFile::fake()->image('image.png', 200, 200)->size(3072);
        $filename = $picture->hashName();
        Storage::disk('public')->putFileAs('picture', $picture, $filename);
        $this->dummyPicturePash = 'picture/' . $filename;
    }


    // テストケース27に相当、写真の削除ができる
    public function test_delete_picture()
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // ダミー画像のファイルパスを作成
        $picturePath = $this->dummyPicturePash;
        // データがフォルダに保存されているかを確認 
        // ※ただ初めから保存されていなくてassertMissingがtrueにならないように確認
        Storage::disk('public')->assertExists($picturePath);
        // オブジェクトとして生成
        $imageHelper = new ImageHelper();
        // uploadePictureを呼び出してファイルパスを取得
        $imageHelper->deletePicture($picturePath);
        // データがフォルダから消えているかを確認する
        Storage::disk('public')->assertMissing($picturePath);
    }

    // データプロバイダのアノテーション()が必要。　しゃーぷ[DataProvider('関数名')]と書き、インポートもする。
    #[DataProvider('ProvideDateProvider')]
    // 型宣言せずに動的に型を設定する(文字列でないものを渡すパターンもあるため)
    public function test_no_delete_picture($delete_img_path,)
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // オブジェクトとして生成
        $imageHelper = new ImageHelper();
        // 引数$delete_img_pathを引数として、削除を呼び出す
        $imageHelper->deletePicture($delete_img_path);
        // すでに保存済みのデータが消えていないことで、削除がされていないことを確認する
        Storage::disk('public')->assertExists($this->dummyPicturePash);
    }

    public static function ProvideDateProvider(): array
    {
        return [
            // テストケース28に相当　画像パスでないものを引数として呼び出す
            'emptyPath' => ['delete_img_path' => '',],
            // テストケース29に相当　画像パスでないものを引数として呼び出す
            'noString' => ['delete_img_path' => 1,],
            // テストケース30に相当　存在しない画像パスを引数として呼び出す
            'noexistpath' => ['delete_img_path' => 'picture/noimagenapass',],
        ];
    }
}
