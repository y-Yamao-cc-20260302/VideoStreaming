# API エンドポイント詳細仕様

主要エンドポイントのリクエスト / レスポンス例。OpenAPI スキーマ確定までの暫定仕様。

## 1. 認証

### POST `/api/auth/register`

**リクエスト**
```json
{
  "email": "user@example.com",
  "password": "P@ssw0rd!",
  "name": "山田太郎",
  "nickname": "yamada"
}
```

**レスポンス 201**
```json
{
  "access_token": "eyJ0eXAiOiJKV1Qi...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "email": "user@example.com",
    "name": "山田太郎",
    "nickname": "yamada",
    "avatar_url": null,
    "status": "active"
  }
}
```

### POST `/api/auth/login`

**リクエスト**
```json
{ "email": "user@example.com", "password": "P@ssw0rd!" }
```

**レスポンス 200**: `register` と同形式

### GET `/api/auth/me`

**レスポンス 200**
```json
{
  "id": 1,
  "email": "user@example.com",
  "name": "山田太郎",
  "nickname": "yamada",
  "avatar_url": null,
  "status": "active",
  "subscription": {
    "plan_code": "standard",
    "plan_name": "スタンダード",
    "status": "active",
    "started_at": "2026-04-01T00:00:00Z",
    "ended_at": null
  }
}
```

---

## 2. 動画

### GET `/api/videos`

**クエリパラメータ**

| 名前 | 型 | 必須 | 説明 |
|---|---|---|---|
| `category` | string | × | カテゴリ slug |
| `genre` | string | × | ジャンル slug |
| `keyword` | string | × | タイトル / 説明文の全文検索 |
| `sort` | string | × | `new`(default) / `popular` / `release_date` |
| `page` | int | × | ページ番号（default 1） |
| `per_page` | int | × | 1〜100（default 20） |

**レスポンス 200**
```json
{
  "data": [
    {
      "id": 101,
      "title": "サンプル映画",
      "description": "あらすじ...",
      "thumbnail_url": "/storage/thumbnails/101.jpg",
      "duration_sec": 7200,
      "release_date": "2026-01-15",
      "category": { "id": 1, "name": "映画", "slug": "movie" },
      "genres": [
        { "id": 3, "name": "アクション", "slug": "action" }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 153,
    "last_page": 8
  }
}
```

### GET `/api/videos/{id}`

**レスポンス 200**
```json
{
  "id": 101,
  "title": "サンプル映画",
  "description": "あらすじ...",
  "thumbnail_url": "/storage/thumbnails/101.jpg",
  "stream_url": "https://cdn.example.com/hls/101/master.m3u8",
  "duration_sec": 7200,
  "release_date": "2026-01-15",
  "category": { "id": 1, "name": "映画", "slug": "movie" },
  "genres": [{ "id": 3, "name": "アクション", "slug": "action" }],
  "rating_avg": 4.2,
  "rating_count": 38,
  "is_favored": true,
  "progress_sec": 1234
}
```

### POST `/api/videos/{id}/progress`

**リクエスト**
```json
{ "progress_sec": 1234 }
```

**レスポンス 204**

---

## 3. マイリスト

### POST `/api/favorites`

**リクエスト**
```json
{ "video_id": 101 }
```

**レスポンス 201**
```json
{ "video_id": 101, "favored_at": "2026-05-18T03:14:15Z" }
```

### DELETE `/api/favorites/{video_id}`

**レスポンス 204**

---

## 4. レビュー

### GET `/api/videos/{id}/reviews`

**レスポンス 200**
```json
{
  "data": [
    {
      "id": 9001,
      "user": { "id": 1, "nickname": "yamada", "avatar_url": null },
      "rating": 5,
      "comment": "とても良かった",
      "created_at": "2026-05-10T12:00:00Z"
    }
  ],
  "meta": { "current_page": 1, "per_page": 20, "total": 38, "last_page": 2 }
}
```

### POST `/api/videos/{id}/reviews`

**リクエスト**
```json
{ "rating": 5, "comment": "とても良かった" }
```

**レスポンス 201**: 上記レビュー要素 1 件

---

## 5. サブスクリプション

### GET `/api/subscription-plans`

**レスポンス 200**
```json
{
  "data": [
    {
      "id": 1, "code": "free", "name": "無料", "price_jpy": 0,
      "description": "広告あり・新作除く"
    },
    {
      "id": 2, "code": "standard", "name": "スタンダード", "price_jpy": 980,
      "description": "全作品見放題・HD 画質"
    },
    {
      "id": 3, "code": "premium", "name": "プレミアム", "price_jpy": 1980,
      "description": "全作品見放題・4K 画質・同時 4 デバイス"
    }
  ]
}
```

### POST `/api/subscriptions`

**リクエスト**
```json
{ "plan_code": "standard" }
```

**レスポンス 201**
```json
{
  "plan_code": "standard",
  "plan_name": "スタンダード",
  "status": "active",
  "started_at": "2026-05-18T03:14:15Z",
  "ended_at": null
}
```

### DELETE `/api/subscriptions/current`

**レスポンス 200**: 解約日時を `ended_at` にセットしたサブスクリプション情報

---

## 6. お知らせ

### GET `/api/notices`

**レスポンス 200**
```json
{
  "data": [
    {
      "id": 1,
      "title": "メンテナンスのお知らせ",
      "body_excerpt": "5/20 0:00〜2:00 にメンテナンスを実施します...",
      "published_at": "2026-05-15T00:00:00Z"
    }
  ]
}
```
