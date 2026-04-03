<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねアイコンを押下することによって、いいねした商品として登録することができる()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), 
        ]);
        
        $item = Item::factory()->create();

        $response = $this->actingAs($user)
                         ->post(route('favorite.toggle', ['id' => $item->id]));

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function 追加済みのアイコンは色が変化する()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        $user->favoriteItems()->attach($item->id);

        $response = $this->actingAs($user)->get(route('items.show', ['id' => $item->id]));

        $response->assertSee('heart-icon--active'); 
    }

    /** @test */
    public function 再度いいねアイコンを押下することによっていいねを解除することができる()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        $user->favoriteItems()->attach($item->id);

        $response = $this->actingAs($user)
                         ->post(route('favorite.toggle', ['id' => $item->id]));

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}