@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
<main class="purchase-container">
    <div class="purchase-content">
        {{-- 左側：詳細入力 --}}
        <div class="purchase-main">
            {{-- 商品情報 --}}
            <div class="purchase-item">
                <div class="purchase-item__image">
                    <img src="{{ asset('storage/' . $item->image_url) }}" alt="{{ $item->name }}">
                </div>
                <div class="purchase-item__detail">
                    <h2>{{ $item->name }}</h2>
                    <p>¥{{ number_format($item->price) }}</p>
                </div>
            </div>

            {{-- 支払い方法 (FN023) --}}
            <div class="purchase-section">
                <div class="section-header--vertical">
                    <h3>支払い方法</h3>
                    <select name="payment_method" form="purchase-form" id="payment-select" class="payment-select">
                        <option value="" disabled selected>選択してください</option>
                        <option value="konbini">コンビニ払い</option>
                        <option value="card">クレジットカード払い</option>
                    </select>
                </div>
                @error('payment_method')
                    <p class="error-text">{{ $message }}</p>
                @enderror
            </div>

            {{-- 配送先 (FN024) --}}
            <div class="purchase-section">
                <div class="section-header">
                    <h3>配送先</h3>
                    <a href="{{ route('purchase.address', ['item_id' => $item->id]) }}" class="address-change-link">変更する</a>
                </div>
                <div class="address-display">
                    <p>〒 {{ $address['postcode'] }}</p>
                    <p>{{ $address['address'] }} {{ $address['building'] }}</p>
                </div>
            </div>
        </div>

        {{-- 右側：サイドバー（決済確認） --}}
        <div class="purchase-side">
            <div class="side-card">
                <table class="summary-table">
                    <tr class="row-item-price">
                        <th>商品代金</th>
                        <td>¥{{ number_format($item->price) }}</td>
                    </tr>
                    <tr class="row-payment-method">
                        <th>支払い方法</th>
                        <td id="payment-display">選択してください</td>
                    </tr>
                </table>
            </div>

            <div class="purchase-form-wrapper">
                <form action="{{ route('purchase.store', $item->id) }}" method="POST" id="purchase-form">
                    @csrf
                    <button type="submit" class="btn-purchase">購入する</button>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    // 支払い方法の選択を小計画面に反映させるJavaScript
    const select = document.getElementById('payment-select');
    const display = document.getElementById('payment-display');

    select.addEventListener('change', function() {
        // 選択されたテキスト（コンビニ払い等）を取得して右側に表示
        const selectedText = select.options[select.selectedIndex].text;
        display.textContent = selectedText;
    });
</script>
@endsection