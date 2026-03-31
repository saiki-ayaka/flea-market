@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-page">
    <div class="verify-card">
        <p class="verify-text">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        <a href="https://mailtrap.io/inboxes" target="_blank" class="btn-verify">
            認証はこちらから
        </a>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-resend">
                認証メールを再送信する
            </button>
        </form>

        @if (session('status') == 'verification-link-sent')
            <p class="verify-status">
                新しい認証リンクを送信しました。
            </p>
        @endif
    </div>
</div>
@endsection