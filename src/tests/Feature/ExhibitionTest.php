<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品出品画面にて必要な情報が保存できる()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'ファッション']);
        $image = \Illuminate\Http\UploadedFile::fake()->create('item.jpg', 100);

        $exhibitionData = [
            'name' => 'テスト商品',
            'price' => 5000,
            'description' => '商品の説明文です',
            'condition_id' => 1,
            'brand_name' => 'テストブランド',
            'category_ids' => [$category->id],
            'image' => $image,
        ];

        $response = $this->actingAs($user)
            ->post(route('items.store'), $exhibitionData);

        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'price' => 5000,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function 会員登録後_認証メールが送信される()
    {
        Notification::fake();

        $registrationData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->post('/register', $registrationData);

        $user = \App\Models\User::where('email', 'test@example.com')->first();
            Notification::assertSentTo($user, VerifyEmail::class);
    }
}
