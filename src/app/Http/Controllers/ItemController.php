<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request) {
        // 1. パラメータ（タブと検索ワード）を取得
        $isMylist = $request->query('tab') === 'mylist';
        $keyword = $request->query('keyword'); // ヘッダーの name="keyword" から取得

        if ($isMylist) {
            // --- マイリスト表示のロジック ---
            if (Auth::check()) {
                // ログイン中：いいねした商品をベースにクエリ開始
                $query = Auth::user()->favoriteItems();

                // 検索ワードがあれば「商品名」で部分一致検索を追加
                if (!empty($keyword)) {
                $query->where('items.name', 'LIKE', "%{$keyword}%");
                }

                $items = $query->get(); 
            } else {
                $items = collect();
            }
        } else {
            // --- おすすめ（通常一覧）表示のロジック ---
            $query = Item::query();

            // 自分が出品した商品を除外
            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }

            // 検索ワードがあれば「商品名」で部分一致検索を追加
            if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%{$keyword}%");
            }

            $items = $query->get();
        }

        return view('items.index', compact('items'));
    }

    public function show($id) {
    // categoriesも一緒に取得するように変更
        $item = Item::with('categories', 'comments.user')->findOrFail($id);

        return view('items.show', ['item' => $item]);
    }

    public function comments()
    {
        // ItemはたくさんのCommentを持つ
        return $this->hasMany(Comment::class);
    }

    public function storeComment(Request $request, $id)
    {
        // 1. バリデーション (FN020-2: 入力必須、最大255文字)
        $request->validate([
            'comment' => 'required|string|max:255',
        ], [
            'comment.required' => 'コメントを入力してください',
            'comment.max' => '255文字以内で入力してください',
        ]);

        // 2. データの保存
        // $id は商品のID、Auth::id() はログインしているユーザーのIDです
        \App\Models\Comment::create([
            'item_id' => $id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        // 3. 元の詳細画面に戻る（FN020-3: コメント数が増加表示されるようリロード）
        return back();
    }

    // 出品画面を表示
    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();
        return view('items.create', compact('categories', 'conditions'));
    }

    // 出品処理
    public function store(ExhibitionRequest $request)
    {
        // 2. 画像の保存処理
        // storage/app/public/item_images フォルダに保存
        $imagePath = $request->file('image')->store('item_images', 'public');

        // 3. 商品データの登録
        $item = Item::create([
            'user_id'      => Auth::id(), // 出品したユーザーのID
            'condition' => $request->condition_id,
            'name'         => $request->name,
            'brand'        => $request->brand, // 空でもOK
            'description'  => $request->description,
            'price'        => $request->price,
            'image_url'    => $imagePath, // 保存したパスをDBに入れる
        ]);

        // 4. カテゴリの紐付け（中間テーブルへの一括保存）
        // $request->categories はチェックボックスのID配列（[1, 3, 5]など）が入っている
        if ($request->has('category_ids')) {
            $item->categories()->attach($request->category_ids);
        }

        return redirect()->route('item.index');
    }
}
