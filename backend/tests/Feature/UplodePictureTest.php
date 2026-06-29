<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Storage;
use Illuminate\Http\UploadedFile;
use App\Helpers\ImageHelper;

class UplodePictureTest extends TestCase
{
    // テスト実行するたびにデータベースをロールバックまでしてくれる機能
    use RefreshDatabase;

    // テストケース24に相当　正常に画像登録ができる
    public function test_uplode_picture()
    {
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // 画像アップロード用のストレージを作成
        Storage::fake('public');
        // ダミー画像を作成
        $picture = UploadedFile::fake()->image('image.png', 200, 200)->size(3072);
        // オブジェクトとして生成
        $imageHelper = new ImageHelper();
        // uploadePictureを呼び出してファイルパスを取得
        $img_path = $imageHelper->uplodePicture($picture);
        // ファイルパスを参照できる形に修正
        $img_path = 'picture/' . $img_path;
        // データがフォルダに保存されているかを確認
        Storage::disk('public')->assertExists($img_path);
    }

    #[DataProvider('ProvideDateProvider')]
    public function test_dont_uplode_picture(object $img)
    {
        // 画像アップロード用のストレージを作成
        Storage::fake('public');
        // エラーを出力してくれる
        $this->withoutExceptionHandling();
        // 画像でないオブジェクトでは、Call to undefined methodのエラーになるため。
        $this->expectExceptionMessage('Call to undefined method stdClass::getMimeType()');
        // オブジェクトを作成
        $imagehelper = new ImageHelper();
        // 画像アップロード関数を呼び出す
        $imagehelper->uplodePicture($img);
        // Storage::disk('public')->assertExists($img_path);
    }

    public static function ProvideDateProvider(): array
    {
        return [
            // テストケース25に相当　画像ではないものを入力値とする
            'noImageObject' => ['img' => (object)['name' => 'a',]],
            // テストケース26に相当　空のオブジェクトを引数として呼び出す
            // が、空のオブジェクトで渡してもhelper側がfalseにならない。
            'emptyObject' => ['img' => (object)[],],
        ];
    }
}
