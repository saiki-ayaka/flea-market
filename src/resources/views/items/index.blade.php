@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<main class="l-main">
    <div class="top-tabs">
        <div class="top-tabs__inner">
            {{-- おすすめリンク：現在のkeywordを引き継ぐ --}}
            <a href="{{ route('item.index', ['keyword' => request('keyword')]) }}" class="top-tab {{ request('tab') !== 'mylist' ? 'active' : '' }}">おすすめ
            </a>

            {{-- マイリストリンク：tab=mylist と 現在のkeyword をセットで送る --}}
            <a href="{{ route('item.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}" class="top-tab {{ request('tab') === 'mylist' ? 'active' : '' }}">マイリスト
            </a>
        </div>
    </div>

    <div class="product-grid">
        {{-- $items が空（いいねなし/未ログインのマイリスト）なら、何も表示されない --}}
        @forelse($items as $item)
            <a href="{{ route('items.show', ['id' => $item->id]) }}" class="product-card">
                <div class="product-card__image-wrapper">
                    {{-- 商品画像 --}}
                    <img src="{{ asset('storage/' . $item->image_url) }}" alt="{{ $item->name }}" class="product-card__image">

                    {{-- 購入済み（Sold）判定 --}}
                    {{-- $item->is_sold が true または $item->status が 'sold' などの条件に合わせて調整 --}}
                    @if($item->is_sold) 
                        <div class="sold-label">Sold</div>
                    @endif
                </div>
                {{-- 商品名 --}}
                <p class="product-card__name">{{ $item->name }}</p>
            </a>
        @empty
            {{-- ここを空欄にすることで、いいねがない場合は何も表示されません --}}
        @endforelse
    </div>
</main>
@endsection