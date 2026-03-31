<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // 一括割り当てを許可する項目（FN023, FN024の要件に基づく）
    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
        'postcode',
        'address',
        'building',
    ];

    /**
     * この注文に紐づく商品を取得
     */
    public function item()
    {
        // 1つの注文は1つの商品に属する
        return $this->belongsTo(Item::class);
    }
}
