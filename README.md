# 動画配信サービスサイト

Docker + Laravel (REST API + 管理画面 Blade) + React SPA + PostgreSQL で構成された動画配信サービスのサンプル実装。

## 構成早見表

| URL | 内容 |
|---|---|
| `http://localhost/` | React SPA（一般ユーザー向け） |
| `http://localhost/admin` | 管理画面（Blade） |
| `http://localhost/api/...` | REST API（Laravel） |
| `localhost:5432` | PostgreSQL（TablePlus / DBeaver 等で直接接続） |

## 技術スタック

| レイヤー | 技術 |
|---|---|
| インフラ | Docker / Docker Compose |
| バックエンド | Laravel 12 (PHP 8.2) |
| データベース | PostgreSQL 16 |
| フロント | React 18 + Vite + TypeScript |
| 管理画面 | Laravel Blade |
| API 仕様 | OpenAPI |
| 認証（管理画面） | Laravel セッション認証 |
| 認証（SPA） | JWT (`tymon/jwt-auth`) |

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
docker compose exec app php artisan storage:link  # サムネイル画像配信用
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

## 設計書

設計書は `docs/` 配下に集約しています。

- [docs/README.md](docs/README.md) — ドキュメント目次
- [docs/features.md](docs/features.md) — 機能一覧
- [docs/architecture/overview.md](docs/architecture/overview.md) — システム全体構成
- [docs/architecture/docker.md](docs/architecture/docker.md) — Docker 構成
- [docs/database/er.md](docs/database/er.md) / [tables.md](docs/database/tables.md) — DB 設計
- [docs/api/README.md](docs/api/README.md) / [endpoints.md](docs/api/endpoints.md) — REST API 仕様
- [docs/frontend/screens.md](docs/frontend/screens.md) — フロント画面設計
- [docs/admin/screens.md](docs/admin/screens.md) — 管理画面設計

---

## 認証方式

| 対象 | 方式 |
|---|---|
| 管理画面 (`/admin`) | Laravel セッション認証（Cookie + CSRF） |
| フロント SPA (`/api`) | JWT — `Authorization: Bearer <token>` ヘッダー |
