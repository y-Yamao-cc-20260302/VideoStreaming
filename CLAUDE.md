# CLAUDE.md

## プロジェクト概要

動画配信サービスサイト (VOD)。Docker + Laravel (REST API + 管理画面 Blade) + React SPA + PostgreSQL の構成。

| レイヤー | 技術 | 備考 |
|---|---|---|
| インフラ | Docker / Docker Compose | |
| バックエンド | Laravel 12 (PHP 8.2) | REST API のみ。将来 Python / Java への差し替えを見据え、Inertia など Laravel 固有機構はフロントから前提にしない |
| データベース | PostgreSQL 16 | |
| フロント | React 18 + Vite + TypeScript | 独立プロジェクト。API 経由のみでバックエンドと通信 |
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
│   ├── app/Dockerfile      # PHP 8.2-FPM + Composer + pdo_pgsql
│   ├── frontend/Dockerfile # Node 20
│   └── nginx/              # Nginx リバースプロキシ
├── backend/                # Laravel アプリケーション
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/    # REST API（JWT）
│   │   │   ├── Controllers/Admin/  # 管理画面（セッション）
│   │   │   ├── Middleware/AdminAuthenticate.php
│   │   │   ├── Requests/Api/       # フロント側のFormRequest
│   │   │   ├── Requests/Admin/     # 管理画面側のFormRequest
│   │   │   └── Resources/          # APIレスポンス整形（言語非依存）
│   │   └── Models/                 # User, Admin, Video, Category, Genre, Review, ...
│   ├── database/
│   │   ├── migrations/             # users, admins, videos, ... 13テーブル
│   │   └── seeders/                # 管理者/マスタ/サンプル動画
│   ├── routes/
│   │   ├── api.php                 # REST API ルート
│   │   └── web.php                 # 管理画面ルート
│   └── resources/views/admin/      # 管理画面 Blade
├── frontend/                       # React SPA
│   └── src/
│       ├── api/                    # APIクライアント（axios）
│       ├── components/
│       │   ├── common/             # Pagination, Toast, LoadingSpinner
│       │   ├── layout/             # MainLayout, MyPageLayout
│       │   └── video/              # VideoCard, VideoGrid, VideoPlayer, VideoSection
│       ├── contexts/AuthContext.tsx
│       ├── pages/
│       │   ├── HomePage.tsx
│       │   ├── auth/(Login|Register)
│       │   ├── videos/(VideoList|VideoDetail|ReviewSection)
│       │   ├── search/
│       │   ├── my/(Profile|Password|Favorites|History|Subscription|PaymentHistories)
│       │   ├── notices/(NoticeList|NoticeDetail)
│       │   └── PlansPage.tsx
│       ├── router/                 # RequireAuth
│       ├── types/                  # ドメイン型
│       └── utils/format.ts
└── docs/                           # 設計書 → docs/README.md 参照
```

## 開発用コマンド

```bash
# コンテナ起動
docker compose up -d

# Composer
docker compose exec app composer <command>

# Artisan
docker compose exec app php artisan <command>

# マイグレーション
docker compose exec app php artisan migrate --seed

# npm
docker compose exec frontend npm run <command>

# TypeScript 型チェック
docker compose exec frontend npx tsc --noEmit
```

## コーディング規約

### バックエンド (Laravel)

- `routes/api.php` に REST API を集約。HTML を返すのは管理画面 Blade のみ
- API レスポンスは**言語非依存な JSON 構造**に保つ。他言語で再現できない Laravel 固有変換を API 境界に出さない
- Eloquent リレーション結果はそのまま返さず `Resource` クラスで整形する
- `Carbon` の独自フォーマット・enum キャストの挙動などが API 境界に漏れていないか常に確認する
- 日付は ISO 8601 (UTC) に統一
- セッション・CSRF に依存した API 設計は禁止
- API（SPA向け）の認証は JWT（`tymon/jwt-auth`）
- 管理画面は Laravel セッション認証（`/admin` ルート）

### フロント (React SPA)

- コンポーネント名は **PascalCase**
- ページコンポーネントは `src/pages/`、カスタム Hook は `src/hooks/` に配置
- バックエンドとの通信は `src/api/` 配下のクライアント経由のみ（直接 fetch / axios を散在させない）
- 型は OpenAPI スキーマから `openapi-typescript` 等で自動生成する想定

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

## 主要機能（実装済み）

| 区分 | 機能 |
|---|---|
| フロント認証 | 新規登録 / ログイン / プロフィール / パスワード変更 / 退会 |
| 動画 | 一覧（検索・フィルタ・ソート）/ 詳細 / 再生 / 視聴位置記録 / 新着 / 人気 / おすすめ |
| マイリスト | 追加・削除・一覧 |
| レビュー | 投稿・編集・削除・一覧 |
| サブスク | プラン一覧 / 加入・変更・解約 / 現プラン / 課金履歴 |
| お知らせ | 一覧・詳細 |
| 視聴履歴 | 一覧（再生位置付き） |
| 管理画面 | ダッシュボード / 動画 CRUD / カテゴリ・ジャンル・プラン マスタ / 会員一覧・詳細・停止 / レビューモデレーション / お知らせ CRUD |

詳細は `docs/features.md` を参照。

## 設計書

設計書は**必ず `docs/` 配下に置く**。新規機能の実装前に設計書を先に作成すること。

- [docs/README.md](docs/README.md) — フォルダ構成・目次
- [docs/features.md](docs/features.md) — 機能一覧
- [docs/architecture/overview.md](docs/architecture/overview.md) — システム全体構成
- [docs/architecture/docker.md](docs/architecture/docker.md) — Docker 構成・ポート・ボリューム・認証方式
- [docs/database/er.md](docs/database/er.md) / [tables.md](docs/database/tables.md) — DB 設計
- [docs/api/README.md](docs/api/README.md) / [endpoints.md](docs/api/endpoints.md) — REST API 仕様
- [docs/frontend/screens.md](docs/frontend/screens.md) — フロント画面設計
- [docs/admin/screens.md](docs/admin/screens.md) — 管理画面設計
