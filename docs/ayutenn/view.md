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
`/app/views/components/flat/head.php`を、必ずすべてのページでheadタグの内容としてrequireしてください。
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

## アラートメッセージの表示

コントローラーから渡されたアラートメッセージを表示する場合:

```php
<?php
use ayutenn\core\session\FlashMessage;

// フラッシュメッセージ取得（取得後は自動的にセッションから削除される）
$session_messages = FlashMessage::getMessages();
$alert_messages = [];
$info_messages = [];
$error_messages = [];

foreach ($session_messages as $msg) {
    if ($msg['alert_type'] === FlashMessage::ALERT) {
        $alert_messages[] = $msg['text'];
    } elseif ($msg['alert_type'] === FlashMessage::INFO) {
        $info_messages[] = $msg['text'];
    } elseif ($msg['alert_type'] === FlashMessage::ERROR) {
        $error_messages[] = $msg['text'];
    }
}
?>

<!-- HTML内で表示 -->
<?php if (!empty($alert_messages)): ?>
    <?php foreach ($alert_messages as $message): ?>
        <div class="alert alert-warning">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($info_messages)): ?>
    <?php foreach ($info_messages as $message): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (!empty($error_messages)): ?>
    <?php foreach ($error_messages as $message): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
```

**メッセージタイプ:**
- `FlashMessage::INFO` - 成功・情報メッセージ
- `FlashMessage::ALERT` - 警告メッセージ
- `FlashMessage::ERROR` - エラーメッセージ


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
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
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
    <input type="text" name="user-id" value="<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?>">
    <input type="text" name="user-name" value="<?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?>">
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

ユーザー入力や外部データを表示する際は必ず `htmlspecialchars()` を使用してください:

```php
<p><?= htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8') ?></p>
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
