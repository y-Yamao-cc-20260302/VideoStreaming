@extends('admin.layouts.app')
@section('title', '動画登録')
@section('breadcrumb')
  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">ダッシュボード</a></li>
  <li class="breadcrumb-item"><a href="{{ route('admin.videos.index') }}">動画管理</a></li>
  <li class="breadcrumb-item active">登録</li>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('admin.videos.store') }}" enctype="multipart/form-data">
      @include('admin.videos._form')
    </form>
  </div>
</div>
@endsection
