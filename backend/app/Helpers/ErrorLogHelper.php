<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log;

class ErrorLogHelper
{
    public function outputLog(Exception $e)
    {
        // メッセージと、エラーそのものをlaravel.logに出力する
        Log::error($e->getMessage(), ['exception' => $e]);
    }
}
