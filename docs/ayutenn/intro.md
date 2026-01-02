# ayutenn フレームワーク 概要

このドキュメントでは、ayutennフレームワークのセットアップとプロジェクト構造について説明します。

## フレームワークリファレンス

設定やパス管理の詳細仕様については、ayutenn-coreのドキュメントを参照してください：

- **[config.md](../../vendor/tyaunen/ayutenn-core/docs/config.md)** - Configクラスの詳細仕様
- **[framework_paths.md](../../vendor/tyaunen/ayutenn-core/docs/framework_paths.md)** - FrameworkPathsクラスの詳細仕様

---

## セットアップ
```bash
git clone https://github.com/tyaunen/ayutenn-skeleton.git MyProject
cd MyProject
composer install
```

環境設定ファイル、`config/config.json` が生成されます。
環境に合わせて設定値を変更してください。

```json
{
    "DEBUG_MODE": true,
    "PDO_DSN": "mysql:host=localhost;dbname=YOUR_DATABASE_NAME;charset=utf8mb4",
    "PDO_USERNAME": "YOUR_DB_USERNAME",
    "PDO_PASSWORD": "YOUR_DB_PASSWORD",
    ...
}
```

`config/app.json`は、アプリ設定ファイルです。

ディレクトリパスの設定を変更することで自由にディレクトリ構造を変えることができますが、
ドキュメントやAI向けワークフローはデフォルトの値を基準に記載しているので、そのまま使用することを推奨します。

```json
{
    // 必須
    "APP_DIR": "/app",
    "ROUTE_DIR": "/app/routes",
    "CONTROLLER_DIR": "/app/controller",
    "VIEW_DIR": "/app/views",
    "API_DIR": "/app/api",
    "MODEL_DIR": "/app/model",
    "PUBLIC_DIR": "/app/public",
    "LOG_DIR": "/storage/logs",
    "SESSION_DIR": "/storage/session",

    "404_PAGE_FILE": "/system/404", // 404ページのビューファイル /system/404.php

    // 任意
    "APP_TITLE": "アプリ名", // サンプルファイルが<title>タグで使用しています
}
```

`config.json`と`app.json`ファイルの設定はayutenn/core/config/Configクラスから取得できます。
プロジェクトに定数設定を行いたい場合、環境に依存するものなら`config.json`に、環境に依存しないものなら`app.json`に設定してください。

## ディレクトリ構造

```
/skeleton
├── /.agent               - AIエージェント向けワークフローファイル
├── /app
│   ├── /api              - API処理(JSON形式でレスポンスを返す)
│   ├── /controller       - コントローラー(フォーム処理後にリダイレクト)
│   ├── /database         - データベース操作クラス
│   ├── /helper           - 再利用可能なヘルパークラス・関数
│   ├── /model            - バリデーションルール定義(JSONファイル)
│   ├── /public           - エントリポイント(index.php)とアセット(css, js, img, etc...)
│   ├── /routes           - ルート定義ファイル
│   │   └── /middleware   - ミドルウェア(ログインチェックなど)
│   └── /views            - ビューファイル(HTML)
│       ├── /components   - 再利用可能なHTMLコンポーネント
│       └── /system       - デフォルトのシステムページ(404など)
├── /config
│   ├── config.json       - 環境固有の設定(DB接続情報など)
│   └── app.json          - アプリケーション設定(全環境共通)
├── /docs                 - ドキュメント
├── /migrations
│   ├── /ddl              - DDL SQLファイル
│   └── /define           - テーブル定義(JSON)
├── /scripts              - ユーティリティスクリプト
├── /storage              - アプリケーションストレージ ログやユーザーアップロードファイルなど
│   └── /session          - セッションファイル格納先
└── /vendor               - Composer依存(ayutenn-core含む)
```

### 各ディレクトリの役割

#### `/app/api`
JSONレスポンスを返すAPI処理を格納します。
フロントエンドのJavaScriptから非同期で呼び出される処理に使用します。

詳細: [api.md](api.md)

#### `/app/controller`
フォームからのPOST処理を受け付け、データベース操作などを行った後、リダイレクトします。
従来のMVCパターンにおけるControllerに相当します。

詳細: [controller.md](controller.md)

#### `/app/database`
`DataManager`を継承したクラスを格納します。
大機能ごとに専用のマネージャークラスを作成し、SQL実行とデータ取得を行います。

詳細: [database.md](database.md)

#### `/app/model`
バリデーションルールを定義するJSONファイルを格納します。
ControllerやAPIで使用され、リクエストパラメータの自動バリデーションと型変換を行います。

詳細: [model.md](model.md)

#### `/app/routes`
ルート定義ファイル(PHPファイル)を格納します。
URLパターンと処理(Controller、API、View)のマッピングを定義します。

詳細: [routing.md](routing.md)

#### `/app/views`
HTMLテンプレートを格納します。
ルーティングから直接呼び出されるか、リダイレクトによって表示されます。

詳細: [view.md](view.md)

#### `/config`
- `config.json`: データベース接続情報など、環境によって異なる設定
- `app.json`: ディレクトリパスなど、アプリケーション固有の設定

