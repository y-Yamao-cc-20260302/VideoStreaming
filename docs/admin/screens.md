# 管理画面（Laravel Blade）設計

## 技術スタック

- Laravel Blade（サーバーサイドレンダリング）
- Tailwind CSS（または Bootstrap でも可、テンプレートに合わせる）
- 認証: Laravel セッション認証（Cookie + CSRF）
- ベース URL: `http://localhost/admin`

## ルーティング

| メソッド | パス | 認証 | 画面 |
|---|---|---|---|
| GET | `/admin/login` | 不要 | ログイン |
| POST | `/admin/login` | 不要 | ログイン処理 |
| POST | `/admin/logout` | 必須 | ログアウト |
| GET | `/admin` | 必須 | ダッシュボード |
| GET | `/admin/dashboard` | 必須 | ダッシュボード（別名） |
| GET | `/admin/videos` | 必須 | 動画一覧 |
| GET | `/admin/videos/create` | 必須 | 動画登録フォーム |
| POST | `/admin/videos` | 必須 | 動画登録処理 |
| GET | `/admin/videos/{id}/edit` | 必須 | 動画編集フォーム |
| PATCH | `/admin/videos/{id}` | 必須 | 動画更新処理 |
| DELETE | `/admin/videos/{id}` | 必須 | 動画削除 |
| PATCH | `/admin/videos/{id}/publish` | 必須 | 公開・非公開切替 |
| GET | `/admin/categories` | 必須 | カテゴリ一覧 |
| GET | `/admin/categories/create` | 必須 | カテゴリ登録 |
| POST | `/admin/categories` | 必須 | |
| GET | `/admin/categories/{id}/edit` | 必須 | |
| PATCH | `/admin/categories/{id}` | 必須 | |
| DELETE | `/admin/categories/{id}` | 必須 | |
| GET | `/admin/genres` | 必須 | ジャンル一覧（CRUD 一式） |
| GET | `/admin/subscription-plans` | 必須 | プラン一覧（CRUD 一式） |
| GET | `/admin/users` | 必須 | 会員一覧 |
| GET | `/admin/users/{id}` | 必須 | 会員詳細 |
| PATCH | `/admin/users/{id}/status` | 必須 | 会員停止 / 復帰 |
| GET | `/admin/reviews` | 必須 | レビュー一覧 |
| DELETE | `/admin/reviews/{id}` | 必須 | レビュー削除 |
| GET | `/admin/notices` | 必須 | お知らせ一覧（CRUD 一式） |

## レイアウト

```
┌──────────────────────────────────────────────┐
│ Topbar:  Video Streaming Admin   admin@... ▼ │
├────────────┬─────────────────────────────────┤
│            │                                  │
│ Sidebar    │   Main Content                   │
│ - ダッシュ  │                                  │
│ - 動画      │                                  │
│ - カテゴリ  │                                  │
│ - ジャンル  │                                  │
│ - プラン    │                                  │
│ - 会員      │                                  │
│ - レビュー  │                                  │
│ - お知らせ  │                                  │
│            │                                  │
└────────────┴─────────────────────────────────┘
```

## 画面別 仕様サマリ

### ダッシュボード (`/admin`)

| エリア | 内容 |
|---|---|
| KPI カード | 総会員数 / 有料会員数 / 当月再生数 / 当月新規登録 |
| 人気動画ランキング | 直近 7 日の再生回数 TOP10 |
| 最近のレビュー | 最新 10 件 |

### 動画一覧 (`/admin/videos`)

| 項目 | 内容 |
|---|---|
| フィルタ | カテゴリ / ジャンル / 公開状態 / キーワード |
| 表示列 | サムネ / タイトル / カテゴリ / 公開日 / 公開状態 / 操作 |
| 操作 | 編集 / 公開切替 / 削除 |
| ページネーション | 20 件 / ページ |

### 動画登録・編集

| 項目 | 入力 |
|---|---|
| タイトル | text 必須 |
| 説明 | textarea |
| サムネイル | ファイルアップロード（`storage/app/public/thumbnails/`）|
| ストリーミング URL | text 必須 |
| カテゴリ | select 必須 |
| ジャンル | multi-select |
| 尺（秒） | number |
| 公開日 | date |
| 公開状態 | checkbox |

### 会員一覧 (`/admin/users`)

| 項目 | 内容 |
|---|---|
| 検索 | メール / 氏名 |
| 表示列 | ID / メール / 氏名 / プラン / ステータス / 登録日 / 操作 |
| 操作 | 詳細 |

### 会員詳細 (`/admin/users/{id}`)

| エリア | 内容 |
|---|---|
| プロフィール | メール / 氏名 / ニックネーム / 登録日 |
| プラン情報 | 現プラン / 開始日 / 解約日 |
| 視聴履歴（直近 20 件） | 動画タイトル / 視聴日 / 進捗 |
| 操作 | 停止 / 復帰 |

### レビュー一覧 (`/admin/reviews`)

| 項目 | 内容 |
|---|---|
| 検索 | 動画 / ユーザー |
| 表示列 | 動画 / 投稿者 / ★ / コメント / 投稿日 / 操作 |
| 操作 | 削除（論理削除） |

## 認証

- `/admin/login` 以外は `AdminAuthenticate` ミドルウェアを通る
- ログイン失敗時はフラッシュメッセージで通知
- ログイン後の遷移先: `/admin/dashboard`
- すべての POST / PATCH / DELETE で CSRF トークン必須

## ディレクトリ構成（実装方針）

```
backend/
├── app/Http/
│   ├── Controllers/Admin/
│   │   ├── AdminAuthController.php
│   │   ├── DashboardController.php
│   │   ├── VideoController.php
│   │   ├── CategoryController.php
│   │   ├── GenreController.php
│   │   ├── SubscriptionPlanController.php
│   │   ├── UserController.php
│   │   ├── ReviewController.php
│   │   └── NoticeController.php
│   ├── Middleware/AdminAuthenticate.php
│   └── Requests/Admin/
│       ├── Video/{Store,Update}Request.php
│       └── ...（他リソース）
└── resources/views/admin/
    ├── layouts/
    │   ├── app.blade.php        # サイドバー付きレイアウト
    │   └── auth.blade.php       # ログイン専用
    ├── auth/login.blade.php
    ├── dashboard/index.blade.php
    ├── videos/(index|create|edit).blade.php
    ├── categories/(index|create|edit).blade.php
    ├── genres/(index|create|edit).blade.php
    ├── subscription-plans/(index|create|edit).blade.php
    ├── users/(index|show).blade.php
    ├── reviews/index.blade.php
    └── notices/(index|create|edit).blade.php
```
