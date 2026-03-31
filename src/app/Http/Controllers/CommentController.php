<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $id)
    {
    // FN020-1: ログインユーザーのみ（ルートで制限済みだが Auth::id() で保存）
        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $id,
            'comment' => $request->comment,
        ]);

    // FN020-3: 合計数が増加表示されるよう元のページへリダイレクト
        return back()->with('message', 'コメントを投稿しました');
    }
}
