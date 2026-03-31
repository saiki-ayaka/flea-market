<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    //いいねの登録・解除を切り替える
    public function toggle($id) // パラメータ名は route と合わせる
    {
        $user = Auth::user();
        
        // 商品が存在するか確認（存在しないIDが送られた場合の対策）
        $item = Item::findOrFail($id);

        // toggle メソッド1本で「登録/解除」を自動判別
        $user->favoriteItems()->toggle($id);

        return back();
    }
}
