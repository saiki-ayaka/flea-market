<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function show($id)
    {
        $item = Item::findOrFail($id);
        $user = Auth::user();

        if ($item->is_sold) {
            return redirect()->route('item.index');
        }

        $sessionAddress = session("new_address_{$id}", []);

        if (!empty($sessionAddress) && isset($sessionAddress['postcode'])) {
            $address = $sessionAddress;
        } else {
            $address = [
                'postcode' => $user->postcode,
                'address'  => $user->address,
                'building' => $user->building,
            ];
            session(["new_address_{$id}" => $address]);
        }

        return view('purchase.index', compact('item', 'user', 'address'));
    }

    public function store(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $user = Auth::user();
    
        $request->validate([
            'payment_method' => 'required',
        ], [
            'payment_method.required' => '支払い方法を選択してください',
        ]);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $payment_types = ($request->payment_method === 'konbini') ? ['konbini'] : ['card'];

        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => $payment_types,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['id' => $item->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('purchase.show', ['id' => $item->id]),
        ]);

        return redirect($checkout_session->url, 303);
    }

    public function success(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $user = Auth::user();

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $checkout_session = \Stripe\Checkout\Session::retrieve($request->get('session_id'));
    
        $payment_method = $checkout_session->payment_method_types[0];

        $address = session("new_address_{$id}", [
            'postcode' => $user->postcode,
            'address' => $user->address,
            'building' => $user->building,
        ]);

        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => $payment_method,
            'postcode' => $address['postcode'],
            'address' => $address['address'],
            'building' => $address['building'],
        ]);

        $item->update(['is_sold' => true]);

        session()->forget("new_address_{$id}");
    
        return redirect()->route('item.index')->with('message', 'ご購入ありがとうございました！');
    }
}
