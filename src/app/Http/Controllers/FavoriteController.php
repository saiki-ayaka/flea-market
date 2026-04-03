<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle($id)
    {
        $user = Auth::user();
        $item = Item::findOrFail($id);
        $user->favoriteItems()->toggle($id);

        return back();
    }
}
