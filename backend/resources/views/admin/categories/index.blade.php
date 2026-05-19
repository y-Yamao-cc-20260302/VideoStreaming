@extends('admin.layouts.app')
@section('title', 'カテゴリ管理')
@section('breadcrumb')<li class="breadcrumb-item active">カテゴリ管理</li>@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> 登録</a>
  </div>
  <div class="card-body">
    <table class="table">
      <thead><tr><th>ID</th><th>名前</th><th>slug</th><th>表示順</th><th class="text-right">操作</th></tr></thead>
      <tbody>
        @forelse($categories as $c)
          <tr>
            <td>{{ $c->id }}</td><td>{{ $c->name }}</td><td>{{ $c->slug }}</td><td>{{ $c->sort_order }}</td>
            <td class="text-right">
              <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-info">編集</a>
              <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" class="d-inline" onsubmit="return confirm('削除しますか？')">
                @csrf @method('DELETE')<button class="btn btn-sm btn-danger">削除</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted">なし</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $categories->links() }}
  </div>
</div>
@endsection
