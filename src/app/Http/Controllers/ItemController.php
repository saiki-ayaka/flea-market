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
    public function index(Request $request)
    {
        $isMylist = $request->query('tab') === 'mylist';
        $keyword = $request->query('keyword');

        if ($isMylist) {
            if (Auth::check()) {
                $query = Auth::user()->favoriteItems();

                if (!empty($keyword)) {
                $query->where('items.name', 'LIKE', "%{$keyword}%");
                }

                $items = $query->get();
            } else {
                $items = collect();
            }
        } else {
            $query = Item::query();

            if (Auth::check()) {
                $query->where('user_id', '!=', Auth::id());
            }

            if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%{$keyword}%");
            }

            $items = $query->get();
        }

        return view('items.index', compact('items'));
    }

    public function show($id)
    {
        $item = Item::with('categories', 'comments.user')->findOrFail($id);

        return view('items.show', ['item' => $item]);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function storeComment(CommentRequest $request, $id)
    {
        \App\Models\Comment::create([
            'item_id' => $id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return back();
    }

    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();
        return view('items.create', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $imagePath = $request->file('image')->store('item_images', 'public');

        $item = Item::create([
            'user_id' => Auth::id(),
            'condition' => $request->condition_id,
            'name' => $request->name,
            'brand' => $request->brand,
            'description' => $request->description,
            'price' => $request->price,
            'image_url' => $imagePath,
        ]);

        if ($request->has('category_ids')) {
            $item->categories()->attach($request->category_ids);
        }

        return redirect()->route('item.index');
    }
}
