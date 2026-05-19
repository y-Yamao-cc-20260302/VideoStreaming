@extends('admin.layouts.app')
@section('title', 'ダッシュボード')
@section('breadcrumb')
  <li class="breadcrumb-item active">ダッシュボード</li>
@endsection

@section('content')
<div class="row">
  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">総会員数</span>
        <span class="info-box-number">{{ number_format($totalUsers) }}</span>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-success"><i class="fas fa-yen-sign"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">有料会員数</span>
        <span class="info-box-number">{{ number_format($paidUsers) }}</span>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-warning"><i class="fas fa-play"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">当月再生数</span>
        <span class="info-box-number">{{ number_format($monthlyWatchCount) }}</span>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-primary"><i class="fas fa-user-plus"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">当月新規登録</span>
        <span class="info-box-number">{{ number_format($monthlyNewUsers) }}</span>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><h3 class="card-title">人気動画 TOP10（直近 7 日）</h3></div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead><tr><th>#</th><th>タイトル</th><th class="text-right">再生数</th></tr></thead>
          <tbody>
            @forelse($popularVideos as $i => $video)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td><a href="{{ route('admin.videos.edit', $video) }}">{{ $video->title }}</a></td>
                <td class="text-right">{{ number_format($video->recent_watch_count) }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-muted text-center">データなし</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><h3 class="card-title">最近のレビュー</h3></div>
      <div class="card-body p-0">
        <table class="table table-sm mb-0">
          <thead><tr><th>動画</th><th>投稿者</th><th>★</th><th>投稿日</th></tr></thead>
          <tbody>
            @forelse($recentReviews as $review)
              <tr>
                <td>{{ $review->video->title ?? '-' }}</td>
                <td>{{ $review->user->nickname ?? $review->user->name ?? '-' }}</td>
                <td>{{ $review->rating }}</td>
                <td>{{ $review->created_at?->format('Y-m-d H:i') }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-muted text-center">データなし</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
