<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function edit($item_id)
    {
        // $item_id を使って、もし特定の商品専用の住所があれば取得するロジックなど
        return view('purchase.address',['item_id' => $item_id]);
    }

    public function update(AddressRequest $request, $item_id)
    {
        // セッションに保存（キーに $item_id を入れることで商品ごとに管理可能にする）
        session(["new_address_{$item_id}" => [
            'postcode' => $request->postcode,
            'address'     => $request->address,
            'building'    => $request->building,
        ]]);
        // 更新後、元の商品の購入画面へ戻る（要件：購入画面に反映されている）
        return redirect()->route('purchase.show', ['id' => $item_id]);
    }
}
