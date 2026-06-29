<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    public function uplodePicture(object $picture)
    {
        $filename = '';
        try {
            if (str_starts_with($picture->getMimeType(), 'image/')) {
                // 引数の画像を保存
                $picture->store('picture', 'public');
                // 画像のファイルネームを取得
                $filename = $picture->hashName();
            }
            // ファイルネームを返却
        } catch (Exception $e) {
            Error_Log($e);
        }
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
            Error_Log($e);
        }
    }
}
