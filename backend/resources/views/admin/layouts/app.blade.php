<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', '管理画面') | {{ config('app.name') }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  {{-- Navbar --}}
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <span class="nav-link text-muted">{{ Auth::guard('admin')->user()->name }}</span>
      </li>
      <li class="nav-item">
        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
          @csrf
          <button type="submit" class="nav-link btn btn-link text-muted">ログアウト</button>
        </form>
      </li>
    </ul>
  </nav>

  {{-- Sidebar --}}
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
      <span class="brand-text font-weight-light ml-3">{{ config('app.name') }} 管理</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" data-accordion="false">
          <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i><p>ダッシュボード</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.videos.index') }}" class="nav-link {{ request()->routeIs('admin.videos.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-film"></i><p>動画管理</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-folder"></i><p>カテゴリ</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.genres.index') }}" class="nav-link {{ request()->routeIs('admin.genres.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-tags"></i><p>ジャンル</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.subscription-plans.index') }}" class="nav-link {{ request()->routeIs('admin.subscription-plans.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-yen-sign"></i><p>プラン</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-users"></i><p>会員管理</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-star"></i><p>レビュー</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('admin.notices.index') }}" class="nav-link {{ request()->routeIs('admin.notices.*') ? 'active' : '' }}">
              <i class="nav-icon fas fa-bullhorn"></i><p>お知らせ</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  {{-- Content --}}
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6"><h1 class="m-0">@yield('title')</h1></div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">@yield('breadcrumb')</ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div class="container-fluid">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
          </div>
        @endif
        @if(session('error'))
          <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('error') }}
          </div>
        @endif
        @yield('content')
      </div>
    </div>
  </div>

  <footer class="main-footer"><strong>{{ config('app.name') }} 管理画面</strong></footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
@stack('scripts')
</body>
</html>
