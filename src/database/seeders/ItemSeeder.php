<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'テスト太郎',
                'password' => Hash::make('password'),
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'test2@example.com'],
            [
                'name' => 'テスト次郎',
                'password' => Hash::make('password')
            ]
        );

        $items = [
            [
                'name' => '腕時計',
                'price' => 15000,
                'user_id' => $user->id,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image_url' => 'images/watch.jpg',
                'condition' => 1,
                'category_ids' => [1, 5],
            ],

            [
                'name' => 'HDD',
                'price' => 5000,
                'user_id' => $user->id,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image_url' => 'images/hdd.jpg',
                'condition' => 2,
                'category_ids' => [2],
            ],

            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'user_id' => $user2->id,
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'image_url' => 'images/onion.jpg',
                'condition' => 3,
                'category_ids' => [10],
            ],

            [
                'name' => '革靴',
                'price' =>4000,
                'user_id' => $user2->id,
                'brand' => '',
                'description' => 'クラシックなデザインの革靴',
                'image_url' => 'images/shoes.jpg',
                'condition' => 4,
                'category_ids' => [1, 5],
            ],

            [
                'name' => 'ノートPC',
                'price' => 45000,
                'user_id' => $user->id,
                'brand' => '',
                'description' => '高性能なノートパソコン',
                'image_url' => 'images/pc.jpg',
                'condition' => 1,
                'category_ids' => [2],
            ],

            [
                'name' => 'マイク',
                'price' => 8000,
                'user_id' => $user->id,
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'image_url' => 'images/mic.jpg',
                'condition' => 2,
                'category_ids' => [2],
            ],

            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'user_id' => $user->id,
                'brand' => '',
                'description' => 'おしゃれなショルダーバッグ',
                'image_url' => 'images/bag.jpg',
                'condition' => 3,
                'category_ids' => [1, 4],
            ],

            [
                'name' => 'タンブラー',
                'price' => 500,
                'user_id' => $user2->id,
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'image_url' => 'images/tumbler.jpg',
                'condition' => 4,
                'category_ids' => [4, 5, 10],
            ],

            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'user_id' => $user2->id,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image_url' => 'images/grinder.jpg',
                'condition' => 1,
                'category_ids' => [10],
            ],

            [
                'name' => 'メイクセット',
                'price' => 2500,
                'user_id' => $user2->id,
                'brand' => '',
                'description' => '便利なメイクアップセット',
                'image_url' => 'images/makeup.jpg',
                'condition' => 2,
                'category_ids' => [4, 6],
            ],
        ];

        foreach ($items as $itemData) {
            //カテゴリーIDを一旦変数に移し、元のデータから消す
            $categoryIds = $itemData['category_ids'];
            unset($itemData['category_ids']);

            //商品を作成
            $item = Item::create($itemData);

            //中間テーブルにカテゴリーを紐付ける
            $item->categories()->attach($categoryIds);
        }
    }
}
