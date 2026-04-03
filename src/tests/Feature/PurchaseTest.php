<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 「購入する」ボタンを押下すると購入が完了する()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $purchaseData = [
            'item_id' => $item->id,
            'payment_method' => 'card',
            'postcode' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => 'テストダヨビル',
        ];

        $response = $this->actingAs($user)
                         ->post(route('purchase.store', ['id' => $item->id]), $purchaseData);

        $response->assertStatus(303);
        $this->assertStringContainsString('checkout.stripe.com', $response->headers->get('Location'));
    }

    /** @test */
    public function 購入した商品は一覧画面でsoldと表示される()
    {
        $item = Item::factory()->create(['name' => '売却品']);
        Order::factory()->create([
            'item_id' => $item->id,
            'payment_method' => 'card',
            'postcode' => '123-4567',
            'address' => '東京都',
            'building' => 'ビル',
        ]);

        $this->get('/')->assertSee('sold');
    }

    /** @test */
    public function 購入した商品はプロフィール画面の購入一覧に追加される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['name' => '私の購入品']);

        Order::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'payment_method' => 'card',
            'postcode' => '123-4567',
            'address' => '東京都',
            'building' => 'ビル',
        ]);

        $response = $this->actingAs($user)->get('/mypage?tab=buy');
        $response->assertSee('私の購入品');
    }

    /** @test */
    public function 支払い方法の変更が小計画面に反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
                         ->withSession(['payment_method' => 'コンビニ払い'])
                         ->get(route('purchase.show', ['id' => $item->id]));

        $response->assertSee('コンビニ払い');
    }
}