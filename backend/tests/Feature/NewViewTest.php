<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Models\Admin;
use Hash;

// テストケース5に相当
class NewViewTest extends TestCase
{
    // テスト実行するたびにデータベースをロールバックまでしてくれる機能(管理者ユーザー作成に必要)
    use RefreshDatabase;

    public function test_new_view()
    {
        // エラー出力
        $this->withoutExceptionHandling();
        //認証を入れる
        $admin = Admin::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        // 変数urlを作成
        $url = route('admin.casts.new');
        // 管理者権限を付与してgetアクションする
        $response = $this->actingAs($admin, 'admin')->get($url);
        // 通信が成功したか確認
        $response->assertStatus(200);
        // ルートがnew.viewを開いているかを確認
        $response->assertViewIs('admin.casts.new');
    }

    // テストケース6に相当
    public function test_dont_new_view()
    {
        // エラー出力
        $this->withoutExceptionHandling();
        // getを入れる
        $response = $this->get(route('admin.casts.new'));
        // 管理者ログインがないため302エラーになることを確認
        $response->assertStatus(302);
    }
}
