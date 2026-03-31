<?php

namespace App\Models;

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

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id')->withTimestamps();
    }

    public function isFavoritedBy($user): bool
    {
        if (!$user) return false;

        return $this->favoritedByUsers()
                    ->where('user_id', $user->id)
                    ->exists();
    }

    public function conditionModel()
    {
    return $this->belongsTo(Condition::class, 'condition');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function orders()
    {
        return $this->hasOne(Order::class);
    }

    public function getIsSoldAttribute()
    {
        return $this->orders()->exists();
    }
}
