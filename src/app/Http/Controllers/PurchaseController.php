<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    // 購入確認画面の表示 (FN021)
public function show($id)
{
    $item = Item::findOrFail($id);
    $user = Auth::user();

    if ($item->is_sold) {
        return redirect()->route('item.index');
    }

    // セッションを取得（なければ空の配列を返す）
    $sessionAddress = session("new_address_{$id}", []);

    // 【重要】セッションの中に 'postcode' というキーが「実際に存在するか」までチェックする
    if (!empty($sessionAddress) && isset($sessionAddress['postcode'])) {
        $address = $sessionAddress;
    } else {
        // セッションが空、もしくは不完全なデータなら、ユーザーの基本情報を使う
        $address = [
            'postcode' => $user->postcode,
            'address'  => $user->address,
            'building' => $user->building,
        ];
        
        // 次回エラーにならないよう、正しい形式でセッションを上書き保存しておく
        session(["new_address_{$id}" => $address]);
    }

    return view('purchase.index', compact('item', 'user', 'address'));
}

    // 購入実行処理 (FN022)
    public function store(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        $user = Auth::user();
    
        // 支払い方法のバリデーション
        $request->validate([
            'payment_method' => 'required',
        ], [
            'payment_method.required' => '支払い方法を選択してください',
        ]);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        // 3. 支払い方法をStripe用に変換 ('card' または 'konbini')
        $payment_types = ($request->payment_method === 'konbini') ? ['konbini'] : ['card'];

        // 4. Stripe Checkoutセッションを作成
        // ここでStripeのサーバーに「これからこの商品を売ります」と伝えます
        $checkout_session = Session::create([
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
            // 決済成功時の戻り先（ここでDB保存処理を行うように後で調整します）
            'success_url' => route('purchase.success', ['id' => $item->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('purchase.show', ['id' => $item->id]),
        ]);

        // 5. Stripeの決済画面へリダイレクト！
        return redirect($checkout_session->url, 303);
    }

    // 購入成功時の処理
public function success(Request $request, $id)
{
    $item = Item::findOrFail($id);
    $user = Auth::user();

    // 1. Stripeから決済セッション情報を取得して、支払い方法を確認する
    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    $checkout_session = \Stripe\Checkout\Session::retrieve($request->get('session_id'));
    
    // Stripeから返ってきた支払い方法（card か konbini）を取得
    $payment_method = $checkout_session->payment_method_types[0];

    // 2. セッションから住所を取得
    $address = session("new_address_{$id}", [
        'postcode' => $user->postcode,
        'address'     => $user->address,
        'building'    => $user->building,
    ]);

    // 3. Orderテーブルに記録（payment_method を追加！）
    \App\Models\Order::create([
        'user_id'        => $user->id,
        'item_id'        => $item->id,
        'payment_method' => $payment_method, // これでエラーが消えます
        'postcode'       => $address['postcode'],
        'address'        => $address['address'],
        'building'       => $address['building'],
    ]);

    // 2. 商品を「売却済み」にする
    $item->update(['is_sold' => true]);

    // 3. セッションをクリアして一覧へ
    session()->forget("new_address_{$id}");
    
    return redirect()->route('item.index')->with('message', 'ご購入ありがとうございました！');
}
}
