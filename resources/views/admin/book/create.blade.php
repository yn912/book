{{-- layouts/admin.blade.phpを読み込む --}}
@extends('layouts.admin')


{{-- admin.blade.phpの@yield('title')に'ニュースの新規作成'を埋め込む --}}
@section('title', 'ブックの新規作成')

{{-- admin.blade.phpの@yield('content')に以下のタグを埋め込む --}}
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <h2>ブック新規作成</h2>
                <form action="{{ route('admin.book.create') }}" method="post" enctype="multipart/form-data">

                    @if (count($errors) > 0)
                        <ul>
                            @foreach($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    @endif
                    <div class="form-group row">
                        <label class="col-md-2">タイトル</label>
                        <div class="col-md-10">
                            <input type="text" id="title-input" class="form-control" name="title" maxlength="100" value="{{ old('title') }}">
                            {{-- カウント表示用の場所 --}}
                            <div class="text-right small text-secondary">
                             残り <span id="title-count">100</span> 文字
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-4">
                        <label class="col-md-2 col-form-label font-weight-bold">紹介文</label>
                        <div class="col-md-10">
                            <textarea id="body-input" class="form-control" name="body" rows="10" maxlength="2000" placeholder="本の内容について詳しく記入してください">{{ old('body') }}</textarea>
                            <div class="text-right small text-secondary mt-1">
                                残り <span id="body-count">2000</span> 文字
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2">本の表紙</label>
                        <div class="col-md-10">
                            <input type="file" class="form-control-file" name="image">
                        </div>
                    </div>
                    @csrf
                    <input type="submit" class="btn btn-primary" value="更新">
                </form>
            </div>
        </div>
    </div>
    <script>
        // タイトルの残り文字数カウント
        const titleInput = document.getElementById('title-input');
        const titleCount = document.getElementById('title-count');
        const titleMax = 100; // 最大文字数

        titleInput.addEventListener('input', () => {
            const remaining = titleMax - titleInput.value.length;
            titleCount.textContent = remaining;
            
            // 残りが少なくなったら赤くする演出
            if (remaining <= 10) {
                titleCount.style.color = 'red';
                titleCount.style.fontWeight = 'bold';
            } else {
                titleCount.style.color = 'inherit';
                titleCount.style.fontWeight = 'normal';
            }
        });

        // --- 本文（紹介文）の処理 (残り文字数カウントに変更) ---
        const bodyInput = document.getElementById('body-input');
        const bodyCount = document.getElementById('body-count');
        const bodyMax = 2000; // ★最大文字数を設定

        // 入力時の処理
        bodyInput.addEventListener('input', () => {
        const remaining = bodyMax - bodyInput.value.length;
        bodyCount.textContent = remaining;
        
        // ★ 本文は長いので、残り200文字(10%)を切ったら赤くする設定
        if (remaining <= 200) {
            bodyCount.style.color = 'red';
            bodyCount.style.fontWeight = 'bold';
        } else {
            bodyCount.style.color = 'inherit';
            bodyCount.style.fontWeight = 'normal';
        }
        });

        // ★ ページ読み込み時にも実行（編集画面やエラーで戻った時のため）
        // これを入れないと、最初は「2000」のままになってしまいます
        window.addEventListener('DOMContentLoaded', () => {
        titleInput.dispatchEvent(new Event('input'));
        bodyInput.addEventListener('input', () => {}); // body用も発火
        // 簡易的に初期計算を実行
        const remainingBody = bodyMax - bodyInput.value.length;
        bodyCount.textContent = remainingBody;
        });
    </script>
@endsection