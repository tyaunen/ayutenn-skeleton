# ayutenn ドキュメント

このディレクトリには、ayutennフレームワークの**実装ガイド**と**ベストプラクティス**が格納されています。

> [!TIP]
> クラスやメソッドの詳細仕様は、[ayutenn-core/docs/](../../vendor/tyaunen/ayutenn-core/docs/) を参照してください。

---

## ayutennとは？

ayutennは、PHPで作成するWebアプリケーションを構築するためのフレームワークです。

| コンポーネント | 説明 |
|--------------|------|
| **ayutenn-css** | CSS/JSライブラリ。簡素な装飾と基本的なUIコンポーネント |
| **ayutenn-core** | フレームワークのコア。ルーティング、バリデーション、DB操作など |
| **ayutenn-skeleton** | プロジェクトテンプレート。ディレクトリ構造とサンプルファイル |

---

## ドキュメント一覧

### [intro.md](intro.md) - はじめに
フレームワークの概要、セットアップ、ディレクトリ構造、リクエスト処理の流れ。
**初めての方はここから読んでください。**

### [routing.md](routing.md) - ルート定義
URLパターンと処理のマッピング。4種類のルートアクション、RouteGroup、ミドルウェア。

### [view.md](view.md) - ビュー
HTMLテンプレートの作成。データ取得、フラッシュメッセージ、フォーム、CSRFトークン。

### [controller.md](controller.md) - コントローラー
フォーム処理の実装。バリデーション、Form Remain、PRGパターン。

### [api.md](api.md) - API
JSONレスポンスを返すAPIの実装。バリデーション、エラーハンドリング。

### [model.md](model.md) - バリデーションルール
JSONファイルでの型・形式定義。Controller/APIでの自動バリデーション、DB定義での再利用。

### [database.md](database.md) - データベース操作
DataManagerの実装。CRUD操作、QueryResult、パラメータバインド。

### [migration.md](migration.md) - マイグレーション
JSON定義からDDL SQLを生成。テーブル定義の管理。

### [testing.md](testing.md) - テスト
PHPUnitによるユニットテスト、インテグレーションテストの実装。

### [best-practices.md](best-practices.md) - ベストプラクティス
よくある間違いと推奨される実装パターン。

---

## 推奨読書順序

```
1. intro.md          ← フレームワーク全体の理解
2. routing.md        ← URLと処理のマッピング
3. model.md          ← バリデーションルールの定義
4. database.md       ← データベース操作
5. controller.md     ← フォーム処理
6. api.md            ← API開発
7. view.md           ← ビュー作成
8. testing.md        ← テスト実装
```

---

## クイックリファレンス

| やりたいこと | 参照先 |
|------------|-------|
| プロジェクトのセットアップ | [intro.md](intro.md) |
| 新しいページを追加したい | [routing.md](routing.md) → [view.md](view.md) |
| フォームを作りたい | [model.md](model.md) → [controller.md](controller.md) |
| APIを作りたい | [model.md](model.md) → [api.md](api.md) |
| DBテーブルを作りたい | [migration.md](migration.md) |
| テストを書きたい | [testing.md](testing.md) |
