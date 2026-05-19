# docs/

動画配信サービスサイトの設計書置き場。新規機能の実装前に、対応する設計書をここに先に作成してから着手すること。

## ドキュメント目次

| ファイル | 概要 |
|---|---|
| [features.md](features.md) | 機能一覧（フロント / 管理画面） |
| [architecture/overview.md](architecture/overview.md) | システム全体構成・技術スタック・境界設計 |
| [architecture/docker.md](architecture/docker.md) | Docker / コンテナ構成・ポート・ボリューム・認証方式 |
| [database/er.md](database/er.md) | ER 図 |
| [database/tables.md](database/tables.md) | テーブル定義 |
| [api/README.md](api/README.md) | REST API エンドポイント一覧 |
| [api/endpoints.md](api/endpoints.md) | エンドポイント詳細仕様 |
| [frontend/screens.md](frontend/screens.md) | フロント SPA 画面一覧・ルーティング |
| [admin/screens.md](admin/screens.md) | 管理画面（Blade）画面一覧・ルーティング |

## サブディレクトリの使い分け

| ディレクトリ | 用途 |
|---|---|
| `architecture/` | システム全体・Docker 構成・**バックエンド差し替えを意識した境界設計** |
| `frontend/` | React SPA の画面設計・コンポーネント設計 |
| `admin/` | Blade 管理画面の設計 |
| `api/` | **OpenAPI スキーマ（言語非依存）とエンドポイント仕様** |
| `database/` | ER 図・マイグレーション方針・テーブル定義 |
