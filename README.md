# OJT サンプルプロジェクト テンプレート

Docker + Laravel (REST API) + React SPA + Laravel Blade (管理画面) のスケルトン。
新しい OJT 用サンプルを作るたびに、このテンプレートをコピーして開始する。

## 含まれているもの

- Docker Compose 一式（nginx / app(PHP-FPM) / frontend(Node) / db(MySQL)）
- Laravel スケルトン
  - JWT 認証 API（`/api/auth/register`, `/login`, `/me`, `/refresh`, `/logout`）
  - 管理画面セッション認証（`/admin/login`, `/admin/dashboard`）
- React SPA スケルトン
  - 認証コンテキスト（localStorage + 自動リフレッシュ）
  - ログイン/新規登録/ホームのプレースホルダー画面
- 初期管理者シーダー（admin@example.com / password）

## 構成早見表

| URL | 内容 |
|---|---|
| `http://localhost/` | React SPA |
| `http://localhost/admin` | 管理画面（Blade） |
| `http://localhost/api/...` | REST API（Laravel） |
| `localhost:3306` | MySQL（TablePlus 等で直接接続） |

---

## 初回セットアップ

### 1. 環境変数ファイルを作成する

```bash
cp backend/.env.example backend/.env
```

### 2. コンテナをビルドする

```bash
docker compose build
```

### 3. Laravel キー / JWT シークレットを生成する

```bash
docker compose run --rm app composer install
docker compose run --rm app php artisan key:generate
docker compose run --rm app php artisan jwt:secret --force
```

### 4. 全コンテナを起動する

```bash
docker compose up -d
```

### 5. マイグレーションと初期データ投入

```bash
docker compose exec app php artisan migrate --seed
```

初期管理者アカウント:

| 項目 | 値 |
|---|---|
| メールアドレス | `admin@example.com` |
| パスワード | `password` |

### 6. 動作確認

| 確認先 | アクセス先 |
|---|---|
| React SPA | http://localhost/ |
| 管理画面 | http://localhost/admin |

---

## 日常の開発コマンド

```bash
docker compose up -d                              # 起動
docker compose down                               # 停止
docker compose exec app php artisan <command>     # Artisan
docker compose exec app composer <command>        # Composer
docker compose exec frontend npm run <command>    # npm

docker compose exec app php artisan test          # PHPUnit
docker compose exec frontend npm run lint         # ESLint
docker compose exec frontend npm run format       # Prettier
docker compose exec frontend npx tsc --noEmit     # 型チェック
```

---

## テンプレートとして使うとき

1. このディレクトリをコピーして新しいサンプル用ディレクトリにする
2. 以下を新しいサンプル用の名前に置き換える
   - `backend/composer.json` の `name`, `description`
   - `frontend/package.json` の `name`
   - `frontend/index.html` の `<title>`
   - `backend/.env.example` の `APP_NAME`, `DB_DATABASE`, `DB_USERNAME`
   - `docker-compose.yml` の `MYSQL_DATABASE`, `MYSQL_USER`
3. `docs/` に新規サンプルの設計書を作成してから実装に着手する

---

## 認証方式

| 対象 | 方式 |
|---|---|
| 管理画面 (`/admin`) | Laravel セッション認証（Cookie + CSRF） |
| フロント SPA (`/api`) | JWT — `Authorization: Bearer <token>` ヘッダー |
