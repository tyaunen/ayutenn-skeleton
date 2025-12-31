# ayutenn-skeleton ドキュメント

このディレクトリには、skeletonプロジェクトの実装ガイドとベストプラクティスが格納されています。

## フレームワークリファレンス

Ayutennフレームワークのコア機能については、以下のドキュメントを参照してください：

| ドキュメント | 説明 |
|------------|------|
| [routing.md](../../vendor/tyaunen/ayutenn-core/docs/routing.md) | Route, RouteGroup, MiddlewareクラスのAPIリファレンス |
| [requests.md](../../vendor/tyaunen/ayutenn-core/docs/requests.md) | Controller, Api基底クラスのAPIリファレンス |
| [database.md](../../vendor/tyaunen/ayutenn-core/docs/database.md) | DataManager, DbConnector, QueryResultのAPIリファレンス |
| [validation.md](../../vendor/tyaunen/ayutenn-core/docs/validation.md) | バリデーションルールの仕様 |
| [session.md](../../vendor/tyaunen/ayutenn-core/docs/session.md) | FlashMessageのAPIリファレンス |
| [utils.md](../../vendor/tyaunen/ayutenn-core/docs/utils.md) | Logger, Redirect, CsrfTokenManager等のユーティリティ |
| [config.md](../../vendor/tyaunen/ayutenn-core/docs/config.md) | 設定管理クラスのAPIリファレンス |
| [migration.md](../../vendor/tyaunen/ayutenn-core/docs/migration.md) | マイグレーションツールの使用方法 |

---

## プロジェクトガイド

このプロジェクトでの実装方法・実践例については、以下のドキュメントを参照してください：

### [intro.md](intro.md)
ayutennフレームワークの概要、ディレクトリ構造、処理の流れについて説明しています。
**初めてこのフレームワークを使用する方は、まずこのファイルをお読みください。**

### [controller.md](controller.md)
Controllerファイルの作成方法と使用方法について説明しています。
- 命名規則と格納場所
- 必須メソッドの実装
- リクエストパラメータのバリデーション
- Form Remain機能
- 実行フローの詳細

### [api.md](api.md)
APIファイルの作成方法と使用方法について説明しています。
- 命名規則と格納場所
- JSON形式のレスポンス
- リクエストパラメータのバリデーション
- エラーハンドリング

### [model.md](model.md)
モデルファイル(バリデーションルール定義)の作成方法について説明しています。
- JSON形式での型定義
- 文字列型・数値型のプロパティ
- 条件(condition)による形式制限
- 実践的な使用例
- ControllerやAPIでの使用方法

### [database.md](database.md)
DataManager(データベース操作)の作成方法について説明しています。
- DataManagerの継承と基本メソッド
- executeStatement()とexecuteAndFetchAll()の使い方
- QueryResultによる結果管理
- CRUD操作の実践例
- ControllerやAPIでの使用方法

### [routing.md](routing.md)
ルート定義の作成方法について説明しています。
- 4種類のルートアクション(view, controller, api, redirect)
- RouteとRouteGroupの使い方
- ミドルウェアによる認証・認可
- HTTPメソッドとパスの定義
- 実践的なルート定義例

### [view.md](view.md)
ビューファイルの作成方法について説明しています。
- 基本的な構造
- データの取得とリダイレクト
- アラートメッセージの表示
- フォームとCSRFトークン
- コンポーネントの利用

### [testing.md](testing.md)
ユニットテストの実装方法について説明しています。
- 基本方針(PHPUnit, SQLiteインメモリDB)
- データベーステストの実装
- コントローラーテストの実装
- トラブルシューティング

### [best-practices.md](best-practices.md)
よくある間違いと推奨される実装方法について説明しています。

---

## 推奨読書順序

1. **[intro.md](intro.md)** - フレームワーク全体の理解
2. **[model.md](model.md)** - バリデーションルールの定義方法
3. **[database.md](database.md)** - データベース操作の実装
4. **[controller.md](controller.md)** - フォーム処理の実装
5. **[api.md](api.md)** - API開発
6. **[routing.md](routing.md)** - ルート定義とミドルウェア
7. **[view.md](view.md)** - ビューファイルの作成
8. **[testing.md](testing.md)** - ユニットテストの実装
