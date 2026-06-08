<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    public function uplodePicture(object $picture)
    {
        try {
            if ($picture) {
                // 引数の画像を保存
                $picture->store('picture', 'public');
                // 画像のファイルネームを取得
                $filename = $picture->hashName();
            }
            // ファイルネームを返却
            return $filename;
        } catch (Exception $e) {
            Error_Log($e);
        }
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
            Error_Log($e);
        }
    }
}
