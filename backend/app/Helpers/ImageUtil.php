<?php

namespace App\Helpers;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageUtil {
    public function uplodePicture(object $picture){
        try{
            if ($picture) {
                // 引数の画像を保存
                $picture->store('picture', 'public');
                // 画像のファイルネームを取得
                $filename = $picture->hashName();
            }
            // ファイルネームを返却
            return $filename;
        }catch(Exception $e){
            Log::error('エラーが発生しました。',[
                'message' => $e->getMessage(), // エラーメッセージ
                'file'    => $e->getFile(),    // エラーが起きたファイル名
                'line'    => $e->getLine(),    // エラーが起きた行番号
            ]);
        }
    }

    public function deletePicture(string $picture_path){
        try{
            if ($picture_path) {
                // ストレージのpublicフォルダ内で目当てのファイルの存在を確認する。その後削除する
                if (Storage::disk('public')->exists($picture_path)){
                    Storage::disk('public')->delete($picture_path);
                }
            }
        }catch(Exception $e){
            Log::error('エラーが発生しました。',[
                'message' => $e->getMessage(), // エラーメッセージ
                'file'    => $e->getFile(),    // エラーが起きたファイル名
                'line'    => $e->getLine(),    // エラーが起きた行番号
            ]);
        }
    }
}