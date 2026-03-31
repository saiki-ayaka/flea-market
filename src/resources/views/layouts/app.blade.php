<!DOCTYPE html>

<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>フリマアプリ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @yield('css')
</head>

<body class="l-page">
    <header class="main-header">
    <div class="main-header__container">
        <h1 class="main-header__logo">
            <a href="{{ route('item.index') }}" class="main-header__logo-link">
                COACHTECH
            </a>
        </h1>

        @if(!Route::is('register') && !Route::is('login') && !Route::is('verification.notice'))
            <form class="main-header__search-form" action="{{ route('item.index') }}" method="GET">
                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？" class="main-header__search-input">
    </form>
@endif

        <nav class="main-header__nav">
            @guest
                @if(!Route::is('register') && !Route::is('login') && !Route::is('verification.notice'))
                <a href="{{ route('login') }}" class="main-header__link">ログイン</a>
                <a href="{{ route('register') }}" class="main-header__link">会員登録</a>
                @endif
            @endguest

            @auth
                @if(!Route::is('verification.notice'))
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="main-header__link" style="background:none; border:none; cursor:pointer;">ログアウト</button>
                    </form>

                    <a href="{{ route('profile.index') }}" class="main-header__link">マイページ</a>
                    <a href="{{ route('items.create') }}" class="main-header__btn-sell">出品</a>
                @endif
            @endauth
        </nav>
    </div>
</header>
    @yield('content')
    <script>
    document.getElementById('image').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const preview = document.getElementById('preview');
        const reader = new FileReader();

        // ファイルが画像でない場合は処理しない
        if (!file.type.match('image.*')) {
            alert('画像ファイルを選択してください');
            return;
        }

        reader.onload = function (e) {
            // すでにある画像を消す（リセット）
            preview.innerHTML = '';
            
            // 新しいimg要素を作成してプレビューに追加
            const img = document.createElement('img');
            img.src = e.target.result;
            preview.appendChild(img);
        }

        reader.readAsDataURL(file);
    });
    </script>
</body>
</html>