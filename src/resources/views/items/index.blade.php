@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<main class="l-main">
    <div class="top-tabs">
        <div class="top-tabs__inner">
            <a href="{{ route('item.index', ['keyword' => request('keyword')]) }}" class="top-tab {{ request('tab') !== 'mylist' ? 'active' : '' }}">おすすめ
            </a>

            <a href="{{ route('item.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}" class="top-tab {{ request('tab') === 'mylist' ? 'active' : '' }}">マイリスト
            </a>
        </div>
    </div>

    <div class="product-grid">
        @forelse($items as $item)
            <a href="{{ route('items.show', ['id' => $item->id]) }}" class="product-card">
                <div class="product-card__image-wrapper">
                    <img src="{{ asset('storage/' . $item->image_url) }}" alt="{{ $item->name }}" class="product-card__image">
                    @if($item->is_sold)
                        <div class="sold-label">Sold</div>
                    @endif
                </div>
                <p class="product-card__name">{{ $item->name }}</p>
            </a>
        @empty
        @endforelse
    </div>
</main>
@endsection