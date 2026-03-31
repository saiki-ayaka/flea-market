@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-container">
    <h2 class="login-title">ログイン</h2>

    <form method="POST" action="{{ route('login') }}" class="login-form">
        @csrf

        {{-- メールアドレス --}}
        <div class="form-group">
            <label for="email" class="form-label">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus>
            @error('email')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        {{-- パスワード --}}
        <div class="form-group">
            <label for="password" class="form-label">パスワード</label>
            <input id="password" type="password" name="password">
            @error('password')
                <span class="error-message">{{ $message }}</span>
            @enderror

            @error('login_error')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        {{-- ログインボタン --}}
        <button type="submit" class="btn-login">
            ログインする
        </button>

        {{-- 会員登録リンク --}}
        <div class="register-link">
            <a href="{{ route('register') }}">会員登録はこちら</a>
        </div>
    </form>
</div>
@endsection