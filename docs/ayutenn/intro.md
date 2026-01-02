# ayutenn フレームワーク 概要

## ayutennとは

ayutennは、PHPで構築されたシンプルで直感的なWebアプリケーションフレームワークです。
ayutennは`vendor/tyaunen/ayutenn-core`と`vendor/tyaunen/ayutenn-css`を使用しています。

### 設計思想

- **シンプルさ**: 複雑な設定や学習コストを最小限に抑え、直感的に使えることを重視
- **実用性**: 実際の開発で必要な機能を素早く実装できる
- **明示性**: 「魔法」のような動作を避け、コードの動きが明確

### 主な特徴

- **統合されたバリデーション**: JSONファイルでバリデーションルールを定義し、自動的に型変換
- **MVCライクな構造**: Model(バリデーション定義)、View、Controller、API、Databaseを明確に分離
- **安全なデータベースアクセス**: プリペアドステートメントとQueryResultによる統一的な結果管理
- **CSRFトークン**: 全てのPOSTリクエストで自動的にCSRF対策
- **セッション管理**: フラッシュメッセージ、フォーム入力値の一時保存などを標準装備

### ドキュメント

開発を始める前に、以下のドキュメントを参照してください:

- **[ベストプラクティス](best-practices.md)** - よくある間違いと推奨される実装方法
- [コントローラー](controller.md) - フォーム処理とリダイレクト
- [ビュー](view.md) - HTMLの表示とデータ取得
- [データベース](database.md) - データベース操作
- [モデル](model.md) - バリデーションルール定義
- [API](api.md) - JSONレスポンスの返却
- [ルーティング](routing.md) - URLとハンドラーのマッピング
- [テスト](testing.md) - ユニットテストの実装


## フレームワークドキュメント参照

Ayutennフレームワークのコア機能の詳細なAPIリファレンスについては、以下のドキュメントを参照してください：

- [routing.md](../../vendor/tyaunen/ayutenn-core/docs/routing.md) - Route, RouteGroup, Middleware
- [requests.md](../../vendor/tyaunen/ayutenn-core/docs/requests.md) - Controller, Api基底クラス
- [database.md](../../vendor/tyaunen/ayutenn-core/docs/database.md) - DataManager, DbConnector, QueryResult
- [validation.md](../../vendor/tyaunen/ayutenn-core/docs/validation.md) - バリデーション仕様
- [session.md](../../vendor/tyaunen/ayutenn-core/docs/session.md) - FlashMessage
- [utils.md](../../vendor/tyaunen/ayutenn-core/docs/utils.md) - ユーティリティクラス
- [config.md](../../vendor/tyaunen/ayutenn-core/docs/config.md) - 設定管理
- [migration.md](../../vendor/tyaunen/ayutenn-core/docs/migration.md) - マイグレーション

## ディレクトリ構造

```
/skeleton
├── /app
│   ├── /api              - API処理(JSON形式でレスポンスを返す)
│   ├── /controller       - コントローラー(フォーム処理後にリダイレクト)
│   ├── /database         - DataManagerクラス(データベース操作)
│   ├── /helper           - 再利用可能なヘルパークラス・関数
│   ├── /model            - バリデーションルール定義(JSONファイル)
│   ├── /public           - エントリポイント(index.php)とアセット(css, js, img)
│   ├── /routes           - ルート定義ファイル
│   │   └── /middleware   - ミドルウェア(ログインチェックなど)
│   └── /views            - ビューファイル(HTML)
│       ├── /components   - 再利用可能なHTMLコンポーネント
│       ├── /guest        - 認証不要ページ(ログイン画面など)
│       ├── /main         - 認証必要ページ
│       └── /system       - システムページ(404など)
├── /config
│   ├── config.json       - 環境固有の設定(DB接続情報など)
│   └── app.json          - アプリケーション設定(全環境共通)
├── /docs                 - ドキュメント
├── /migrations
│   ├── /ddl              - DDL SQLファイル
│   └── /define           - テーブル定義(JSON)
├── /scripts              - ユーティリティスクリプト
├── /storage              - アプリケーションストレージ
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
テーブルごとに専用のマネージャークラスを作成し、SQL実行とデータ取得を行います。

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

## リクエスト処理の流れ

ayutennフレームワークでのリクエスト処理は、以下の流れで行われます。

### 1. エントリポイント (`/app/public/index.php`)

すべてのリクエストは`index.php`で受け取られます。

**初期処理:**
- 設定ファイル(`config.json`, `app.json`)の読み込み
- セッションの開始
- CSRFトークンの検証(POSTリクエストの場合)
- Routerの初期化とディスパッチ

```php
// index.phpの主要部分
Config::reset(__DIR__ . '/../../config');
session_start();

