<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    // どのユーザーが、どの商品に「いいね」したかを保存する
    protected $fillable = [
        'user_id',
        'item_id',
    ];
}
