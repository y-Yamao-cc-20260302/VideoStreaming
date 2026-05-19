@extends('admin.layouts.app')
@section('title', 'ジャンル管理')
@section('content')
<div class="card">
  <div class="card-header"><a href="{{ route('admin.genres.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> 登録</a></div>
  <div class="card-body">
    <table class="table">
      <thead><tr><th>ID</th><th>名前</th><th>slug</th><th class="text-right">操作</th></tr></thead>
      <tbody>
        @forelse($genres as $g)
          <tr>
            <td>{{ $g->id }}</td><td>{{ $g->name }}</td><td>{{ $g->slug }}</td>
            <td class="text-right">
              <a href="{{ route('admin.genres.edit', $g) }}" class="btn btn-sm btn-info">編集</a>
              <form method="POST" action="{{ route('admin.genres.destroy', $g) }}" class="d-inline" onsubmit="return confirm('削除しますか？')">
                @csrf @method('DELETE')<button class="btn btn-sm btn-danger">削除</button>
              </form>
            </td>
          </tr>
        @empty<tr><td colspan="4" class="text-center text-muted">なし</td></tr>@endforelse
      </tbody>
    </table>
    {{ $genres->links() }}
  </div>
</div>
@endsection
