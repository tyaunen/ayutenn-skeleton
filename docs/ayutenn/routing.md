# Routing (ルート定義)

このドキュメントでは、skeletonプロジェクトでのルート定義の実装方法を説明します。

## フレームワークリファレンス

Route、RouteGroup、MiddlewareクラスのAPIリファレンスについては、ayutenn-coreのドキュメントを参照してください：

- **[routing.md](../../vendor/tyaunen/ayutenn-core/docs/routing.md)** - クラスの詳細仕様、コンストラクタ引数、メソッド一覧

## ルート定義とは

ルート定義は、URLパターンと処理(Controller、API、View)のマッピングを定義するものです。
Routerはリクエストされたページ(URL)とHTTPメソッドを基に、ルート定義を検索して適切な処理を実行します。

## 格納ディレクトリ

ルート定義ファイルは、`/app/routes`に格納するPHPファイルです。
このディレクトリ内のすべての`.php`ファイルが自動的に読み込まれます。

## ファイル形式

ルート定義ファイルは、`Route`または`RouteGroup`のインスタンスの配列を返す必要があります。

```php
<?php
use ayutenn\core\routing\Route;

return [
    new Route('GET', '/', 'view', '/guest/top'),
    new Route('POST', '/login', 'controller', '/session/Login'),
    // ...
];
```

## ルートの種類

Ayutennでは、4種類のルートアクションを定義できます。

### 1. view - ビュー表示

指定したビューファイル(PHP)を表示します。
HTMLの表示に使用します。

```php
new Route('GET', '/', 'view', '/guest/top')
```

- **HTTPメソッド**: `GET`
- **パス**: `/`
- **アクション**: `view`
- **リソース**: `/guest/top` → `/app/views/guest/top.php`が表示される

### 2. controller - コントローラー実行

指定したControllerクラスの`run()`メソッドを実行します。
フォーム処理などに使用します。

```php
new Route('POST', '/user/add', 'controller', '/CreateUserController')
```

- **HTTPメソッド**: `POST`
- **パス**: `/user/add`
- **アクション**: `controller`
- **リソース**: `/CreateUserController` → `/app/controller/CreateUserController.php`が実行される

### 3. api - API実行

指定したAPIクラスの`run()`メソッドを実行します。
JSON形式のレスポンスを返すAPIに使用します。

```php
new Route('GET', '/api/get/number', 'api', '/GetRandomNumberApi')
```

- **HTTPメソッド**: `GET` または `POST`
- **パス**: `/api/get/number`
- **アクション**: `api`
- **リソース**: `/GetRandomNumberApi` → `/app/api/GetRandomNumberApi.php`が実行される

### 4. redirect - リダイレクト

指定したパスにリダイレクトします。
ページの移動に使用します。

```php
new Route('GET', '/old-page', 'redirect', '/new-page')
```

- **HTTPメソッド**: `GET` または `POST`
- **パス**: `/old-page`
- **アクション**: `redirect`
- **リソース**: `/new-page` → このパスにリダイレクトされる

## Routeクラス

### コンストラクタ

```php
new Route(
    string $method,              // HTTPメソッド
    string $path,                // URLパス
    string $routeAction,         // アクション種類
    string $targetResourceName,  // リソース名
    array $middleware = []       // ミドルウェア(オプション)
)
```

### HTTPメソッド

以下のHTTPメソッドを指定できます:

- **GET**: ページ表示、データ取得
- **POST**: フォーム送信、データ作成・更新

```php
// GETリクエスト
new Route('GET', '/profile', 'view', '/user/profile'),

// POSTリクエスト
new Route('POST', '/register', 'controller', '/UserRegister'),
```

### パス

URLのパスを指定します。パスの末尾の`/`は自動的に削除されます。

```php
new Route('GET', '/', 'view', '/guest/top'),          // トップページ
new Route('GET', '/about', 'view', '/guest/about'),   // /about
new Route('GET', '/user/profile', 'view', '/user/profile'), // /user/profile
```

### リソース名

