@extends('admin.layouts.app')
@section('title', '会員詳細')
@section('content')
<div class="card mb-3">
  <div class="card-header"><h3 class="card-title">プロフィール</h3></div>
  <div class="card-body">
    <dl class="row mb-0">
      <dt class="col-sm-3">ID</dt><dd class="col-sm-9">{{ $user->id }}</dd>
      <dt class="col-sm-3">メール</dt><dd class="col-sm-9">{{ $user->email }}</dd>
      <dt class="col-sm-3">氏名</dt><dd class="col-sm-9">{{ $user->name }}</dd>
      <dt class="col-sm-3">ニックネーム</dt><dd class="col-sm-9">{{ $user->nickname ?? '-' }}</dd>
      <dt class="col-sm-3">登録日</dt><dd class="col-sm-9">{{ $user->created_at?->format('Y-m-d H:i') }}</dd>
      <dt class="col-sm-3">プラン</dt>
      <dd class="col-sm-9">
        @if($user->activeSubscription)
          {{ $user->activeSubscription->plan?->name }} ({{ $user->activeSubscription->started_at?->format('Y-m-d') }} 〜)
        @else - @endif
      </dd>
      <dt class="col-sm-3">ステータス</dt>
      <dd class="col-sm-9">
        <form method="POST" action="{{ route('admin.users.status', $user) }}" class="form-inline">
          @csrf @method('PATCH')
          <select name="status" class="form-control mr-2">
            <option value="active" @selected($user->status === 'active')>アクティブ</option>
            <option value="suspended" @selected($user->status === 'suspended')>停止</option>
          </select>
          <button class="btn btn-primary btn-sm">変更</button>
        </form>
      </dd>
    </dl>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3 class="card-title">直近の視聴履歴</h3></div>
  <div class="card-body p-0">
    <table class="table mb-0">
      <thead><tr><th>動画</th><th>進捗（秒）</th><th>視聴日時</th></tr></thead>
      <tbody>
        @forelse($watchHistories as $h)
          <tr><td>{{ $h->video?->title ?? '-' }}</td><td>{{ $h->progress_sec }}</td><td>{{ $h->watched_at?->format('Y-m-d H:i') }}</td></tr>
        @empty<tr><td colspan="3" class="text-center text-muted">なし</td></tr>@endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
