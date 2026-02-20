# CSRF保護の追加

フォームにCSRF保護を追加する際は、CsrfTokenManager クラスを使用してください。

## 1. フォームにトークンを埋め込む

```php
<?php
use ayutenn\core\utils\CsrfTokenManager;

$csrf = new CsrfTokenManager();
?>

<form method="POST" action="/submit">
    <input type="hidden" name="csrf_token" value="<?= $csrf->getToken() ?>">

    <!-- 他のフォーム要素 -->
    <input type="text" name="username">
    <button type="submit">送信</button>
</form>
```

## 2. /public/index.phpでトークンを検証

```php
// POSTリクエストのときは常にcsrfが必要
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrf_manager = new CsrfTokenManager();
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!$csrf_manager->validateToken($submittedToken)) {
        http_response_code(403);
        AlertsSession::putErrorMessageIntoSession('タイムアウトです。ページを開いてからデータを送信するまで時間がかかりすぎたかもしれません。');
        Redirect::redirect(URL_ROOT);
    } else {
        $csrf_manager->getToken();
    }
}
```

## API

| メソッド | 説明 |
|---------|------|
| `getToken(): string` | CSRFトークンを取得（未生成時は自動生成） |
| `validateToken(string $token): bool` | トークンを検証（有効期限: 12時間） |

## 詳細ドキュメント

詳細は `vendor/tyaunen/ayutenn-core/docs/utils.md` を参照してください。
