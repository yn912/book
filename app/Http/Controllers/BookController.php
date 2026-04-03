<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Book;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $cond_title = $request->cond_title;
        $query = Book::query();
        
        if ($cond_title != '') {
            $query->where('title', 'like', '%' . $cond_title . '%')
                  ->orWhere('body', 'LIKE', "%{$cond_title}%");
        }

        $sort = $request->sort; // 名前は何でもOK
        if ($sort == 'old') {
            $query->orderBy('updated_at', 'asc'); // 古い順
        } else {
            $query->orderBy('updated_at', 'desc'); // 指定がなければ新しい順
        }

        $posts = $query->paginate(5); // 1ページ5件表示

        if (count($posts) > 0) {
            $headline = $posts->shift();
        } else {
            $headline = null;
        }

        // news/index.blade.php ファイルを渡している
        // また View テンプレートに headline、 posts、という変数を渡している
        return view('book.index', ['headline' => $headline, 'posts' => $posts,'cond_title' => $cond_title,'sort' => $sort]);
    }
}
