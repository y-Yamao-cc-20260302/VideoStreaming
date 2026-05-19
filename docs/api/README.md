# REST API 一覧

動画配信サービスのフロント SPA 向け REST API。すべて JSON で応答する。

## 共通仕様

- **ベース URL**: `http://localhost/api`
- **認証**: JWT（`Authorization: Bearer <token>`）
  - 公開エンドポイント（後述）は認証不要
- **Content-Type**: `application/json`
- **日付フォーマット**: ISO 8601 UTC（例: `2026-05-18T03:14:15Z`）
- **金額**: 整数 JPY（小数なし）
- **エラーレスポンス**:
  ```json
  {
    "message": "エラー概要",
    "errors": { "field": ["バリデーションメッセージ"] }
  }
  ```
- **HTTP ステータス**:
  - `200` 取得・更新成功
  - `201` 作成成功
  - `204` 削除成功
  - `400` リクエスト不正
  - `401` 未認証
  - `403` 認可エラー
  - `404` リソースなし
  - `422` バリデーションエラー
  - `500` サーバーエラー

## エンドポイント一覧

### 認証 / アカウント

| メソッド | パス | 認証 | 概要 |
|---|---|---|---|
| POST | `/api/auth/register` | 不要 | 新規会員登録 |
| POST | `/api/auth/login` | 不要 | ログイン（JWT 発行） |
| POST | `/api/auth/refresh` | 必要 | トークンリフレッシュ |
| POST | `/api/auth/logout` | 必要 | ログアウト |
| GET | `/api/auth/me` | 必要 | 自分のプロフィール取得 |
| PATCH | `/api/auth/me` | 必要 | プロフィール更新 |
| PATCH | `/api/auth/me/password` | 必要 | パスワード変更 |
| DELETE | `/api/auth/me` | 必要 | 退会 |

### 動画

| メソッド | パス | 認証 | 概要 |
|---|---|---|---|
| GET | `/api/videos` | 不要 | 動画一覧（公開済みのみ）。`?category=&genre=&keyword=&sort=&page=` |
| GET | `/api/videos/{id}` | 不要 | 動画詳細 |
| GET | `/api/videos/recommended` | 必要 | おすすめ動画（視聴履歴ベース） |
| GET | `/api/videos/new` | 不要 | 新着動画 |
| GET | `/api/videos/popular` | 不要 | 人気動画（直近 7 日） |
| POST | `/api/videos/{id}/progress` | 必要 | 視聴位置記録（再生中の進捗保存） |

### マスタ

| メソッド | パス | 認証 | 概要 |
|---|---|---|---|
| GET | `/api/categories` | 不要 | カテゴリ一覧 |
| GET | `/api/genres` | 不要 | ジャンル一覧 |

### 視聴履歴 / マイリスト

| メソッド | パス | 認証 | 概要 |
|---|---|---|---|
| GET | `/api/watch-histories` | 必要 | 自分の視聴履歴 |
| GET | `/api/favorites` | 必要 | 自分のマイリスト |
| POST | `/api/favorites` | 必要 | マイリストに追加（`video_id`） |
| DELETE | `/api/favorites/{video_id}` | 必要 | マイリストから削除 |

### レビュー

| メソッド | パス | 認証 | 概要 |
|---|---|---|---|
| GET | `/api/videos/{id}/reviews` | 不要 | 動画のレビュー一覧 |
| POST | `/api/videos/{id}/reviews` | 必要 | レビュー投稿 |
| PATCH | `/api/reviews/{id}` | 必要 | 自身のレビュー編集 |
| DELETE | `/api/reviews/{id}` | 必要 | 自身のレビュー削除 |

### サブスクリプション

| メソッド | パス | 認証 | 概要 |
|---|---|---|---|
| GET | `/api/subscription-plans` | 不要 | プラン一覧 |
| GET | `/api/subscriptions/current` | 必要 | 現在のサブスクリプション |
| POST | `/api/subscriptions` | 必要 | プラン加入・変更 |
| DELETE | `/api/subscriptions/current` | 必要 | 解約 |
| GET | `/api/payment-histories` | 必要 | 課金履歴 |

### お知らせ

| メソッド | パス | 認証 | 概要 |
|---|---|---|---|
| GET | `/api/notices` | 不要 | 掲載中のお知らせ一覧 |
| GET | `/api/notices/{id}` | 不要 | お知らせ詳細 |

## OpenAPI スキーマ

`docs/api/openapi.yaml`（今後作成）に正式な仕様を定義する。
フロントの型は `openapi-typescript` で自動生成する想定。

詳細なリクエスト / レスポンス例は [endpoints.md](endpoints.md) を参照。
