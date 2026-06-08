<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;

class ErrorHelper
{
    public function inputLog(Exception $e)
    {
        Log::error('エラーが発生しました。', [
            'message' => $e->getMessage(), // エラーメッセージ
            'file'    => $e->getFile(),    // エラーが起きたファイル名
            'line'    => $e->getLine(),    // エラーが起きた行番号
        ]);
    }
}
