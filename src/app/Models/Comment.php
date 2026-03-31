<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // FN020-1, 2: 保存を許可する項目を指定
    protected $fillable = ['user_id', 'item_id', 'comment'];

    public function item()
    {
        // Commentは1つのItemに属する
        return $this->belongsTo(Item::class);
    }

    /**
     * コメントをしたユーザーを取得
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
