@extends('admin.layouts.app')
@section('title', '動画管理')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">ダッシュボード</a></li>
  <li class="breadcrumb-item active">動画管理</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <a href="{{ route('admin.videos.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> 動画を登録</a>
  </div>
  <div class="card-body">
    <form method="GET" class="form-inline mb-3">
      <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control mr-2" placeholder="キーワード">
      <select name="category_id" class="form-control mr-2">
        <option value="">カテゴリ：すべて</option>
        @foreach($categories as $c)
          <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
        @endforeach
      </select>
      <select name="genre_id" class="form-control mr-2">
        <option value="">ジャンル：すべて</option>
        @foreach($genres as $g)
          <option value="{{ $g->id }}" @selected(request('genre_id') == $g->id)>{{ $g->name }}</option>
        @endforeach
      </select>
      <select name="published" class="form-control mr-2">
        <option value="">公開状態：すべて</option>
        <option value="1" @selected(request('published') === '1')>公開</option>
        <option value="0" @selected(request('published') === '0')>非公開</option>
      </select>
      <button type="submit" class="btn btn-secondary">検索</button>
    </form>

    <table class="table table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>タイトル</th>
          <th>カテゴリ</th>
          <th>公開日</th>
          <th>公開</th>
          <th class="text-right">操作</th>
        </tr>
      </thead>
      <tbody>
        @forelse($videos as $video)
          <tr>
            <td>{{ $video->id }}</td>
            <td>{{ $video->title }}</td>
            <td>{{ $video->category?->name }}</td>
            <td>{{ $video->release_date?->format('Y-m-d') }}</td>
            <td>
              <form method="POST" action="{{ route('admin.videos.publish', $video) }}" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm {{ $video->is_published ? 'btn-success' : 'btn-secondary' }}">
                  {{ $video->is_published ? '公開中' : '非公開' }}
                </button>
              </form>
            </td>
            <td class="text-right">
              <a href="{{ route('admin.videos.edit', $video) }}" class="btn btn-sm btn-info">編集</a>
              <form method="POST" action="{{ route('admin.videos.destroy', $video) }}" class="d-inline" onsubmit="return confirm('削除してよろしいですか？')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">削除</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted">動画がありません</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $videos->links() }}
  </div>
</div>
@endsection