- **view**: `/app/views`からの相対パス(`.php`は省略)
- **controller**: `/app/controller`からの相対パス(`.php`は省略)
- **api**: `/app/api`からの相対パス(`.php`は省略)
- **redirect**: リダイレクト先のパス

```php
// view: /app/views/guest/top.php
new Route('GET', '/', 'view', '/guest/top'),

// controller: /app/controller/session/Login.php
new Route('POST', '/login', 'controller', '/session/Login'),

// api: /app/api/GetUserApi.php
new Route('POST', '/api/get-user', 'api', '/GetUserApi'),

// redirect: /topにリダイレクト
new Route('GET', '/home', 'redirect', '/top'),
```

## RouteGroup - ルートのグループ化

複数のルートをグループ化し、共通のミドルウェアを適用できます。
認証が必要なページをまとめて管理する際に便利です。

### コンストラクタ

```php
new RouteGroup(
    string $group,      // グループのパスプレフィックス
    array $routes,      // Routeの配列
    array $middleware   // ミドルウェアの配列
)
```

### 基本的な使用例

```php
use ayutenn\core\routing\Route;
use ayutenn\core\routing\RouteGroup;
use ayutenn\skeleton\app\routes\middleware\NeedAuth;

$need_auth = new NeedAuth('redirect', '/');

return [
    // 認証不要なルート
    new Route('GET', '/', 'view', '/guest/top'),
    new Route('POST', '/login', 'controller', '/session/Login'),

    // 認証が必要なルートをグループ化
    new RouteGroup('', [
        new Route('GET', '/top', 'view', '/main/top'),
        new Route('GET', '/profile', 'view', '/user/profile'),
        new Route('POST', '/profile/update', 'controller', '/user/UpdateProfile'),
    ], [$need_auth])
];
```

### パスプレフィックス

グループのパスプレフィックスを指定すると、グループ内の全ルートにプレフィックスが追加されます。

```php
// /adminプレフィックスを付与
new RouteGroup('/admin', [
    new Route('GET', '/dashboard', 'view', '/admin/dashboard'), // /admin/dashboard
    new Route('GET', '/users', 'view', '/admin/users'),         // /admin/users
], [$need_auth])
```

## Middleware - ミドルウェア

ミドルウェアは、ルートが実行される前に条件をチェックし、必要に応じてルートを上書きする機能です。
主にログイン認証などで使用します。

### ミドルウェアの作成

`ayutenn\core\routing\Middleware`を継承したクラスを作成します。

```php
// /app/routes/middleware/NeedAuth.php
namespace ayutenn\skeleton\app\routes\middleware;

use ayutenn\core\routing\Middleware;
use ayutenn\core\session\FlashMessage;
use ayutenn\skeleton\app\helper\Auth;

class NeedAuth extends Middleware
{
    /**
     * 副作用処理を実行する
     * ログインしていない場合、フラッシュメッセージを表示
     */
    public function handle(): void
    {
        if (!Auth::isLogined()) {
            FlashMessage::info("ログインが必要です。");
        }
    }

    /**
     * ルートを上書きすべきかどうかを判定する
     * ログインしていない場合はtrue（ルートを上書き）
     */
    public function shouldOverride(): bool
    {
        return !Auth::isLogined();
    }
}
```

### ミドルウェアの使用

#### 個別のルートに適用

```php
$need_auth = new NeedAuth('redirect', '/');

return [
    // このルートにのみミドルウェアを適用
    new Route('GET', '/profile', 'view', '/user/profile', [$need_auth]),
];
```

#### ルートグループに適用

```php
$need_auth = new NeedAuth('redirect', '/');

return [
    // グループ内の全ルートにミドルウェアを適用
    new RouteGroup('', [
        new Route('GET', '/top', 'view', '/main/top'),
        new Route('GET', '/profile', 'view', '/user/profile'),
    ], [$need_auth])
];
```

### ミドルウェアのコンストラクタ

ミドルウェアのコンストラクタでは、条件を満たさない場合の処理を指定します。

```php
// ログインページにリダイレクト
$need_auth = new NeedAuth('redirect', '/');

// ログインビューを表示
$need_auth = new NeedAuth('view', '/guest/login');

// エラーページを表示
$need_auth = new NeedAuth('view', '/error/403');
```

