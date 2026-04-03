@extends('layouts.front')

@section('content')
    <div class="container">
        <hr color="#c0c0c0">
        {{-- ★ここから：検索＆並べ替えフォーム（中央寄せ）★ --}}
        <div class="row pt-4 mb-4">
            <div class="col-md-10 mx-auto">
                {{-- route('book.index') はご自身のルーティング名に合わせてください --}}
                <form action="{{ route('book.index') }}" method="get">
                    <div class="form-row d-flex align-items-center justify-content-center">
                        {{-- 検索入力 --}}
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="cond_title" placeholder="本を検索..." value="{{ $cond_title }}">
                        </div>
                        {{-- 並べ替え選択 --}}
                        <div class="col-md-3">
                            <select name="sort" class="form-control" onchange="this.form.submit()">
                                <option value="new" {{ $sort == 'new' ? 'selected' : '' }}>新しい順</option>
                                <option value="old" {{ $sort == 'old' ? 'selected' : '' }}>古い順</option>
                            </select>
                        </div>
                        {{-- 検索ボタン --}}
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">検索</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        {{-- ★ここまで：検索フォーム★ --}}
        <div class="row">
            <div class="col-md-8 mx-auto text-right">
                <div id="search-count" class="mb-2 text-secondary">
                 {{-- 初回表示時（ページを開いた時）の件数 --}}
             検索結果：{{ $posts->count() + ($headline ? 1 : 0) }} 件
                 </div>
            </div>
        </div>
        <div id="loading" class="d-none text-center my-4">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">検索中...</p>
        </div>
        <div id="headline">
        @if (!is_null($headline))
            <div class="row">
                <div class="headline col-md-10 mx-auto">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="caption mx-auto">
                                <div class="image">
                                <img src="{{ $headline->image_path 
                                     ? asset('storage/image/' . $headline->image_path) 
                                    : asset('images/no-image.png') }}" >
                                </div>
                                <div class="title p-2">
                                    <h1>{!! str_replace($cond_title, '<span class="highlight">'.$cond_title.'</span>', Str::limit($headline->title, 70)) !!}</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <p class="body mx-auto">{!! str_replace($cond_title, '<span class="highlight">'.$cond_title.'</span>', Str::limit($headline->body, 650)) !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        </div>
        <hr color="#c0c0c0">
        <div class="row">
    <div class="posts col-md-8 mx-auto mt-3">
        {{-- ★ここ！IDは foreach の「外側」に置きます --}}
        <div id="book-list">
            @forelse($posts as $post)
                <div class="post">
                    <div class="row">
                        <div class="text col-md-6">
                            <div class="date">
                                {{ $post->updated_at->format('Y年m月d日') }}
                            </div>
                            <div class="title">
                                {!! str_replace($cond_title, '<span class="highlight">'.$cond_title.'</span>', Str::limit($post->title, 150)) !!}
                            </div>
                            <div class="body mt-3">
                                {!! str_replace($cond_title, '<span class="highlight">'.$cond_title.'</span>', Str::limit($post->body, 1500)) !!}
                            </div>
                        </div>
                        <div class="image col-md-6 text-right mt-4">
                            @if ($post->image_path)
                                <img src="{{ asset('storage/image/' . $post->image_path) }}" class="img-fluid">
                            @else
                                <img src="{{ asset('images/no-image.png') }}" class="img-fluid">    
                            @endif
                        </div>
                    </div>
                    <hr color="#c0c0c0">
                </div> {{-- ここで post の div を閉じる --}}
                {{-- ★ 2. ループが終わる直前に @empty を入れる --}}
            @empty
                {{-- 検索結果が0件の時に出るメッセージ --}}
                <div class="text-center py-5">
                    <p class="text-muted">お探しの本は見つかりませんでした。</p>
                    {{-- もしアイコンを使いたいなら以下も（FontAwesomeが必要） --}}
                    <i class="fas fa-search fa-3x" style="color: #eee;"></i>
                </div>
            @endforelse {{-- ★ 3. 最後は @endforelse で閉じる --}}
        </div> {{-- ★ここで book-list の div を閉じる --}}
        <div id="pagination-wrapper" class="d-flex justify-content-center mt-4">
         {{ $posts->appends(['cond_title' => $cond_title, 'sort' => $sort])->links() }}
        </div>
    </div>
</div>
<script>
    const searchForm = document.querySelector('form');
    const sortSelect = document.querySelector('select[name="sort"]');

    // ★ 並べ替えが選ばれた時、リロードさせずにJSの送信処理を実行させる
    sortSelect.onchange = function() {
        searchForm.dispatchEvent(new Event('submit'));
    };

    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const loading = document.getElementById('loading');
        const bookList = document.getElementById('book-list');
        const headline = document.getElementById('headline');

        loading.classList.remove('d-none');
        bookList.style.opacity = '0.3';

        let formData = new FormData(this);
        let params = new URLSearchParams(formData);

        fetch('{{ route("book.index") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            let parser = new DOMParser();
            let doc = parser.parseFromString(html, 'text/html');
            let newContent = doc.querySelector('#book-list').innerHTML;
            bookList.innerHTML = newContent;
            let newheadlineContent = doc.querySelector('#headline').innerHTML;
            headline.innerHTML = newheadlineContent;

            let newCount = doc.querySelector('#search-count').innerHTML;
            document.querySelector('#search-count').innerHTML = newCount;

            document.querySelector('#pagination-wrapper').innerHTML = doc.querySelector('#pagination-wrapper').innerHTML;

            loading.classList.add('d-none');
            bookList.style.opacity = '1';
            window.history.pushState(null, '', '?' + params.toString());
        })
        
        .catch(error => {
            console.error('Error:', error);
            loading.classList.add('d-none');
            bookList.style.opacity = '1';
        });
    });
    // <script> 内の最後の方に追加
    document.addEventListener('click', function(e) {
    // ページネーションのリンク（aタグ）がクリックされたか判定
        if (e.target.closest('#pagination-wrapper a')) {
            e.preventDefault();
            
            const url = e.target.closest('a').href;
            const loading = document.getElementById('loading');
            const bookList = document.getElementById('book-list');

            loading.classList.remove('d-none');
            bookList.style.opacity = '0.3';

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                
                // リスト、件数、そして「ページネーション自体」も書き換える
                document.querySelector('#book-list').innerHTML = doc.querySelector('#book-list').innerHTML;
                document.querySelector('#search-count').innerHTML = doc.querySelector('#search-count').innerHTML;
                document.querySelector('#pagination-wrapper').innerHTML = doc.querySelector('#pagination-wrapper').innerHTML;

                loading.classList.add('d-none');
                bookList.style.opacity = '1';
                window.scrollTo(0, 0); // ページ上部へスクロール
                window.history.pushState(null, '', url);
            });
        }
    });
</script>
@endsection