# フロント（React SPA）画面設計

## 技術スタック

- React 18 + TypeScript
- Vite
- React Router v6
- Tailwind CSS
- 認証: JWT（`localStorage` 保存）

## ルーティング

| パス | 画面 | 認証 | 説明 |
|---|---|---|---|
| `/` | ホーム | 任意 | 新着 / 人気 / おすすめ動画を縦並びで表示 |
| `/login` | ログイン | 不要 | ログイン済みは `/` へリダイレクト |
| `/register` | 新規会員登録 | 不要 | |
| `/videos` | 動画一覧 | 任意 | カテゴリ・ジャンル・キーワード絞り込み |
| `/videos/:id` | 動画詳細 | 任意 | プレーヤー / あらすじ / レビュー |
| `/categories/:slug` | カテゴリ別一覧 | 任意 | |
| `/genres/:slug` | ジャンル別一覧 | 任意 | |
| `/search?q=...` | 検索結果 | 任意 | |
| `/my/favorites` | マイリスト | 必須 | |
| `/my/history` | 視聴履歴 | 必須 | |
| `/my/profile` | プロフィール編集 | 必須 | |
| `/my/password` | パスワード変更 | 必須 | |
| `/my/subscription` | プラン管理 | 必須 | 現プラン / 変更 / 解約 |
| `/my/payment-histories` | 課金履歴 | 必須 | |
| `/plans` | プラン一覧 | 任意 | 未ログインでも閲覧可 |
| `/notices` | お知らせ一覧 | 任意 | |
| `/notices/:id` | お知らせ詳細 | 任意 | |

> 「認証 任意」= 認証していなくても閲覧できるが、ログイン状態に応じて表示が変わる（マイリスト追加ボタン等）。

## レイアウト

```
┌────────────────────────────────────────────┐
│ Header                                      │
│  ┌──────┐ ┌─────────┐         ┌────────┐  │
│  │ Logo │ │ Search   │         │ User ▼ │  │
│  └──────┘ └─────────┘         └────────┘  │
├────────────────────────────────────────────┤
│ Nav: ホーム / カテゴリ▼ / ジャンル▼ / プラン  │
├────────────────────────────────────────────┤
│                                            │
│              Main Content                  │
│                                            │
├────────────────────────────────────────────┤
│ Footer                                      │
└────────────────────────────────────────────┘
```

## 画面別 仕様サマリ

### ホーム (`/`)

- セクション: 「新着」「人気（直近 7 日）」「あなたへのおすすめ（ログイン時のみ）」「カテゴリ別ピックアップ」
- 各セクションは横スクロール可能なカルーセル

### 動画詳細 (`/videos/:id`)

- 上部: プレーヤー（HLS.js でストリーミング再生）
  - ログイン時は視聴位置を 10 秒おきに `POST /api/videos/{id}/progress` で記録
- 中段: タイトル / カテゴリ / ジャンル / 公開日 / 評価
- アクション: マイリスト追加 ★ / 評価する
- 下段: あらすじ / レビュー一覧 / 関連動画

### 動画一覧 (`/videos`)

- 左サイドにフィルタ（カテゴリ / ジャンル / ソート）
- メインにグリッド表示
- 下部にページネーション

### マイページ（`/my/*`）

- 左サイドメニュー: プロフィール / パスワード / マイリスト / 視聴履歴 / プラン / 課金履歴

### プレーヤー画面

| 要素 | 仕様 |
|---|---|
| 動画ソース | `stream_url` を HLS.js で再生 |
| 再開位置 | 動画詳細 API の `progress_sec` を初期位置に |
| 進捗送信 | 10 秒間隔 + 一時停止時 + 再生終了時 |

## ディレクトリ構成（実装方針）

```
frontend/src/
├── api/
│   ├── client.ts          # fetch ラッパー（JWT ヘッダー付与）
│   ├── auth.ts
│   ├── videos.ts
│   ├── favorites.ts
│   ├── reviews.ts
│   ├── subscriptions.ts
│   └── notices.ts
├── components/
│   ├── common/            # Header, Footer, LoadingSpinner, Pagination, Toast
│   ├── video/             # VideoCard, VideoGrid, VideoCarousel, VideoPlayer
│   ├── review/            # ReviewList, ReviewForm, StarRating
│   └── layout/            # MainLayout, MyPageLayout
├── contexts/
│   └── AuthContext.tsx
├── hooks/
│   ├── useToast.ts
│   ├── useVideos.ts
│   └── usePlayerProgress.ts
├── pages/
│   ├── HomePage.tsx
│   ├── auth/(Login|Register).tsx
│   ├── videos/(List|Detail).tsx
│   ├── search/SearchPage.tsx
│   ├── plans/PlansPage.tsx
│   ├── notices/(List|Detail).tsx
│   └── my/(Profile|Password|Favorites|History|Subscription|PaymentHistories).tsx
├── router/
│   └── RequireAuth.tsx
├── types/
│   ├── api.ts             # OpenAPI から自動生成
│   └── domain.ts          # 画面用の派生型
└── utils/
    └── formatDuration.ts
```

## 認証フロー

1. ログイン成功時に `access_token` を `localStorage` に保存
2. `client.ts` がすべてのリクエストに `Authorization: Bearer` を付与
3. 401 レスポンスを受けたら `/api/auth/refresh` を試行 → 失敗で `/login` へ
4. `AuthContext` がトークン保有状態をアプリ全体に提供
