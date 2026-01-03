# View (ビュー)

このドキュメントでは、HTMLを生成するビューファイルの作成方法を説明します。

## フレームワークリファレンス

ビューで使用するユーティリティの詳細仕様：
- **[utils.md](../../vendor/tyaunen/ayutenn-core/docs/utils.md)** - Redirect, CsrfTokenManager
- **[session.md](../../vendor/tyaunen/ayutenn-core/docs/session.md)** - FlashMessage

---

## 概要

ビューは、HTMLを生成してユーザーに表示するPHPファイルです。
ルート定義で `'view'` タイプとして指定され、直接実行されます。

## 格納ディレクトリ

ビューは `/app/views/` 以下に格納します。
認証が不要なページは `/app/views/guest/`、認証が必要なページは `/app/views/main/` に配置するなど、用途に応じてサブディレクトリを作成できます。

## ルート定義

ビューは `route.php` で以下のように定義します:

```php
// /app/routes/route.php
new Route('GET', '/login', 'view', '/guest/login'),
```

この場合、`/app/views/guest/login.php` が読み込まれます。

## 基本的な構造

```php
<?php
use ayutenn\core\config\Config;
use ayutenn\core\session\FlashMessage;
?>

<!DOCTYPE html>
<html lang="ja" prefix="og: http://ogp.me/ns#">
<head>
    <title><?= Config::get('APP_TITLE') ?> トップページ</title>
    <?php require(PROJECT_ROOT . '/app/views/components/flat/head.php'); ?>
</head>

<body data-page-name='top'>
    <header>
        <h1>ayutennへようこそ！</h1>
    </header>
    <main>
        <p>コンテンツ</p>
    </main>
</body>
</html>
```

## パスの指定
必ずすべてのページで、`/app/views/components/flat/head.php`を、headタグの内容としてrequireしてください。
baseタグでリンクを制御しています。
```php
// /app/public/index.php
define('URL_ROOT', (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . PATH_ROOT);
```
```html
<!-- /app/views/components/flat/head.php -->
<base href="<?= URL_ROOT ?>/">
```

htaccessに変更を加えていなければ、静的ファイルへの直接アクセスは`/app/public`内でのみ許可されているかつ、`/app/public`を省略したものとして扱われます。
つまり、`/app/public/assets/img/common/icon.png`をビューで表示する場合、以下のように書くことができます。

```html
<img src="./assets/img/common/icon.png">
```
リンクやリダイレクトはこうです。
```html
<a href="./user/login">Login</a>
```
```php
Redirect::redirect(URL_ROOT . '/user/login');
```

## データの取得

ビュー内でデータが必要な場合は、ビュー内で直接取得します。
コントローラーからビューへのデータ受け渡しは行いません。

```php
<?php
use ayutenn\core\database\DbConnector;
use ayutenn\skeleton\app\database\UserManager;
use ayutenn\skeleton\app\helper\Auth;
use ayutenn\core\utils\Redirect;

// ログインユーザーの情報を取得
$login_user = Auth::getLoginUser();
$user_id = $login_user['id'];

$pdo = DbConnector::connectWithPdo();
$user_manager = new UserManager($pdo);
$result = $user_manager->getUser($user_id);

if (!$result->isSucceed()) {
    // エラー時はリダイレクト
    Redirect::redirect(URL_ROOT . '/logout');
}

$user = $result->getData();
?>

<!DOCTYPE html>
<!-- ここから通常のHTML -->
```

## アラート（フラッシュメッセージ）の表示

コントローラーから渡されたフラッシュメッセージを表示する場合:
/app/views/components/FlashMessage.phpが利用できます。

```php
<?php
use ayutenn\skeleton\app\views\components\FlashMessage;
?>

<!DOCTYPE html>
<html lang="ja" prefix="og: http://ogp.me/ns#">
<body>
    <?php FlashMessage::render(); ?>
</body>
</html>
```

## フォームの作成

### CSRFトークン

フォームには必ずCSRFトークンを含めてください:

```php
<?php
use ayutenn\core\utils\CsrfTokenManager;

$csrf_manager = new CsrfTokenManager();
$csrf_token = $csrf_manager->getToken();
?>

<form method="POST" action="./login">
    <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">
    <!-- フォーム項目 -->
</form>
```

### Form Remain (入力値の復元)

バリデーションエラー時に入力値を復元する場合:

```php
<?php
use ayutenn\skeleton\app\controller\Register;

// Form Remain: エラー時の入力値復元
$remain_params = Register::getRemainRequestParameter();
$user_id = $remain_params['user-id'] ?? '';
$user_name = $remain_params['user-name'] ?? '';
?>

<form method="POST" action="./register">
    <input type="text" name="user-id" value="<?= h($user_id) ?>">
    <input type="text" name="user-name" value="<?= h($user_name) ?>">
</form>
```

## コンポーネントの利用

共通部品は `/app/views/components/` に配置し、`require` で読み込みます:

```php
<!-- ヘッダー部分の共通読み込み -->
<?php require(__DIR__ . '/../components/flat/head.php'); ?>

<!-- サイドバーの読み込み -->
<?php require(__DIR__ . '/../components/sidebar.php'); ?>
```

## セキュリティ

### XSS対策

ユーザー入力や外部データを表示する際は必ず `htmlspecialchars()` を使用してください。

また、`/helper/shorthands.php`には定義されている`h()`、`hbr()`関数も利用可能です。
これらはそれぞれ、`htmlspecialchars($str, ENT_QUOTES, 'UTF-8')`、`nl2br(htmlspecialchars($str, ENT_QUOTES, 'UTF-8'))`のエイリアスです。

`/helper/shorthands.php`は`index.php`で読み込まれているため、どこでも使用できます。

```php
/**
 * @param string $str
 * @return string
 */
function h(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * @param string $str
 * @return string
 */
function hbr(string $str): string
{
    return nl2br(h($str));
}
```

## ベストプラクティス

1. **データ取得はビュー内で**: コントローラーからビューへのデータ渡しは行わず、ビュー内で必要なデータを取得する
2. **リダイレクトは `Redirect` クラスで**: `header()` 関数は使用しない
3. **セキュリティ**: 出力時は必ず `htmlspecialchars()` を使用
4. **CSRFトークン**: フォームには必ずCSRFトークンを含める
5. **共通部品の活用**: 重複するコードはコンポーネント化する

## コントローラーとの関係

- **コントローラー**: データ処理を行い、リダイレクトする
- **ビュー**: データを取得し、HTMLを生成する

コントローラーがビューを表示したい場合でも、コントローラー内で直接ビューを読み込むのではなく、
必ずビューのURLにリダイレクトしてください。
