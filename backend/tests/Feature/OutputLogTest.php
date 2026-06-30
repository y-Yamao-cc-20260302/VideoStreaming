<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Helpers\ErrorHelper;
use Illuminate\Support\Facades\Log;
use Exception;

class OutputLogTest extends TestCase
{
    public function test_output_log()
    {
        // Log::spy()にてログ確認する
        Log::spy();
        // エラーメッセージを指定
        $message = 'testException';
        // Exceptionを作成（この行のファイル名と行番号が自動でセットされる）
        $testException = new Exception($message);

        // testExceptionからfileとlineの値を取得する
        $file = $testException->getFile();
        $line = $testException->getLine();

        // ErrorHelperを呼び出す
        $helper = new ErrorHelper();
        $helper->outputLog($testException);

        // spyで確認したい内容をshouldHaveReceivedで確認する
        Log::shouldHaveReceived('error')
            // 一度だけ呼び出されたことを確認する
            ->once()
            // 内容を確認、'エラーが発生しました。'に加え、outputLogと同じ内容が書き込まれているかを確認する
            ->with('エラーが発生しました。', [
                'message' => $message,
                'file'    => $file,
                'line'    => $line,
            ]);
    }
}
