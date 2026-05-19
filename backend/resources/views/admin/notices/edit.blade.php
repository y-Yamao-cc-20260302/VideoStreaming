@extends('admin.layouts.app')
@section('title', 'お知らせ編集')
@section('content')
<div class="card"><div class="card-body">
  @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
  <form method="POST" action="{{ route('admin.notices.update', $notice) }}">@include('admin.notices._form')</form>
</div></div>
@endsection
