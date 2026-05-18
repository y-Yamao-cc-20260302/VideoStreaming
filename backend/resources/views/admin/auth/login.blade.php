<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>管理画面ログイン | {{ config('app.name') }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo"><b>{{ config('app.name') }}</b> 管理</div>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">管理者アカウントでログイン</p>
      @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
      @endif
      <form method="POST" action="{{ route('admin.login') }}">
        @csrf
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="メールアドレス" value="{{ old('email') }}" required autofocus>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="パスワード" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" name="remember">
              <label for="remember">ログイン状態を保持</label>
            </div>
          </div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">ログイン</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
