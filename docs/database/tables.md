# テーブル定義

PostgreSQL 16 を前提とした論理設計。日時カラムはすべて `TIMESTAMP WITH TIME ZONE`（UTC 保存）。

## admins（管理者）

| カラム | 型 | 制約 | 説明 |
|---|---|---|---|
| id | BIGSERIAL | PK | |
| name | VARCHAR(100) | NOT NULL | 管理者名 |
| email | VARCHAR(255) | NOT NULL, UNIQUE | ログイン ID |
| password | VARCHAR(255) | NOT NULL | bcrypt ハッシュ |
| created_at | TIMESTAMPTZ | NOT NULL | |
| updated_at | TIMESTAMPTZ | NOT NULL | |

## users（一般会員）

| カラム | 型 | 制約 | 説明 |
|---|---|---|---|
| id | BIGSERIAL | PK | |
| email | VARCHAR(255) | NOT NULL, UNIQUE | |
| password | VARCHAR(255) | NOT NULL | bcrypt ハッシュ |
| name | VARCHAR(100) | NOT NULL | 氏名 |
| nickname | VARCHAR(50) | NULL | 表示名 |
| avatar_path | VARCHAR(255) | NULL | アバター画像パス |
| status | VARCHAR(20) | NOT NULL DEFAULT 'active' | `active` / `suspended` / `withdrawn` |
| created_at | TIMESTAMPTZ | NOT NULL | |
| updated_at | TIMESTAMPTZ | NOT NULL | |

## categories（カテゴリ）

| カラム | 型 | 制約 | 説明 |
|---|---|---|---|
| id | BIGSERIAL | PK | |
| name | VARCHAR(50) | NOT NULL, UNIQUE | 例: 映画、アニメ、ドラマ |
| slug | VARCHAR(50) | NOT NULL, UNIQUE | URL 用 |
| sort_order | INT | NOT NULL DEFAULT 0 | 表示順 |
| created_at | TIMESTAMPTZ | NOT NULL | |
| updated_at | TIMESTAMPTZ | NOT NULL | |

## genres（ジャンル）

| カラム | 型 | 制約 | 説明 |
|---|---|---|---|
| id | BIGSERIAL | PK | |
| name | VARCHAR(50) | NOT NULL, UNIQUE | 例: アクション、コメディ |
| slug | VARCHAR(50) | NOT NULL, UNIQUE | |
| created_at | TIMESTAMPTZ | NOT NULL | |
| updated_at | TIMESTAMPTZ | NOT NULL | |

## videos（動画）

| カラム | 型 | 制約 | 説明 |
|---|---|---|---|
| id | BIGSERIAL | PK | |
| category_id | BIGINT | NOT NULL, FK→categories.id | |
| title | VARCHAR(255) | NOT NULL | |
| description | TEXT | NULL | あらすじ |
| thumbnail_path | VARCHAR(255) | NULL | サムネイル画像パス |
| stream_url | VARCHAR(500) | NOT NULL | 動画ストリーミング URL（HLS 等） |
| duration_sec | INT | NOT NULL DEFAULT 0 | 尺（秒） |
| release_date | DATE | NULL | 公開日 |
| is_published | BOOLEAN | NOT NULL DEFAULT false | 公開フラグ |
| created_at | TIMESTAMPTZ | NOT NULL | |
| updated_at | TIMESTAMPTZ | NOT NULL | |
| deleted_at | TIMESTAMPTZ | NULL | 論理削除 |

## video_genre（動画⇔ジャンル中間）

| カラム | 型 | 制約 |
|---|---|---|
| video_id | BIGINT | NOT NULL, FK→videos.id, ON DELETE CASCADE |
| genre_id | BIGINT | NOT NULL, FK→genres.id, ON DELETE CASCADE |
| PRIMARY KEY | (video_id, genre_id) | |

## watch_histories（視聴履歴）

