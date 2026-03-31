<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',
        'postcode',
        'address',
        'building',
    ];

    public function item()
    {
        // 1つの注文は1つの商品に属する
        return $this->belongsTo(Item::class);
    }
}
