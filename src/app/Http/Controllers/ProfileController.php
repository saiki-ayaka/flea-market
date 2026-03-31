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

        if (!$user) {
            return redirect()->route('login');
        }

        $sellItems = Item::where('user_id', $user->id)->get();

        $buyItems = Item::whereHas('orders', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->get();

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

        if ($request->hasFile('image')) {
            if ($user->profile_image && $user->profile_image !== 'default.png') {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('image')->store('profiles', 'public');
            $user->profile_image = $path;
        }

        $user->name = $request->name;
        $user->postcode = $request->postcode;
        $user->address = $request->address;
        $user->building = $request->building;

        $user->save();

        return redirect()->route('item.index')->with('message', 'プロフィールを更新しました！');
    }
}
