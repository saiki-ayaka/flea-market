<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    // 会員登録画面表示
    public function showRegister()
    {
        return view('auth.register'); // あとでこのBladeファイルを作ります
    }

    public function login(LoginRequest $request)
    {
        // ここに来る前にバリデーションは自動で終わっています
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // --- ここで分岐！ ---
            // プロフィール（郵便番号や住所）がまだ登録されていない＝「初回ログイン」とみなす
            // もしProfileモデルを使っているなら $user->profile が empty かどうかで判定
            if (empty($user->post_code) || empty($user->address)) {
                return redirect()->route('profile.edit');
            }

            // すでに住所などが登録されていれば、商品一覧（トップページ）へ
            return redirect()->route('item.index');

        }

        // ログイン失敗時のメッセージ（（メアドかパスワードが違う）した場合
        // ここで出すメッセージが画面に表示されます
        return back()->withErrors([
            'login_error' => 'ログイン情報が登録されていません',])->withInput();
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    // 3. 【直す】引数を RegisterRequest に変更し、中の validate() を消す
    public function register(RegisterRequest $request)
    {
        // ユーザー作成（バリデーション済みのデータを使用）
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // メール認証が必要な場合、通常は認証待ち画面（verify-email）へ飛ばします
        return redirect('/email/verify');
    }

    public function updateProfile(Request $request)
{
    // 1. バリデーション（必要に応じて追加してください）
    $request->validate([
        'name' => 'required',
        'postcode' => 'required',
        'address' => 'required',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // 2. 現在ログイン中のユーザーを取得してデータを更新
    $user = Auth::user();
    $user->update([
        'name' => $request->name,
        'postcode' => $request->postcode,
        'address' => $request->address,
        'building' => $request->building, // 建物名
    ]);

    // --- 画像がアップロードされた場合の処理 ---
    if ($request->hasFile('image')) {
        // 画像を public/profiles フォルダに保存し、パスを取得
        $path = $request->file('image')->store('profiles', 'public');
        // DBに保存するパスをセット
        $updateData['image_url'] = $path;
    }

    $user->update($updateData);

    // 3. 更新が終わったら、商品一覧（トップページ）へ飛ばす！
    // これで「次回から一覧に飛ぶ」ようになります
    return redirect()->route('item.index')->with('message', 'プロフィールを更新しました');
}
}