### handle()メソッド

副作用処理（フラッシュメッセージの設定など）を実行します。
`shouldOverride()`が呼ばれる前に必ず実行されます。

```php
public function handle(): void
{
    if (!Auth::isLogined()) {
        FlashMessage::info('ログインが必要です。');
    }
}
```

### shouldOverride()メソッド

このメソッドで条件をチェックし、`true`または`false`を返します。

- **true**: ミドルウェアで指定したアクションが実行される
- **false**: 元のルートが実行される

```php
public function shouldOverride(): bool
{
    // ログインしていない場合はルートを上書き
    return !Auth::isLogined();
}
```

## 実践的な例

### 基本的なルート定義

```php
// /app/routes/web.php
<?php
use ayutenn\core\routing\Route;

return [
    // トップページ
    new Route('GET', '/', 'view', '/guest/top'),

    // ログインフォーム表示
    new Route('GET', '/login', 'view', '/guest/login'),

    // ログイン処理
    new Route('POST', '/login', 'controller', '/session/Login'),

    // ログアウト処理
    new Route('POST', '/logout', 'controller', '/session/Logout'),
];
```

### 認証が必要なページ

```php
// /app/routes/web.php
<?php
use ayutenn\core\routing\Route;
use ayutenn\core\routing\RouteGroup;
use ayutenn\skeleton\app\routes\middleware\NeedAuth;

// ミドルウェアのインスタンス化
$need_auth = new NeedAuth('redirect', '/');

return [
    // ゲストページ
    new Route('GET', '/', 'view', '/guest/top'),
    new Route('POST', '/login', 'controller', '/session/Login'),

    // 認証が必要なページ
    new RouteGroup('', [
        new Route('GET', '/top', 'view', '/main/top'),
        new Route('GET', '/profile', 'view', '/user/profile'),
        new Route('POST', '/profile/update', 'controller', '/user/UpdateProfile'),
    ], [$need_auth])
];
```

### API定義

```php
// /app/routes/api.php
<?php
use ayutenn\core\routing\Route;

return [
    // ユーザー情報取得API
    new Route('POST', '/api/get-user', 'api', '/GetUserApi'),

    // 投稿作成API
    new Route('POST', '/api/create-post', 'api', '/CreatePostApi'),

    // 検索API
    new Route('GET', '/api/search', 'api', '/SearchApi'),
];
```

### 管理画面

```php
// /app/routes/admin.php
<?php
use ayutenn\core\routing\Route;
use ayutenn\core\routing\RouteGroup;
use ayutenn\skeleton\app\routes\middleware\NeedAdmin;

$need_admin = new NeedAdmin('redirect', '/');

return [
    // 管理画面は全て/adminプレフィックス + 管理者認証が必要
    new RouteGroup('/admin', [
        new Route('GET', '/dashboard', 'view', '/admin/dashboard'),
        new Route('GET', '/users', 'view', '/admin/users'),
        new Route('POST', '/users/delete', 'controller', '/admin/DeleteUser'),
        new Route('GET', '/settings', 'view', '/admin/settings'),
    ], [$need_admin])
];
```

### リダイレクト

```php
// /app/routes/web.php
<?php
use ayutenn\core\routing\Route;

return [
    // 古いURLから新しいURLへリダイレクト
    new Route('GET', '/old-page', 'redirect', '/new-page'),

    // トップページへリダイレクト
    new Route('GET', '/home', 'redirect', '/'),
];
```

## 複数のミドルウェア

ルートやルートグループに複数のミドルウェアを適用できます。
ミドルウェアは配列の順番に実行されます。

```php
$need_auth = new NeedAuth('redirect', '/');
$check_permission = new CheckPermission('view', '/error/403');

return [
    // 複数のミドルウェアを適用
    new Route('GET', '/admin/settings', 'view', '/admin/settings',
        [$need_auth, $check_permission]
    ),

    // RouteGroupにも適用可能
    new RouteGroup('/admin', [
        new Route('GET', '/dashboard', 'view', '/admin/dashboard'),
    ], [$need_auth, $check_permission])
];
```

## ルート定義ファイルの分割

