<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Order;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 追加：ログインしてなければログイン画面へあとで消す。
        if (!$user) {
        return redirect()->route('login');
        }

        // 自分が「出品」した商品を取得
        $sellItems = Item::where('user_id', $user->id)->get();

        // 2. 自分が「購入」した商品を取得（ここを修正！）
        // ordersテーブルに自分のIDがある「商品（Item）」だけを直接取得します
        $buyItems = Item::whereHas('orders', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();

        // 3. 表示するビューの名前を確認してください
        // Bladeが profile/index.blade.php なら 'profile.index'
        return view('mypage.index', compact('user', 'sellItems', 'buyItems'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('mypage.edit', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // 1. 画像の保存処理
        if ($request->hasFile('image')) {
        // 古い画像があれば削除（初期状態のダミー画像などは消さないよう注意）
            if ($user->profile_image && $user->profile_image !== 'default.png') {
                Storage::disk('public')->delete($user->profile_image);
            }

            // 新しい画像を storage/app/public/profiles に保存し、パスを取得
            $path = $request->file('image')->store('profiles', 'public');
            $user->profile_image = $path;
        }

        // 2. ユーザー情報の更新（テスト仕様書に基づいた項目）
        $user->name = $request->name;
        $user->postcode = $request->postcode;
        $user->address = $request->address;
        $user->building = $request->building;

        $user->save();
        // ここで住所情報の保存ロジックを書きます
        return redirect()->route('item.index')->with('message', 'プロフィールを更新しました！');
    }
}