#### `/docs`
- ドキュメントを格納します。

#### `/migrations`
- マイグレーションのための定義ファイルと、定義ファイルをもとに出力したDDL SQLファイルを格納します。

詳細: [migration.md](migration.md)

#### `/scripts`
- composerによるアップデート時起動プログラムなど、ユーティリティスクリプトを格納します。

#### `/storage`
- アプリケーションストレージ ログやユーザーアップロードファイルなど

#### `/vendor`
- composerによる依存パッケージを格納します。


## リクエスト処理の流れ

ayutennフレームワークでのリクエスト処理は、以下の流れで行われます。

### 1. エントリポイント (`/app/public/index.php`)

すべてのリクエストは`index.php`で受け取られます。

**index.phpが担う処理:**
- 設定ファイル(`config.json`, `app.json`)の読み込み
- ayutenn-coreのパス設定
- セッションの開始
- CSRFトークンの検証(POSTリクエストの場合)
- Routerの初期化とディスパッチ

### 2. ルーティング (`Router`)

Routerは`/app/routes`直下のすべての`.php`ファイルをルート定義として読み込み、リクエストURLとマッチするルートを探します。

**ルートの種類:**
- **Controller**: フォーム処理を行い、リダイレクトする
- **API**: JSON形式のレスポンスを返す
- **View**: HTMLを表示する
- **Redirect**: 別のURLにリダイレクトする

**ルート定義の例:**
```php
// /app/routes/web.php
use ayutenn\core\routing\Route;

return [
    // ビュー表示
    new Route('GET', '/', 'view', '/views/top.php'),

    // コントローラー
    new Route('POST', '/login', 'controller', 'Login'),

    // API
    new Route('POST', '/api/get-user', 'api', 'GetUserApi'),
];
```

### 3. 処理の実行

マッチしたルートに応じて、以下のいずれかが実行されます。

#### パターンA: Controller

1. Controllerクラスのインスタンス化
2. `run()`メソッドが呼び出される
3. リクエストパラメータのバリデーション
4. バリデーション成功時: `main()`メソッドを実行
5. バリデーション失敗時: エラーメッセージをセッションに保存し、`$redirectUrlWhenError`にリダイレクト
6. `main()`内でデータベース処理などを実行
7. 最終的に`redirect()`で別ページにリダイレクト

原則、全てのコントローラーでPRGパターンを採用します。
コントローラーはGETリクエストを受け取るべきではありませんし、
コントローラー内でrequireなどでビューを表示すべきではありません。

検索処理を行うときなど、GETリクエストを受け取る場合は、すべてビューファイルとして実装します。

```php
// Controllerの実行フロー
POST /register
  ↓
Router -> RegisterController
  ↓
run() → バリデーション → main()
  ↓
UserManager->createUser()
  ↓
redirect('/register-complete')
```

#### パターンB: API

1. APIクラスのインスタンス化
2. `run()`メソッドが呼び出される
3. リクエストパラメータのバリデーション
4. バリデーション成功時: `main()`メソッドを実行してJSON返却
5. バリデーション失敗時: エラーのJSONレスポンスを返却

```php
// APIの実行フロー
POST /api/get-user
  ↓
Router -> GetUserApi
  ↓
run() → バリデーション → main()
  ↓
UserManager->getUser()
  ↓
return JSON response
```

#### パターンC: View

指定されたビューファイル(PHPファイル)をそのまま実行し、HTMLを出力します。

```php
// Viewの実行フロー
GET /profile
  ↓
Router -> /views/profile.php
  ↓
HTML出力
```

## 開発の流れ

### 1. モデルファイルの作成

まず、バリデーションルールを定義するモデルファイルを作成します。

```json
// /app/model/post_title.json
{
    "name": "タイトル",
    "type": "string",
    "min_length": 1,
    "max_length": 100
}
```

### 2. DataManagerの作成

データベース操作を行うマネージャークラスを作成します。

```php
// /app/database/PostManager.php
class PostManager extends DataManager
{
    public function createPost(string $title, string $body): QueryResult
    {
        // SQL実行...
        return QueryResult::success();
    }
}
```

### 3. ControllerまたはAPIの作成

リクエストを処理するControllerまたはAPIを作成します。

```php
// /app/controller/CreatePost.php
class CreatePost extends Controller
{
    protected array $RequestParameterFormat = [
        'title' => ['name' => 'タイトル', 'format' => 'post_title'],
    ];

    public function main(): void
    {
        $postManager = new PostManager(DbConnector::connectWithPdo());
        $result = $postManager->createPost($this->parameter['title'], ...);

        if ($result->isSucceed()) {
            $this->redirect('/post-complete');
        }
    }
}
```

### 4. ビューの作成

HTMLテンプレートを作成します。

```php
// /app/views/post-form.php
<form method="POST" action="/create-post">
    <input type="text" name="title">
    <button type="submit">投稿</button>
</form>
```

### 5. ルート定義

ルート定義ファイルにマッピングを追加します。

```php
// /app/routes/web.php
return [
    new Route('GET', '/post-form', 'view', '/views/post-form.php'),
    new Route('POST', '/create-post', 'controller', 'CreatePost'),
];
```
