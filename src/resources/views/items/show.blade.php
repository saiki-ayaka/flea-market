@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<main class="l-main detail-container">
    {{-- 左側：画像セクション --}}
    <div class="detail-image">
        <div class="detail-image__wrapper">
            <img src="{{ asset('storage/' . $item->image_url) }}" alt="{{ $item->name }}">
            @if($item->is_sold)
                <div class="sold-label">Sold</div>
            @endif
        </div>
    </div>

    {{-- 右側：情報セクション --}}
    <div class="detail-info">
        <h1 class="detail-info__title">{{ $item->name }}</h1>
        <p class="detail-info__brand">ブランド名</p>
        
        <p class="detail-info__price">
            <span class="price-amount">¥{{ number_format($item->price) }}</span>
            <span class="price-tax">(税込)</span>
        </p>

        {{-- いいね・コメントアイコン (FN017-5, 6, 9) --}}
        <div class="detail-actions">
            {{-- いいね --}}
            <div class="action-item">
                <form action="{{ route('favorite.toggle', ['id' => $item->id]) }}" method="POST" id="favorite-form">
                    @csrf
                    <button type="submit" class="action-btn">
                        @if($item->isFavoritedBy(Auth::user()))
                            <i class="fa-solid fa-heart heart-icon active"></i>
                        @else
                            <i class="fa-regular fa-heart heart-icon"></i>
                        @endif
                    </button>
                </form>
                <span class="action-count">{{ $item->favoritedByUsers()->count() }}</span>
            </div>

            {{-- コメント --}}
            <div class="action-item">
                <a href="#comment-section" class="action-icon-box">
                    <i class="fa-regular fa-comment-dots comment-icon"></i>
                </a>
                <span class="action-count">{{ $item->comments->count() }}</span>
            </div>
        </div>

        <a href="{{ route('purchase.show', ['id' => $item->id]) }}" class="btn-primary">購入手続きへ</a>

        <section class="detail-section">
            <h2 class="detail-section__title">商品説明</h2>
            <p class="detail-text">{{ $item->description }}</p>
        </section>

        <section class="detail-section">
            <h2 class="detail-section__title">商品の情報</h2>
            <div class="info-row">
                <span class="info-label">カテゴリー</span>
                <div class="info-tags">
                    @forelse($item->categories as $category)
                        <span class="tag">{{ $category->name }}</span>
                    @empty
                        <span class="tag">未設定</span>
                    @endforelse
                </div>
            </div>
            <div class="info-row">
                <span class="info-label">商品の状態</span>
                <span class="info-value">{{ $item->conditionModel->name}}</span>
            </div>
        </section>

        {{-- コメント一覧エリア (FN017-10, 11) --}}
        <section class="detail-section" id="comment-section">
            <h2 class="detail-section__title detail-section__title--gray">コメント({{ $item->comments->count() }})</h2>
            <div class="comment-list">
                @foreach($item->comments as $comment)
                    <div class="comment-item">
                        <div class="comment-user">
                            <div class="user-icon">
                                @if($comment->user->profile_image)
                                    <img src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="プロフィール画像">
                                @endif
                            </div>
                            <span class="user-name">{{ $comment->user->name }}</span>
                        </div>
                        <div class="comment-body">
                            {{ $comment->comment }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- コメント送信フォーム (FN020) --}}
        <section class="detail-section">
            <h2 class="detail-section__title">商品へのコメント</h2>
            <form action="{{ route('comment.store', ['id' => $item->id]) }}" method="POST" class="comment-form">
                @csrf
                <textarea name="comment" class="comment-textarea">{{ old('comment') }}</textarea>

                @error('comment')
                    <p class="error-text">{{ $message }}</p>
                @enderror

                @auth
                    <button type="submit" class="btn-primary btn-submit-comment">
                        コメントを送信する
                    </button>
                @else
                    {{-- 未認証：クリックするとログインへ飛ばすボタンを表示 --}}
                    <a href="{{ route('login') }}" class="btn-primary btn-submit-comment">
                        コメントを送信する
                    </a>
                @endauth
            </form>
        </section>
    </div>
</main>
@endsection