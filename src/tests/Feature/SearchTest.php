<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品名で部分一致検索ができる()
    {
        Item::factory()->create(['name' => 'メンズ時計']);
        Item::factory()->create(['name' => 'レディースバッグ']);

        $response = $this->get('/?keyword=時計');

        $response->assertStatus(200);
        $response->assertSee('メンズ時計');
        $response->assertDontSee('レディースバッグ');
    }

    /** @test */
    public function 検索状態がマイリストでも保持されている()
    {
        $user = \App\Models\User::factory()->create();
        $item = Item::factory()->create(['name' => '検索対象の商品']);
        $user->favoriteItems()->attach($item->id);

        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=検索対象');

        $response->assertStatus(200);
        $response->assertSee('検索対象の商品');
    }
}