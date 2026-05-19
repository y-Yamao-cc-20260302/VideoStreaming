# ER 図

```mermaid
erDiagram
    users ||--o{ subscriptions : "has"
    users ||--o{ watch_histories : "watches"
    users ||--o{ favorites : "favors"
    users ||--o{ reviews : "writes"
    users ||--o{ payment_histories : "pays"

    admins {
        bigint id PK
        string name
        string email UK
        string password
        timestamps
    }

    users {
        bigint id PK
        string email UK
        string password
        string name
        string nickname
        string avatar_path
        enum status "active|suspended|withdrawn"
        timestamps
    }

    categories ||--o{ videos : "categorizes"
    categories {
        bigint id PK
        string name UK
        string slug UK
        int sort_order
        timestamps
    }

    genres }o--o{ videos : "via video_genre"
    genres {
        bigint id PK
        string name UK
        string slug UK
        timestamps
    }

    videos {
        bigint id PK
        bigint category_id FK
        string title
        text description
        string thumbnail_path
        string stream_url
        int duration_sec
        date release_date
        boolean is_published
        timestamps
        softDeletes
    }

    video_genre {
        bigint video_id FK
        bigint genre_id FK
    }

    videos ||--o{ watch_histories : "watched_in"
    videos ||--o{ favorites : "favored_in"
    videos ||--o{ reviews : "reviewed_in"

    watch_histories {
        bigint id PK
        bigint user_id FK
        bigint video_id FK
        int progress_sec
        timestamp watched_at
        timestamps
    }

    favorites {
        bigint id PK
        bigint user_id FK
        bigint video_id FK
        timestamps
    }

    reviews {
        bigint id PK
        bigint user_id FK
        bigint video_id FK
        smallint rating "1-5"
        text comment
        timestamps
        softDeletes
    }

    subscription_plans ||--o{ subscriptions : "subscribed_to"
    subscription_plans ||--o{ payment_histories : "billed_as"
    subscription_plans {
        bigint id PK
        string name
        string code UK
        int price_jpy
        text description
        boolean is_active
        timestamps
    }

    subscriptions {
        bigint id PK
        bigint user_id FK
        bigint subscription_plan_id FK
        timestamp started_at
        timestamp ended_at
        enum status "active|canceled|expired"
        timestamps
    }

    payment_histories {
        bigint id PK
        bigint user_id FK
        bigint subscription_plan_id FK
        int amount_jpy
        timestamp paid_at
        timestamps
    }

    notices {
        bigint id PK
        string title
        text body
        timestamp published_at
        timestamp expired_at
        timestamps
    }
```

## 主要リレーション

| 親 | 子 | 関係 | 削除時 |
|---|---|---|---|
| users | watch_histories | 1:N | CASCADE |
| users | favorites | 1:N | CASCADE |
| users | reviews | 1:N | CASCADE |
| users | subscriptions | 1:N | CASCADE |
| users | payment_histories | 1:N | RESTRICT |
| categories | videos | 1:N | RESTRICT |
| genres ↔ videos | video_genre | N:M | CASCADE |
| videos | watch_histories | 1:N | CASCADE |
| videos | favorites | 1:N | CASCADE |
| videos | reviews | 1:N | CASCADE |
| subscription_plans | subscriptions | 1:N | RESTRICT |

## インデックス方針（抜粋）

| テーブル | カラム | 種類 | 理由 |
|---|---|---|---|
| videos | (is_published, release_date) | 複合 | 公開動画の新着順表示 |
| videos | category_id | 単一 | カテゴリ別一覧 |
| watch_histories | (user_id, watched_at desc) | 複合 | 視聴履歴の新しい順 |
| watch_histories | (user_id, video_id) | UNIQUE | 同一動画は1レコード（再生位置を上書き） |
| favorites | (user_id, video_id) | UNIQUE | 重複登録防止 |
| reviews | video_id | 単一 | 動画ごとのレビュー集計 |
| subscriptions | (user_id, status) | 複合 | 現在のアクティブプラン取得 |
