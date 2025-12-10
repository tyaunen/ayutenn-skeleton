# ayutenn-skeleton フレームワーク ドキュメント

このディレクトリには、ayutennフレームワークの使用方法に関するドキュメントが格納されています。

## ドキュメント一覧

### [intro.md](intro.md)
ayutennフレームワークの概要、ディレクトリ構造、処理の流れについて説明しています。
初めてこのフレームワークを使用する方は、まずこのファイルをお読みください。

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

### [testing.md](testing.md)
ユニットテストの実装方法について説明しています。
- 基本方針(PHPUnit, SQLiteインメモリDB)
- データベーステストの実装
- コントローラーテストの実装
- トラブルシューティング

## 推奨読書順序

1. **[intro.md](intro.md)** - フレームワーク全体の理解
2. **[model.md](model.md)** - バリデーションルールの定義方法
3. **[database.md](database.md)** - データベース操作の実装
4. **[controller.md](controller.md)** - フォーム処理の実装
5. **[api.md](api.md)** - API開発
6. **[routing.md](routing.md)** - ルート定義とミドルウェア
7. **[testing.md](testing.md)** - ユニットテストの実装

## その他のリソース

より詳しい情報については、`/vendor/tyaunen/ayutenn-core/README.md`を参照してください。
