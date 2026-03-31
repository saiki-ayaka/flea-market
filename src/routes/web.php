<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// --- 誰でも見れるルート ---
Route::get('/', [ItemController::class, 'index'])->name('item.index'); // トップを商品一覧に
Route::get('/items', [ItemController::class, 'index']);
Route::get('/items/{id}', [ItemController::class, 'show'])->name('items.show');

// --- ゲスト（未ログイン）のみのルート ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// --- ログイン済みユーザー共通のルート（メール未認証でもOK） ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // メール認証関連
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/mypage/profile');
    })->middleware('signed')->name('verification.verify');
});

// --- ログイン ＋ メール認証済みユーザー限定のルート ---
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/mypage', [ProfileController::class, 'index'])->name('profile.index');
    // プロフィール関連
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 【★ここに追加】いいね機能（FN018対応）
    Route::post('/items/{id}/favorite', [FavoriteController::class, 'toggle'])->name('favorite.toggle');

    // 商品詳細に関連するコメント投稿ルート
    // FN020-1: ログインユーザーのみが実際に送信できるよう 'auth' をかけます
    Route::post('/items/{id}/comment', [CommentController::class, 'store'])->name('comment.store')->middleware('auth');

    // 商品出品関連
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    // 購入画面の表示 (ブラウザで見るためのURL)
    Route::get('/purchase/{id}', [PurchaseController::class, 'show'])->name('purchase.show');

    // 購入処理 (ボタンを押した時の動作)
    Route::post('/purchase/{id}', [PurchaseController::class, 'store'])->name('purchase.store');

    // 送付先住所変更画面の表示
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->name('purchase.address');

    // 住所更新処理
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])->name('address.update');

    // 購入完了後の遷移先（Stripeからの戻り先）
    Route::get('/purchase/success/{id}', [PurchaseController::class, 'success'])->name('purchase.success');
});
