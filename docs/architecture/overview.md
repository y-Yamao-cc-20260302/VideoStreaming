# システム全体構成

## サービス概要

動画配信サービスサイト（VOD）のサンプル実装。
一般ユーザー向けの SPA と運営向けの管理画面を、共通のバックエンドから提供する。

## レイヤー構成

| レイヤー | 技術 | 備考 |
|---|---|---|
| インフラ | Docker / Docker Compose | ローカル開発専用 |
| バックエンド | Laravel (PHP 8.2) | REST API + Blade（管理画面） |
| データベース | PostgreSQL 16 | |
| フロント（一般ユーザー） | React + Vite + TypeScript | SPA。API 経由のみで通信 |
| 管理画面 | Laravel Blade | サーバーサイドレンダリング |
| API 仕様 | OpenAPI | `docs/api/` に配置・言語非依存 |
| 認証（管理画面） | Laravel セッション認証 + CSRF | Blade 専用 |
| 認証（SPA） | JWT（`tymon/jwt-auth`） | `Authorization: Bearer <token>` |

## システム境界

```
┌────────────────────┐          ┌───────────────────────┐
│  一般ユーザー        │          │  運営者                │
│  ブラウザ (React)   │          │  ブラウザ (Blade)      │
└──────────┬─────────┘          └──────────┬────────────┘
           │ JSON over HTTPS                │ HTML + CSRF
           │ Authorization: Bearer JWT      │ Cookie session
           │                                │
           ▼                                ▼
   ┌────────────────────────────────────────────────┐
   │              Laravel アプリケーション            │
   │   ┌─────────────────┐  ┌────────────────────┐  │
   │   │ /api (REST)     │  │ /admin (Blade)     │  │
   │   │ JWT 認証         │  │ セッション認証       │  │
   │   │ JSON 言語非依存   │  │ HTML レスポンス      │  │
   │   └────────┬────────┘  └─────────┬──────────┘  │
   │            └───────────┬─────────┘             │
   │                ┌───────▼──────┐                │
   │                │ ドメインロジック │                │
   │                │ Models / UseCase│              │
   │                └───────┬───────┘                │
   └────────────────────────┼───────────────────────┘
                            │
                  ┌─────────▼──────────┐
                  │  PostgreSQL 16      │
                  └────────────────────┘
```

## バックエンド差し替えを意識した境界設計

将来 Python / Java など他言語への差し替えを想定し、以下を守る。

- **REST API のレスポンス構造は言語非依存な JSON に保つ**
  - Eloquent のリレーション結果をそのまま返さない（必ず `Resource` クラスで整形）
  - `Carbon` の独自フォーマット / Laravel enum キャストを API 境界に出さない
  - 日付は ISO 8601（UTC）で統一
- **SPA からの認証はセッション・CSRF に依存させない（JWT のみ）**
- **OpenAPI スキーマを実装より先に定義する**
- **管理画面（Blade）はバックエンド差し替え対象外**。Laravel 固有の仕組み（Inertia / Blade テンプレートを API レスポンスで返す等）はフロント側で使わない

## ディレクトリ責務

| パス | 責務 |
|---|---|
| `backend/app/Http/Controllers/Api/` | REST API。JWT 認証。JSON のみ返す |
| `backend/app/Http/Controllers/Admin/` | 管理画面。セッション認証。HTML（Blade）を返す |
| `backend/app/Http/Resources/` | API レスポンス整形（言語非依存形式に変換） |
| `backend/app/Models/` | Eloquent モデル |
| `backend/resources/views/admin/` | 管理画面 Blade テンプレート |
| `frontend/src/api/` | API クライアント（fetch ラッパー） |
| `frontend/src/pages/` | ページコンポーネント |
| `frontend/src/components/` | 再利用コンポーネント |
| `docs/api/` | OpenAPI スキーマ・エンドポイント仕様 |

## URL マッピング

| URL | 種別 | 認証 |
|---|---|---|
| `http://localhost/` | React SPA | JWT（必要画面のみ） |
| `http://localhost/api/*` | REST API | JWT（公開エンドポイント除く） |
| `http://localhost/admin` | 管理画面ログイン | なし |
| `http://localhost/admin/*` | 管理画面 | Laravel セッション |
