@extends('admin.layouts.app')
@section('title', 'レビュー管理')
@section('content')
<div class="card"><div class="card-body">
  <table class="table">
    <thead><tr><th>ID</th><th>動画</th><th>投稿者</th><th>★</th><th>コメント</th><th>投稿日</th><th class="text-right">操作</th></tr></thead>
    <tbody>
      @forelse($reviews as $r)
        <tr>
          <td>{{ $r->id }}</td>
          <td>{{ $r->video?->title ?? '-' }}</td>
          <td>{{ $r->user?->nickname ?? $r->user?->name ?? '-' }}</td>
          <td>{{ $r->rating }}</td>
          <td>{{ \Illuminate\Support\Str::limit($r->comment, 60) }}</td>
          <td>{{ $r->created_at?->format('Y-m-d') }}</td>
          <td class="text-right">
            <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" class="d-inline" onsubmit="return confirm('削除しますか？')">
              @csrf @method('DELETE')<button class="btn btn-sm btn-danger">削除</button>
            </form>
          </td>
        </tr>
      @empty<tr><td colspan="7" class="text-center text-muted">なし</td></tr>@endforelse
    </tbody>
  </table>
  {{ $reviews->links() }}
</div></div>
@endsection
