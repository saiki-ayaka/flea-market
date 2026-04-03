<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->word,
            'price' => 1000,
            'brand' => 'テストブランド',
            'description' => 'テスト説明文',
            'image_url' => 'test.jpg',
            'condition' => '良好',
        ];
    }
}
