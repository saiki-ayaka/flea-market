<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン済みのユーザーはコメントを送信できる()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
                         ->post(route('comment.store', ['id' => $item->id]), [
                            'comment' => '素敵な商品ですね！'
                         ]);

        $this->assertDatabaseHas('comments', [
            'comment' => '素敵な商品ですね！',
            'item_id' => $item->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function ログイン前のユーザーはコメントを送信できない()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('comment.store', ['id' => $item->id]), [
            'comment' => '未ログインのコメント'
        ]);

        $response->assertRedirect('/login');
    }

    /** @test */
    public function コメントが入力されていない場合バリデーションメッセージが表示される()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
                         ->from(route('items.show', ['id' => $item->id]))
                         ->post(route('comment.store', ['id' => $item->id]), [
                             'comment' => ''
                         ]);

        $response->assertSessionHasErrors(['comment']);
    }

    /** @test */
    public function コメントが255字以上の場合バリデーションメッセージが表示される()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
                         ->post(route('comment.store', ['id' => $item->id]), [
                            'comment' => str_repeat('あ', 256)
                         ]);

        $response->assertSessionHasErrors(['comment']);
    }
}