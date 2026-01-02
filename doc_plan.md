# ayutenn ドキュメント刷新計画

## 概要

`docs/ayutenn/` を「プロジェクトの利用方法に特化した手順書」として刷新します。
モジュールの詳細仕様は `vendor/tyaunen/ayutenn-core/docs/` へのリンクで済ませることで、
ドキュメントの重複を避け、メンテナンス性を向上させます。

---

## 設計方針

### 1. 手順書としての位置づけ
- **目的**: 「このフレームワークで○○を実装するにはどうすればよいか」に答える
- **対象読者**: 初めてこのフレームワークを使う開発者
- **形式**: ステップバイステップの手順 + 実践例

### 2. 詳細仕様への誘導
- クラスのコンストラクタ、メソッド一覧、引数などの詳細仕様は ayutenn-core のドキュメントにリンク
- skeleton のドキュメントでは「何をするか」「どう使うか」に集中

### 3. 一貫した構造
各ドキュメントは以下の構造を持つ:
1. **概要** - このファイルで何を学べるか
2. **フレームワークリファレンス** - ayutenn-core のドキュメントへのリンク
3. **手順** - ステップバイステップの実装手順
4. **実践例** - 実際のコード例
5. **ベストプラクティス** - 推奨事項と注意点

---

## ドキュメント構成

### README.md
ドキュメント全体の目次と推奨読書順序

---

### intro.md (イントロダクション)
#### ayutennとは？
- 3つの構成要素(ayutenn-css, ayutenn-core, ayutenn-skeleton)

#### セットアップ手順
- git clone → composer install → config.json設定

#### プロジェクト構造
- ディレクトリ構成と各ディレクトリの役割

#### リクエスト処理の流れ
- index.php → Router → Controller/API/View の流れを図解

---

### routing.md (ルート定義)
#### フレームワークリファレンス
- → ayutenn-core/docs/routing.md

#### ルートの4種類
- view, controller, api, redirect それぞれの説明

#### ルート定義の手順
1. ルート定義ファイルの作成場所
2. Route の書き方
3. RouteGroup でグループ化

#### ミドルウェア
- ミドルウェアの作成手順
- 認証チェックの実装例

---

### view.md (ビュー)
#### フレームワークリファレンス
- → ayutenn-core/docs/utils.md (Redirect, CsrfTokenManager)
- → ayutenn-core/docs/session.md (FlashMessage)

#### ビューの構造
- 基本的なテンプレート構造

#### ビュー実装の手順
1. ルートの追加
2. ビューファイルの作成
3. 表示確認
4. インテグレーションテストの作成

#### 実践テクニック
- ayutenn-cssによるスタイリング
  - 人間: https://tyaunen.moo.jp/ayutenn/css/ 参照
  - AI: .agent/workflows/css ワークフロー参照
- componentディレクトリへの分割
- フラッシュメッセージの表示
- DataManagerを使ったデータの取得
- Form Remain(入力値の復元)
- axiosによるAPI呼び出し
- セキュリティ: h(), h2br(), ayutenn.util.escapeHtml()
- CSRFトークンの埋め込み

---

### controller.md (コントローラー)
#### フレームワークリファレンス
- → ayutenn-core/docs/requests.md

#### コントローラー実装の手順
1. ファイルの作成場所と命名規則
2. Controllerクラスの継承
3. main()メソッドの実装
4. ファイル末尾でインスタンスを返す
5. インテグレーションテストの作成（ayutenn-coreのRedirectのテスト機能を使用）

#### バリデーション
- $RequestParameterFormat の設定
- モデルファイルとの連携

#### Form Remain機能
- エラー時の入力値保存と復元

#### PRGパターン
- なぜリダイレクトが必須なのか
- redirect()の使い方

---

### api.md (API)
#### フレームワークリファレンス
- → ayutenn-core/docs/requests.md

#### API実装の手順
1. ファイルの作成場所と命名規則(〜Api.php)
2. Apiクラスの継承
3. main()メソッドの実装(配列を返す)
4. ファイル末尾でインスタンスを返す
5. インテグレーションテストの作成（ayutenn-coreのRedirectのテスト機能を使用）

#### レスポンス形式
- createResponse()の使い方
- 成功時/失敗時のJSON構造

#### バリデーション
- $RequestParameterFormat の設定
- エラー時の自動レスポンス

---

### model.md (バリデーションルール)
#### フレームワークリファレンス
- → ayutenn-core/docs/validation.md

#### モデルファイルの役割
- Controller/APIでのバリデーション自動化
- マイグレーションテーブル定義での利用

#### モデルファイル作成の手順
1. JSONファイルの作成場所
2. 基本プロパティ(name, type)
3. 型別のプロパティ
4. condition による形式制限
5. DBテーブル定義

#### 実践例
- user_id, password, email などの定義サンプル

---

### database.md (データベース操作)
#### フレームワークリファレンス
- → ayutenn-core/docs/database.md

#### DataManager実装の手順
1. DataManagerクラスの継承
2. CRUD操作メソッドの実装
3. QueryResultによる結果管理

#### 基本メソッド
- executeStatement() - INSERT/UPDATE/DELETE
- executeAndFetchAll() - SELECT

#### パラメータバインド
- プレースホルダとPDO型定数

#### Controller/APIでの使用
- DbConnector::connectWithPdo()
- Managerのインスタンス化と呼び出し

---

### migration.md (マイグレーション)
#### フレームワークリファレンス
- → ayutenn-core/docs/migration.md

#### マイグレーションの概要
- JSON定義からDDL SQL生成

#### 手順
1. /migrations/define/ にJSON定義
2. コマンドでSQL生成
3. SQLの実行

---

### testing.md (テスト)
#### テストの基本方針
- PHPUnit使用
- SQLiteインメモリDB使用

#### 手順
1. テストファイルの作成場所
2. データベーステストの実装
3. コントローラーテストの実装
4. テストの実行方法

---

### best-practices.md (ベストプラクティス)
よくある間違いと推奨される実装パターンのまとめ

---

## 今後の作業

1. [ ] README.md の更新
2. [ ] intro.md の整理
3. [ ] routing.md の整理
4. [ ] view.md の整理(手順書形式に再構成)
5. [ ] controller.md の整理
6. [ ] api.md の整理
7. [ ] model.md の整理
8. [ ] database.md の整理
9. [ ] migration.md の確認
10. [ ] testing.md の確認
11. [ ] best-practices.md の確認