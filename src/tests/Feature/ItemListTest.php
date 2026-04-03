<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品を取得できる()
    {
        $others = User::factory()->create();
        Item::factory()->create(['user_id' => $others->id, 'name' => '商品A']);

        $this->get('/')->assertStatus(200)->assertSee('商品A');
    }

    /** @test */
    public function 購入済み商品はSoldと表示される()
    {
        $item = Item::factory()->create(['name' => '売却済み商品']);
        Order::factory()->create(['item_id' => $item->id]);

        $this->get('/')->assertSee('Sold');
    }

    /** @test */
    public function 自分が出品した商品は表示されない()
    {
        $me = User::factory()->create();
        $myItem = Item::factory()->create(['user_id' => $me->id, 'name' => '私の商品']);

        $otherUser = User::factory()->create();
        $otherItem = Item::factory()->create(['user_id' => $otherUser->id, 'name' => '他人の商品']);

        $this->actingAs($me)->get('/')
             ->assertDontSee('私の商品')
             ->assertSee('他人の商品');
    }
}