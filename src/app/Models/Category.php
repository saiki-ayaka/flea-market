<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function items()
    {
        // カテゴリはたくさんの商品（Item）に属する
        return $this->belongsToMany(Item::class);
    }
}
