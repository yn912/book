<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Book;

use App\Models\BookHistory;
use Carbon\Carbon;

class BookController extends Controller
{
    public function add()
    {
        \Debugbar::info(Book::all()); 
        return view('admin.book.create');
    }

    public function create(Request $request)
    {
        // Validationを行う
        $this->validate($request, Book::$rules);

        $book = new Book;
        $form = $request->all();

        // フォームから画像が送信されてきたら、保存して、$news->image_path に画像のパスを保存する
        if (isset($form['image'])) {
            $path = $request->file('image')->store('public/image');
            $book->image_path = basename($path);
        } else {
            $book->image_path = null;
        }

        // フォームから送信されてきた_tokenを削除する
        unset($form['_token']);
        // フォームから送信されてきたimageを削除する
        unset($form['image']);

        // データベースに保存する
        $book->fill($form);
        $book->save();

        // admin/book/createにリダイレクトする
        return redirect('admin/book/create');
    }

    public function index(Request $request)
    {
        $cond_title = $request->cond_title;
        if ($cond_title != null) {
            // 検索されたら検索結果を取得する
            $posts = Book::where('title','like', "%{$cond_title}%")
                     ->orWhere('body','like',"%{$cond_title}%")     
            ->get();
        } else {
            // それ以外はすべてのニュースを取得する
            $posts = Book::all();
        }
        return view('admin.book.index', ['posts' => $posts, 'cond_title' => $cond_title]);
    }

    public function edit(Request $request)
    {
        // News Modelからデータを取得する
        $book = Book::find($request->id);
        if (empty($book)) {
            abort(404);
        }
        return view('admin.book.edit', ['book_form' => $book]);
    }

    public function update(Request $request)
    {
        // Validationをかける
        $this->validate($request, Book::$rules);
        // News Modelからデータを取得する
        $book = Book::find($request->id);
        // 送信されてきたフォームデータを格納する
        $book_form = $request->all();
        
        if ($request->remove == 'true') {
            $book_form['image_path'] = null;
        } elseif ($request->file('image')) {
            $path = $request->file('image')->store('public/image');
            $book_form['image_path'] = basename($path);
        } else {
            $book_form['image_path'] = $book->image_path;
        }

        unset($book_form['image']);
        unset($book_form['remove']);
        unset($book_form['_token']);

        // 該当するデータを上書きして保存する
        $book->fill($book_form)->save();

        $history = new BookHistory();
        $history->book_id = $book->id;
        $history->edited_at = Carbon::now();
        $history->save();

        return redirect('admin/book');
    }

    public function delete(Request $request)
    {
        // 該当するBook Modelを取得
        $book = Book::find($request->id);

        // 削除する
        $book->delete();

        return redirect('admin/book/');
    }
}
