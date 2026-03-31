<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function edit($item_id)
    {
        return view('purchase.address',['item_id' => $item_id]);
    }

    public function update(AddressRequest $request, $item_id)
    {
        session(["new_address_{$item_id}" => [
            'postcode' => $request->postcode,
            'address' => $request->address,
            'building' => $request->building,
        ]]);
        return redirect()->route('purchase.show', ['id' => $item_id]);
    }
}
