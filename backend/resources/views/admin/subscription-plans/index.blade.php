@extends('admin.layouts.app')
@section('title', 'プラン管理')
@section('content')
<div class="card">
  <div class="card-header"><a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> 登録</a></div>
  <div class="card-body">
    <table class="table">
      <thead><tr><th>ID</th><th>名前</th><th>コード</th><th class="text-right">月額(円)</th><th>提供中</th><th class="text-right">操作</th></tr></thead>
      <tbody>
        @forelse($plans as $p)
          <tr>
            <td>{{ $p->id }}</td><td>{{ $p->name }}</td><td>{{ $p->code }}</td>
            <td class="text-right">{{ number_format($p->price_jpy) }}</td>
            <td>{{ $p->is_active ? '◯' : '×' }}</td>
            <td class="text-right">
              <a href="{{ route('admin.subscription-plans.edit', $p) }}" class="btn btn-sm btn-info">編集</a>
              <form method="POST" action="{{ route('admin.subscription-plans.destroy', $p) }}" class="d-inline" onsubmit="return confirm('削除しますか？')">
                @csrf @method('DELETE')<button class="btn btn-sm btn-danger">削除</button>
              </form>
            </td>
          </tr>
        @empty<tr><td colspan="6" class="text-center text-muted">なし</td></tr>@endforelse
      </tbody>
    </table>
    {{ $plans->links() }}
  </div>
</div>
@endsection