$path_root = Config::get('PATH_ROOT');
$route_dir = $_SERVER['DOCUMENT_ROOT'] . Config::get('ROUTE_DIR');
$router = new Router($route_dir, $path_root);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
```

### 2. ルーティング (`Router`)

Routerは`/app/routes`内のすべてのルート定義を読み込み、リクエストURLとマッチするルートを探します。

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
2. `run()`メソッドが自動呼び出される
3. リクエストパラメータのバリデーション
4. バリデーション成功時: `main()`メソッドを実行
5. バリデーション失敗時: エラーメッセージをセッションに保存し、`$redirectUrlWhenError`にリダイレクト
6. `main()`内でデータベース処理などを実行
7. 最終的に`redirect()`で別ページにリダイレクト

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
2. `run()`メソッドが自動呼び出される
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

## 主要コンポーネント

### Model(バリデーション定義)

JSONファイルでバリデーションルールを定義します。

```json
// /app/model/user_id.json
{
    "name": "ユーザーID",
    "type": "int",
    "min": 1
}
```

**特徴:**
- 型定義(`string`, `int`, `number`, `boolean`, `array`)
- 範囲制限(`min`, `max`, `min_length`, `max_length`)
- 形式制限(`email`, `url`, `alphanumeric`, `symbols`など)
- 自動的な型変換とエラーメッセージ生成

詳細: [model.md](model.md)

### Controller

フォーム処理を行い、リダイレクトします。

```php
class Login extends Controller
{
    protected array $RequestParameterFormat = [
        'user-id' => ['name' => 'ユーザーID', 'format' => 'user_id'],
        'password' => ['name' => 'パスワード', 'format' => 'password'],
    ];

    public function main(): void
    {
        $user_id = $this->parameter['user-id'];
        $password = $this->parameter['password'];

        // 処理...
        $this->redirect('/top');
    }
}
```

**特徴:**
- 自動バリデーション
- Form Remain機能(入力値の一時保存)
- リダイレクト処理の統一化

詳細: [controller.md](controller.md)

### API

JSON形式のレスポンスを返します。

```php
class GetUserApi extends Api
{
    protected array $RequestParameterFormat = [
        'user-id' => ['name' => 'ユーザーID', 'format' => 'user_id'],
    ];

    public function main(): array
    {
        $user_id = $this->parameter['user-id'];

        // 処理...
        return $this->createResponse(true, ['user' => $user_data]);
    }
}
```

**特徴:**
- 自動バリデーション
- 統一されたレスポンス形式
- エラーハンドリング

詳細: [api.md](api.md)

### DataManager

データベース操作を安全に行います。

```php
class UserManager extends DataManager
{
    public function getUser(string $user_id): QueryResult
    {
        $sql = "SELECT * FROM user WHERE user_id = :user_id";
        $params = [':user_id' => [$user_id, PDO::PARAM_STR]];

        $results = $this->executeAndFetchAll($sql, $params);

        if (count($results) !== 0) {
            return QueryResult::success(data: $results);
        } else {
            return QueryResult::alert('ユーザーが見つかりませんでした。');
        }
    }
}
```

**特徴:**
- プリペアドステートメントによるSQLインジェクション対策
- QueryResultによる統一的な結果管理
- `executeStatement()`と`executeAndFetchAll()`の2つの基本メソッド

詳細: [database.md](database.md)

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

## セキュリティ機能

### CSRFトークン

全てのPOSTリクエストで自動的にCSRFトークンが検証されます。

```php
// ビューでCSRFトークンを出力
use ayutenn\core\utils\CsrfTokenManager;
$csrf_manager = new CsrfTokenManager();
?>
<form method="POST" action="/submit">
    <input type="hidden" name="csrf_token" value="<?= $csrf_manager->getToken() ?>">
    <!-- フォームの内容 -->
</form>
```

### SQLインジェクション対策

DataManagerのプリペアドステートメントにより、SQLインジェクションを防ぎます。

```php
// 安全なSQL実行
$params = [':user_id' => [$user_id, PDO::PARAM_STR]];
$results = $this->executeAndFetchAll($sql, $params);
```

### バリデーション

モデルファイルによる自動バリデーションで、不正なデータの侵入を防ぎます。

## ユーティリティ

### セッション管理

- **FlashMessage**: フラッシュメッセージ(成功・エラー通知)
- **Form Remain**: バリデーションエラー時の入力値保存

### その他のユーティリティ

- **Logger**: ログ出力
- **Redirect**: リダイレクト処理
- **CsrfTokenManager**: CSRFトークン管理
- **FileHandler**: ファイルアップロード処理
- **Uuid**: UUID生成

## 次のステップ

より詳しい情報は、以下のドキュメントを参照してください:

1. **[model.md](model.md)** - バリデーションルールの定義方法
2. **[database.md](database.md)** - データベース操作の実装
3. **[controller.md](controller.md)** - フォーム処理の実装
4. **[api.md](api.md)** - API開発
5. **[testing.md](testing.md)** - ユニットテストの実装

各ドキュメントには、実践的なコード例とベストプラクティスが記載されています。