| カラム | 型 | 制約 | 説明 |
|---|---|---|---|
| id | BIGSERIAL | PK | |
| user_id | BIGINT | NOT NULL, FK→users.id | |
| video_id | BIGINT | NOT NULL, FK→videos.id | |
| progress_sec | INT | NOT NULL DEFAULT 0 | 再生位置（秒） |
| watched_at | TIMESTAMPTZ | NOT NULL | 最終視聴日時 |
| created_at | TIMESTAMPTZ | NOT NULL | |
| updated_at | TIMESTAMPTZ | NOT NULL | |
| UNIQUE | (user_id, video_id) | | 同一動画は 1 レコード |

## favorites（マイリスト）

| カラム | 型 | 制約 |
|---|---|---|
| id | BIGSERIAL | PK |
| user_id | BIGINT | NOT NULL, FK→users.id |
| video_id | BIGINT | NOT NULL, FK→videos.id |
| created_at | TIMESTAMPTZ | NOT NULL |
| updated_at | TIMESTAMPTZ | NOT NULL |
| UNIQUE | (user_id, video_id) | |

## reviews（レビュー）

| カラム | 型 | 制約 | 説明 |
|---|---|---|---|
| id | BIGSERIAL | PK | |
| user_id | BIGINT | NOT NULL, FK→users.id | |
| video_id | BIGINT | NOT NULL, FK→videos.id | |
| rating | SMALLINT | NOT NULL, CHECK (1..5) | |
| comment | TEXT | NULL | |
| created_at | TIMESTAMPTZ | NOT NULL | |
| updated_at | TIMESTAMPTZ | NOT NULL | |
| deleted_at | TIMESTAMPTZ | NULL | 論理削除（モデレーション） |

## subscription_plans（プラン）

| カラム | 型 | 制約 | 説明 |
|---|---|---|---|
| id | BIGSERIAL | PK | |
| name | VARCHAR(100) | NOT NULL | 例: スタンダード |
| code | VARCHAR(50) | NOT NULL, UNIQUE | 例: `standard` |
| price_jpy | INT | NOT NULL | 月額（円） |
| description | TEXT | NULL | プラン特典 |
| is_active | BOOLEAN | NOT NULL DEFAULT true | 提供中フラグ |
| created_at | TIMESTAMPTZ | NOT NULL | |
| updated_at | TIMESTAMPTZ | NOT NULL | |

## subscriptions（加入状態）

| カラム | 型 | 制約 | 説明 |
|---|---|---|---|
| id | BIGSERIAL | PK | |
| user_id | BIGINT | NOT NULL, FK→users.id | |
| subscription_plan_id | BIGINT | NOT NULL, FK→subscription_plans.id | |
| started_at | TIMESTAMPTZ | NOT NULL | |
| ended_at | TIMESTAMPTZ | NULL | 解約予定 / 終了日時 |
| status | VARCHAR(20) | NOT NULL | `active` / `canceled` / `expired` |
| created_at | TIMESTAMPTZ | NOT NULL | |
| updated_at | TIMESTAMPTZ | NOT NULL | |

## payment_histories（課金履歴）

| カラム | 型 | 制約 | 説明 |
|---|---|---|---|
| id | BIGSERIAL | PK | |
| user_id | BIGINT | NOT NULL, FK→users.id | |
| subscription_plan_id | BIGINT | NOT NULL, FK→subscription_plans.id | |
| amount_jpy | INT | NOT NULL | |
| paid_at | TIMESTAMPTZ | NOT NULL | |
| created_at | TIMESTAMPTZ | NOT NULL | |
| updated_at | TIMESTAMPTZ | NOT NULL | |

## notices（お知らせ）

| カラム | 型 | 制約 | 説明 |
|---|---|---|---|
| id | BIGSERIAL | PK | |
| title | VARCHAR(255) | NOT NULL | |
| body | TEXT | NOT NULL | |
| published_at | TIMESTAMPTZ | NOT NULL | 公開開始 |
| expired_at | TIMESTAMPTZ | NULL | 掲載終了（NULL なら無期限） |
| created_at | TIMESTAMPTZ | NOT NULL | |
| updated_at | TIMESTAMPTZ | NOT NULL | |

## マイグレーション順序

1. admins
2. users
3. categories
4. genres
5. subscription_plans
6. videos
7. video_genre
8. watch_histories
9. favorites
10. reviews
11. subscriptions
12. payment_histories
13. notices
