<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function メールアドレスが入力されていない場合バリデーションメッセージが表示される()
    {
        $this->post('/login', ['email' => '', 'password' => 'password'])
             ->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /** @test */
    public function パスワードが入力されていない場合バリデーションメッセージが表示される()
    {
        $this->post('/login', ['email' => 'test@example.com', 'password' => ''])
             ->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /** @test */
    public function 入力情報が間違っている場合バリデーションメッセージが表示される()
    {
        $this->post('/login', [
            'email' => 'wrong@example.com', 
            'password' => 'wrong_password'
        ])
        ->assertSessionHasErrors(['login_error' => 'ログイン情報が登録されていません']);
    }

    /** @test */
    public function 正しい情報が入力された場合ログイン処理が実行される()
    {
        $password = 'correct_password';
        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email, 
            'password' => $password
        ]);
    
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/mypage/profile');
    }
}