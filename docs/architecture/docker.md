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
        │ MySQL :3306
┌───────▼──────────┐
│  db              │
│  MySQL 8.0       │
│  app DB          │
└──────────────────┘
```

## ポート

| ホスト | コンテナ | 用途 |
|---|---|---|
| 80 | nginx:80 | メインエントリーポイント |
| 3306 | db:3306 | MySQL 直接接続（TablePlus 等） |
| 5173 | frontend:5173 | Vite 直接アクセス（任意） |

## ボリューム

| ボリューム名 | マウント先 | 用途 |
|---|---|---|
| `./backend` (bind mount) | app: `/var/www/app` | Laravel ソースコード |
| `./backend` (bind mount) | nginx: `/var/www/app` | 静的ファイル配信用 |
| `./frontend` (bind mount) | frontend: `/app` | React ソースコード |
| `frontend_node_modules` | frontend: `/app/node_modules` | node_modules をホストと分離 |
| `db_data` | db: `/var/lib/mysql` | MySQL データ永続化 |

## 認証方式

| 対象 | 方式 |
|---|---|
| 管理画面 (`/admin`) | Laravel セッション認証（Blade） |
| フロント SPA | JWT（`tymon/jwt-auth`）。`Authorization: Bearer <token>` ヘッダーで送信 |

## 初回セットアップ手順

ルート README.md を参照してください。
