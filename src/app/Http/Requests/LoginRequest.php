<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ログインに「名前」や「confirmed」は不要なので削除します
            'email'    => 'required|email',
            'password' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'email.required'     => 'メールアドレスを入力してください',
            'email.email'        => 'メールアドレスの形式で入力してください',
            'password.required'  => 'パスワードを入力してください',
        ];
    }
}