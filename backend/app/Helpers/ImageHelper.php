<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Storage;
use Log;

class ImageHelper
{
    public function uplodePicture(object $picture)
    {
        // 初期値が空文字のファイルネームを生成　引数が写真でないなら空文字を返す
        $filename = '';
        try {
            // $pictureがgetMineType(ファイル種別確認メソッド)を持っているか確認
            if (method_exists($picture, 'getMimeType')) {
                // ファイル種別が画像かどうかを確認
                // str_starts_withは第二引数の部分文字列で始まるかどうか確認する
                if (str_starts_with($picture->getMimeType(), 'image/')) {
                    // 引数の画像を保存
                    $picture->store('picture', 'public');
                    // 画像のファイルネームを取得
                    $filename = $picture->hashName();
                }
            }
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
        }
        // ファイルネームを返却
        return $filename;
    }

    public function deletePicture(string $picture_path)
    {
        try {
            if ($picture_path) {
                // ストレージのpublicフォルダ内で目当てのファイルの存在を確認する。その後削除する
                if (Storage::disk('public')->exists($picture_path)) {
                    Storage::disk('public')->delete($picture_path);
                }
            }
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
        }
    }
}