ルート定義ファイルは複数に分割できます。
`/app/routes`内のすべての`.php`ファイルが読み込まれます。

```
/app/routes
├── web.php        - 通常のWebページルート
├── api.php        - APIルート
├── admin.php      - 管理画面ルート
└── /middleware
    ├── NeedAuth.php       - ログイン認証ミドルウェア
    └── NeedAdmin.php      - 管理者認証ミドルウェア
```

### web.php
```php
<?php
use ayutenn\core\routing\Route;
return [
    new Route('GET', '/', 'view', '/guest/top'),
    // ...
];
```

### api.php
```php
<?php
use ayutenn\core\routing\Route;
return [
    new Route('POST', '/api/get-user', 'api', '/GetUserApi'),
    // ...
];
```

### admin.php
```php
<?php
use ayutenn\core\routing\Route;
use ayutenn\core\routing\RouteGroup;
return [
    new RouteGroup('/admin', [
        new Route('GET', '/dashboard', 'view', '/admin/dashboard'),
        // ...
    ], [$need_admin])
];
```

## 404エラーページ

マッチするルートが見つからない場合、404エラーページが表示されます。

### 404ページの設定

`/config/app.json`で404ページを設定します。

```json
{
    "404_PAGE_FILE": "/system/404"
}
```

この設定により、`/app/views/system/404.php`が404ページとして表示されます。

## ベストプラクティス

### 1. ルート定義ファイルを機能ごとに分割する

大規模なアプリケーションでは、ルート定義を複数のファイルに分割しましょう。

```
/app/routes
├── web.php      - 通常のページ
├── api.php      - API
├── admin.php    - 管理画面
└── auth.php     - 認証関連
```

### 2. ミドルウェアを再利用する

同じ条件のチェックが必要な場合、ミドルウェアを作成して再利用しましょう。

```php
$need_auth = new NeedAuth('redirect', '/');

// 複数のRouteGroupで再利用
new RouteGroup('/user', [...], [$need_auth]),
new RouteGroup('/post', [...], [$need_auth]),
```

### 3. RouteGroupでルートをまとめる

共通のミドルウェアやパスプレフィックスがある場合、RouteGroupを使いましょう。

```php
// ✅ 良い例: RouteGroupでまとめる
new RouteGroup('/admin', [
    new Route('GET', '/dashboard', 'view', '/admin/dashboard'),
    new Route('GET', '/users', 'view', '/admin/users'),
], [$need_admin])

// ❌ 悪い例: 個別にミドルウェアを指定
new Route('GET', '/admin/dashboard', 'view', '/admin/dashboard', [$need_admin]),
new Route('GET', '/admin/users', 'view', '/admin/users', [$need_admin]),
```

### 4. HTTPメソッドを適切に使い分ける

- **GET**: データの取得、ページ表示
- **POST**: データの作成・更新、フォーム送信

```php
// ✅ 良い例
new Route('GET', '/users', 'view', '/user/list'),        // ユーザー一覧表示
new Route('POST', '/users/create', 'controller', '/CreateUser'), // ユーザー作成

// ❌ 悪い例
new Route('GET', '/users/create', 'controller', '/CreateUser'), // GETでデータ作成はNG
```

### 5. リソース名は明確に

ファイル名とクラス名が一致するよう、明確な名前を使用しましょう。

```php
// ✅ 良い例: ファイル名とリソース名が一致
new Route('POST', '/login', 'controller', '/session/Login'),
// → /app/controller/session/Login.php

// ❌ 悪い例: 略称を使うと分かりにくい
new Route('POST', '/login', 'controller', '/session/L'),
```

## まとめ

ルート定義は以下の役割を持ちます:

- URLパターンと処理のマッピング
- HTTPメソッドによるアクセス制御
- ミドルウェアによる認証・認可
- ルートグループによる共通設定の適用

これらの機能により、URLの管理と認証処理を明確に定義できます。

## 関連ドキュメント

- **[controller.md](controller.md)** - Controllerの実装方法
- **[api.md](api.md)** - APIの実装方法
- **[intro.md](intro.md)** - リクエスト処理の流れ
