# Docker 構成

## コンテナ構成

```
┌─────────────────────────────────────────────────┐
│  ブラウザ                                         │
│  http://localhost                                │
└────────────────────┬────────────────────────────┘
                     │ :80
┌────────────────────▼────────────────────────────┐
│  nginx                                           │
│  ルーティング:                                    │
│    /api/*   → FastCGI → app:9000                │
│    /admin/* → FastCGI → app:9000                │
│    /*       → proxy  → frontend:5173            │
└───────┬────────────────────────┬────────────────┘
        │ FastCGI :9000          │ HTTP :5173
┌───────▼──────────┐   ┌────────▼───────────────┐
│  app             │   │  frontend              │
│  PHP 8.2-FPM     │   │  Node 20 (Vite)        │
│  Laravel         │   │  React SPA             │
│  /var/www/app    │   │  /app (./frontend)     │
└───────┬──────────┘   └────────────────────────┘
        │ PostgreSQL :5432
┌───────▼──────────┐
│  db              │
│  PostgreSQL 16   │
│  app DB          │
└──────────────────┘
```

## ポート

| ホスト | コンテナ | 用途 |
|---|---|---|
| 80 | nginx:80 | メインエントリーポイント |
| 5432 | db:5432 | PostgreSQL 直接接続（TablePlus / DBeaver 等） |
| 5173 | frontend:5173 | Vite 直接アクセス（任意） |

## ボリューム

| ボリューム名 | マウント先 | 用途 |
|---|---|---|
| `./backend` (bind mount) | app: `/var/www/app` | Laravel ソースコード |
| `./backend` (bind mount) | nginx: `/var/www/app` | 静的ファイル配信用 |
| `./frontend` (bind mount) | frontend: `/app` | React ソースコード |
| `frontend_node_modules` | frontend: `/app/node_modules` | node_modules をホストと分離 |
| `db_data` | db: `/var/lib/postgresql/data` | PostgreSQL データ永続化 |

## 環境変数（主要なもの）

### `backend/.env`

| キー | 値（例） |
|---|---|
| `APP_NAME` | `VideoStreamingService` |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | `db` |
| `DB_PORT` | `5432` |
| `DB_DATABASE` | `video_streaming` |
| `DB_USERNAME` | `app` |
| `DB_PASSWORD` | `secret` |
| `JWT_SECRET` | `php artisan jwt:secret` で生成 |

### `docker-compose.yml` (db サービス)

| キー | 値 |
|---|---|
| `POSTGRES_DB` | `video_streaming` |
| `POSTGRES_USER` | `app` |
| `POSTGRES_PASSWORD` | `secret` |

## 認証方式

| 対象 | 方式 |
|---|---|
| 管理画面 (`/admin`) | Laravel セッション認証（Cookie + CSRF） |
| フロント SPA (`/api`) | JWT（`tymon/jwt-auth`）。`Authorization: Bearer <token>` ヘッダーで送信 |

## 初回セットアップ手順

ルート `README.md` を参照。
