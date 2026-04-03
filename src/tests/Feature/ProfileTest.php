<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 送付先住所変更画面にて登録した住所が商品購入画面に反映されている()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $newAddress = [
            'postcode' => '000-0000',
            'address' => '変更後の住所',
            'building' => '変更後のビル名',
        ];

        $this->actingAs($user)->post(route('address.update', ['item_id' => $item->id]), $newAddress);

        $response = $this->actingAs($user)->get(route('purchase.show', ['id' => $item->id]));

        $response->assertSee('000-0000');
        $response->assertSee('変更後の住所');
        $response->assertSee('変更後のビル名');
    }

    /** @test */
    public function 購入した商品に送付先住所が紐づいて登録される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $orderData = [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment_method' => 'card',
            'postcode' => '999-9999',
            'address' => '紐付け確認住所',
            'building' => '紐付けビル',
        ];

        Order::create($orderData);

        $this->assertDatabaseHas('orders', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'postcode' => '999-9999',
            'address' => '紐付け確認住所',
        ]);
    }

    /** @test */
    public function 必要な情報が取得できる()
    {
        $user = User::factory()->create(['name' => 'テストユーザー']);

        Item::factory()->create(['user_id' => $user->id, 'name' => '出品アイテム']);

        $boughtItem = Item::factory()->create(['name' => '購入アイテム']);
        Order::create([
            'user_id' => $user->id,
            'item_id' => $boughtItem->id,
            'payment_method' => 'card',
            'postcode' => '123-4567',
            'address' => '東京都',
        ]);

        $response = $this->actingAs($user)->get('/mypage?tab=sell');
        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('出品アイテム');

        $response = $this->actingAs($user)->get('/mypage?tab=buy');
        $response->assertSee('購入アイテム');
    }
    /** @test */
    public function 変更項目が初期値として過去設定されていること()
    {
        $user = User::factory()->create([
            'name' => '設定済みの名前',
            'postcode' => '111-2222',
            'address' => '設定済みの住所',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('設定済みの名前');
        $response->assertSee('111-2222');
        $response->assertSee('設定済みの住所');
    }
}