@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}">
@endsection

@section('content')
<main class="profile__wrapper">
    <div class="profile__content">
        <h2 class="profile__title">プロフィール設定</h2>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="profile__image-section">
                <div class="profile__image-preview" id="preview">
                    @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像">
                    @else
                        <div class="default-circle"></div>
                    @endif
                </div>
                <label class="profile__image-label">
                    画像を選択する
                    <input type="file" name="image" id="image" class="profile__image-input" accept="image/*">
                </label>
            </div>

            <div class="form-group">
                <label for="name" class="form-label">ユーザー名</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $user->name) }}">
                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="postcode" class="form-label">郵便番号</label>
                <input type="text" name="postcode" id="postcode" class="form-input" value="{{ old('postcode' ,$user->postcode) }}">
                @error('postcode')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="address" class="form-label">住所</label>
                <input type="text" name="address" id="address" class="form-input" value="{{ old('address', $user->address) }}">
                @error('address')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="building" class="form-label">建物名</label>
                <input type="text" name="building" id="building" class="form-input" value="{{ old('building', $user->building) }}">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">更新する</button>
            </div>
        </form>
    </div>
</main>
@endsection