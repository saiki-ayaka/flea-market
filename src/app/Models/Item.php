<?php

namespace App\Models;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'price',
        'brand',
        'description',
        'image_url',
        'condition',
    ];

    /**
     * 商品に紐づくカテゴリーを取得
     */
    public function categories()
    {
        // 多対多のリレーション（中間テーブル category_item を想定）
        return $this->belongsToMany(Category::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id')->withTimestamps();
        //この商品をいいねしたユーザーを取得
    }

    public function isFavoritedBy($user): bool
    {
        if (!$user) return false;

        return $this->favoritedByUsers()
                    ->where('user_id', $user->id)
                    ->exists();
        //ログイン中のユーザーがこの商品をすでにいいねしているかの判定
    }

    public function conditionModel()
{
    // Itemテーブルの 'condition' カラム（数字が入っている場所）を使って
    // Conditionモデルから名前を引っ張ってくる設定です
    return $this->belongsTo(Condition::class, 'condition');
}

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * ★ここを追加：商品と注文のリレーション
     * 商品が購入された（注文データがある）かを判定するために必要です
     */
    public function orders()
    {
        // 商品1つに対して、注文が1つ（1対1）と想定
        // 注文テーブル名が 'orders' なら hasOne で繋ぎます
        return $this->hasOne(Order::class); 
    }

    public function getIsSoldAttribute()
    {
        // 例：既に注文(Order)が存在すれば完売とみなす場合
        return $this->orders()->exists(); 
    }
}
