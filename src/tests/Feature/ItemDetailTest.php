<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Comment;
use App\Models\User;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 必要な情報が商品詳細ページに表示される()
    {
        $user = User::factory()->create(['name' => 'コメントユーザー']);
        $condition = Condition::create(['name' => '良好']);
        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 2000,
            'description' => 'これは詳細な説明文です',
            'condition' => $condition->id,
        ]);

        Comment::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'comment' => '素敵な商品ですね',
        ]);

        $response = $this->get(route('items.show', ['id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('2,000');
        $response->assertSee('これは詳細な説明文です');
        $response->assertSee('良好');
        $response->assertSee('コメントユーザー');
        $response->assertSee('素敵な商品ですね');
    }

    /** @test */
    public function 複数選択されたカテゴリが表示されているか()
    {
        $condition = Condition::create(['name' => '良好']);
        $item = Item::factory()->create(['condition' => $condition->id]);
        
        $cat1 = Category::create(['name' => 'ファッション']);
        $cat2 = Category::create(['name' => 'レディース']);

        $item->categories()->attach([$cat1->id, $cat2->id]);

        $response = $this->get(route('items.show', ['id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('ファッション');
        $response->assertSee('レディース');
    }
}