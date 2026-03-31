@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<main class="profile">
    <div class="profile__container">
        <div class="profile__info">
            <div class="profile__image-wrapper">
                @if($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" class="profile__image">
                @else
                    <div class="default-circle"></div>
                @endif
            </div>
            <h2 class="profile__name">{{ Auth::user()->name }}</h2>
            <a href="{{ route('profile.edit') }}" class="profile__edit-btn">プロフィールを編集</a>
        </div>

        <div class="profile__tabs">
            <div class="profile__tabs-inner">
                <a href="{{ route('profile.index', ['tab' => 'sell']) }}" class="profile__tab {{ request('tab') !== 'buy' ? 'profile__tab--active' : '' }}">出品した商品</a>
                <a href="{{ route('profile.index', ['tab' => 'buy']) }}" class="profile__tab {{ request('tab') === 'buy' ? 'profile__tab--active' : '' }}">購入した商品</a>
            </div>
        </div>

        <div class="profile__item-grid">
            @if(request('tab') === 'buy')
                @foreach($buyItems as $item)
                    <div class="profile__item-card">
                        <div class="profile__item-image-wrapper">
                            <img src="{{ asset('storage/' . $item->image_url) }}" alt="{{ $item->name }}" class="profile__item-image">
                            <div class="sold-label">Sold</div>
                        </div>
                        <p class="profile__item-name">{{ $item->name }}</p>
                    </div>
                @endforeach
            @else
                @foreach($sellItems as $item)
                    <div class="profile__item-card">
                        <div class="profile__item-image-wrapper">
                            <img src="{{ asset('storage/' . $item->image_url) }}" alt="{{ $item->name }}" class="profile__item-image">
                            @if($item->is_sold)
                                <div class="sold-label">Sold</div>
                            @endif
                        </div>
                        <p class="profile__item-name">{{ $item->name }}</p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</main>
@endsection