# CLAUDE.md

## プロジェクト概要

OJT サンプル用のテンプレート。Docker + Laravel (REST API) + React SPA + Laravel Blade (管理画面) のスケルトン構成。
このテンプレートをコピーして、各 OJT サンプルを実装する。

| レイヤー | 技術 | 備考 |
|---|---|---|
| インフラ | Docker / Docker Compose | |
| バックエンド | Laravel | REST API のみ。将来 Python / Java への差し替えを見据え、Inertia など Laravel 固有機構は使わない |
| フロント | React SPA | 独立プロジェクト。API 経由のみでバックエンドと通信 |
| 管理画面 | Laravel Blade | HTML レスポンスを返す唯一の箇所 |
| API 仕様 | OpenAPI | `docs/api/` に配置。言語非依存に保つ |
| 認証（管理画面） | Laravel セッション認証 | Blade 専用。CSRF トークン使用 |
| 認証（SPA） | JWT（tymon/jwt-auth） | `Authorization: Bearer <token>` ヘッダー。セッション不使用 |

> **重要**: バックエンドは将来差し替え可能性があるため、Laravel 固有の仕組み(Inertia、Blade テンプレートを API レスポンスで返す等)をフロント側で前提にしない。

## ディレクトリ構成

```
.
├── docker-compose.yml
├── docker/
│   ├── app/Dockerfile      # PHP 8.2-FPM + Composer
│   ├── frontend/Dockerfile # Node 20
│   └── nginx/              # Nginx リバースプロキシ
│       ├── Dockerfile
│       └── default.conf
├── backend/                # Laravel アプリケーション
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/AuthController.php       # JWT 認証
│   │   │   ├── Controllers/Admin/AdminAuthController.php # 管理画面ログイン
│   │   │   ├── Controllers/Admin/DashboardController.php # 管理画面トップ
│   │   │   ├── Middleware/AdminAuthenticate.php
│   │   │   └── Requests/Api/Auth/                       # Login/Register
│   │   └── Models/         # User, Admin のみ。サンプル固有モデルは追加する
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   │   ├── migrations/     # users, admins のみ
│   │   └── seeders/        # AdminSeeder
│   ├── public/
│   ├── routes/
│   │   ├── api.php         # REST API ルート（認証のみ）
│   │   └── web.php         # 管理画面ルート
│   ├── resources/
│   │   └── views/admin/    # 管理画面 Blade（layouts / auth / dashboard）
│   ├── storage/
│   └── .env.example
├── frontend/               # React SPA
│   └── src/
│       ├── api/            # auth.ts, client.ts
│       ├── components/common/ # LoadingSpinner, Pagination, Toast
│       ├── contexts/AuthContext.tsx
│       ├── hooks/useToast.ts
│       ├── pages/          # HomePage, auth/(Login|Register)
│       ├── router/         # RequireAuth
│       └── types/          # User, Paginated
└── docs/                   # 設計書置き場 → docs/README.md 参照
    ├── architecture/
    ├── frontend/
    ├── admin/
    ├── api/
    └── database/
```

## 開発用コマンド

```bash
# コンテナ起動
docker compose up -d

# Composer
docker compose exec app composer <command>

# Artisan
docker compose exec app php artisan <command>

# npm
docker compose exec frontend npm run <command>

# TypeScript 型チェック
docker compose exec frontend npx tsc --noEmit
```

## コーディング規約

### バックエンド (Laravel)

- `routes/api.php` に REST API を集約。HTML を返すのは管理画面 Blade のみ
- API レスポンスは**言語非依存な JSON 構造**に保つ。他言語で再現できない Laravel 固有変換を API 境界に出さない
- Eloquent リレーション結果はそのまま返さず `Resource` クラスで整形する(他言語で再現可能な範囲に留める)
- `Carbon` の独自フォーマット・enum キャストの挙動などが API 境界に漏れていないか常に確認する
- セッション・CSRF に依存した API 設計は禁止
- API（SPA向け）の認証は JWT（`tymon/jwt-auth`）を使用する
- 管理画面は Laravel セッション認証を使用する（`/admin` ルート）

### フロント (React SPA)

- コンポーネント名は **PascalCase**
- ページコンポーネントは `src/pages/`、カスタム Hook は `src/hooks/` に配置
- バックエンドとの通信は API クライアント経由のみ(直接 fetch を散在させない)
- 型は OpenAPI スキーマから `openapi-typescript` 等で自動生成する

### API 設計

- OpenAPI スキーマを `docs/api/` に置き、実装より先にスキーマを定義する
- レスポンス構造は言語非依存な JSON に保つ

## テスト / Lint

```bash
docker compose exec app php artisan test          # PHPUnit / Pest
docker compose exec frontend npm run lint         # ESLint
docker compose exec frontend npm run format       # Prettier
docker compose exec frontend npx tsc --noEmit     # 型チェック
```

## 将来のバックエンド差し替えを意識した制約

- Eloquent・Carbon・Laravel enum キャストなど、Laravel 固有の挙動を API レスポンスに含めない
- セッション・CSRF に依存した API 設計は避ける
- SPA 向け API 認証は JWT。セッション・CSRF に依存させない
- API スキーマは OpenAPI で言語非依存に定義する

## このテンプレートからサンプルを作るとき

1. プロジェクトをコピーして新しいディレクトリ名にする
2. アプリ名を置き換える
   - `backend/composer.json` の `name`, `description`
   - `backend/.env.example` の `APP_NAME`, `DB_DATABASE`, `DB_USERNAME`
   - `frontend/package.json` の `name`
   - `frontend/index.html` の `<title>`
   - `frontend/src/pages/HomePage.tsx` の表示名
   - `docker-compose.yml` の `MYSQL_DATABASE`, `MYSQL_USER`
3. `docs/` に新しいサンプルの設計書(画面仕様・API・ER 図など)を先に作成する
4. その後にモデル / マイグレーション / コントローラ / 画面を実装する

## 設計書

設計書は**必ず `docs/` 配下に置く**。新規機能の実装前に設計書を先に作成すること。

- [docs/README.md](docs/README.md) — フォルダ構成の説明
- [docs/architecture/docker.md](docs/architecture/docker.md) — Docker 構成・ポート・ボリューム・認証方式
