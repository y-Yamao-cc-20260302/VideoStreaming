@extends('admin.layouts.app')
@section('title', '会員管理')
@section('content')
<div class="card">
  <div class="card-body">
    <form method="GET" class="form-inline mb-3">
      <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control mr-2" placeholder="メール / 氏名">
      <button class="btn btn-secondary">検索</button>
    </form>
    <table class="table">
      <thead><tr><th>ID</th><th>メール</th><th>氏名</th><th>プラン</th><th>ステータス</th><th>登録日</th><th class="text-right">操作</th></tr></thead>
      <tbody>
        @forelse($users as $u)
          <tr>
            <td>{{ $u->id }}</td>
            <td>{{ $u->email }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->activeSubscription?->plan?->name ?? '-' }}</td>
            <td>
              @if($u->status === 'active')<span class="badge badge-success">アクティブ</span>
              @elseif($u->status === 'suspended')<span class="badge badge-warning">停止中</span>
              @else<span class="badge badge-secondary">退会</span>@endif
            </td>
            <td>{{ $u->created_at?->format('Y-m-d') }}</td>
            <td class="text-right"><a href="{{ route('admin.users.show', $u) }}" class="btn btn-sm btn-info">詳細</a></td>
          </tr>
        @empty<tr><td colspan="7" class="text-center text-muted">なし</td></tr>@endforelse
      </tbody>
    </table>
    {{ $users->links() }}
  </div>
</div>
@endsection
