@extends('admin.layouts.app')
@section('title', 'お知らせ管理')
@section('content')
<div class="card">
  <div class="card-header"><a href="{{ route('admin.notices.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> 登録</a></div>
  <div class="card-body">
    <table class="table">
      <thead><tr><th>ID</th><th>タイトル</th><th>公開日</th><th>終了日</th><th class="text-right">操作</th></tr></thead>
      <tbody>
        @forelse($notices as $n)
          <tr>
            <td>{{ $n->id }}</td><td>{{ $n->title }}</td>
            <td>{{ $n->published_at?->format('Y-m-d H:i') }}</td>
            <td>{{ $n->expired_at?->format('Y-m-d H:i') ?? '-' }}</td>
            <td class="text-right">
              <a href="{{ route('admin.notices.edit', $n) }}" class="btn btn-sm btn-info">編集</a>
              <form method="POST" action="{{ route('admin.notices.destroy', $n) }}" class="d-inline" onsubmit="return confirm('削除しますか？')">
                @csrf @method('DELETE')<button class="btn btn-sm btn-danger">削除</button>
              </form>
            </td>
          </tr>
        @empty<tr><td colspan="5" class="text-center text-muted">なし</td></tr>@endforelse
      </tbody>
    </table>
    {{ $notices->links() }}
  </div>
</div>
@endsection
