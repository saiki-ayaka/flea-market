<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;

class MyListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねした商品だけが表示される()
    {
        $user = User::factory()->create();

        $itemLiked = Item::factory()->create(['name' => 'お気に入り商品']);
        $itemNotLiked = Item::factory()->create(['name' => '普通の商品']);

        $user->favoriteItems()->attach($itemLiked->id);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('お気に入り商品');
        $response->assertDontSee('普通の商品');
    }

    /** @test */
    public function 購入済み商品はSoldと表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => '売れた商品']);

        $user->favoriteItems()->attach($item->id);
        Order::factory()->create(['item_id' => $item->id]);

        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('売れた商品');
        $response->assertSee('Sold');
    }

    /** @test */
    public function 未認証の場合は何も表示されない()
    {
        $response = $this->get('/?tab=mylist');

        $response->assertDontSee('お気に入り商品');
    }
}
