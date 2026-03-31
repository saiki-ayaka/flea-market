@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<main class="register__wrapper">
    <div class="register__content">
        <h2 class="register__title">会員登録</h2>

        <form class="register__form" action="{{ route('register') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">ユーザー名</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name') }}">
                @error('name')
                    <p class="error-message">お名前を入力してください</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">メールアドレス</label>
                <input type="email" name="email" id="email" class="form-input" value="{{ old('email') }}">
                @error('email')
                    <p class="error-message">メールアドレスを入力してください</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">パスワード</label>
                <input type="password" name="password" id="password" class="form-input">
                @error('password')
                    <p class="error-message">{{ $message }}</p> 
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">確認用パスワード</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-input">
                @error('password_confirmation')
                    <p class="error-message">パスワードと一致しません</p>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">登録する</button>
                <a href="{{ route('login') }}" class="login-link">ログインはこちら</a>
            </div>
        </form>
    </div>
</main>
@endsection