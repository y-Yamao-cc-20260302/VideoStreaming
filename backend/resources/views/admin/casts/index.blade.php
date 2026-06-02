@extends('admin.layouts.app')
@section('title', '出演者管理')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">ダッシュボード</a></li>
  <li class="breadcrumb-item active">出演者管理</li>
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <a href="{{ route('admin.casts.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> 出演者を登録</a>
  </div>
  <div class="card-body">
    <form method="GET" class="form-inline mb-3">
      <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control mr-2" placeholder="キーワード">
      <select name="occupation_id" class="form-control mr-2">
        <option value="">職業：すべて</option>
        @foreach($occupations as $occupation)
          <option value="{{ $occupation->id }}" @selected(request('occupation_id') == $occupation->id)>{{ $occupation->name }}</option>
        @endforeach
      </select>
      <select name="publish" class="form-control mr-2">
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
          <th>名前</th>
          <th>職業</th>
          <th>公開</th>
          <th class="text-right">操作</th>
        </tr>
      </thead>
      <tbody>
        @forelse($casts as $cast)
          <tr>
            <td>{{ $cast->id }}</td>
            <td>{{ $cast->name }}</td>
            <td>{{ $cast->occupation?->name }}</td>
            <td>
              <form method="POST" action="{{ route('admin.casts.publish', $cast) }}" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm {{ $cast->is_publish ? 'btn-success' : 'btn-secondary' }}">
                  {{ $cast->is_publish ? '公開中' : '非公開' }}
                </button>
              </form>
            </td>
            <td class="text-right">
              <a href="{{ route('admin.casts.edit', $cast) }}" class="btn btn-sm btn-info">編集</a>
              <form method="POST" action="{{ route('admin.casts.destroy', $cast) }}" class="d-inline" onsubmit="return confirm('削除してよろしいですか？')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">削除</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted">出演者がありません</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $casts->links() }}
  </div>
</div>
@endsection
