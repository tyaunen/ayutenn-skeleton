# Best Practices (ベストプラクティス)

このドキュメントでは、skeletonプロジェクトでayutennフレームワークを使用する際のベストプラクティスと、よくある間違いをまとめます。

## コントローラー

### ✅ DO: コントローラーファイル末尾でインスタンスを返す

```php
class Login extends Controller
{
    public function main(): void
    {
        // 処理...
    }
}
return new Login; // ← 必須！
```

コントローラーファイルの末尾には、**必ず** `return new ClassName;` を記述してください。
これを忘れると「requireからクラスのインスタンスが取得できません」というエラーが発生します。

### ✅ DO: 名前空間をファイルパスと一致させる

```php
// ファイル: /app/controller/session/Login.php
namespace ayutenn\skeleton\app\controller\session; // ← ファイルパスと一致

class Login extends Controller { ... }
```

名前空間はファイルの配置場所と完全に一致させる必要があります。
不一致の場合、オートローダーがクラスを見つけられません。

### ✅ DO: コントローラーは必ずリダイレクトする

```php
public function main(): void
{
    // 処理...
    $this->redirect('/success');
    return;
}
```

コントローラーはビューを直接表示せず、必ずリダイレクトしてください。
ビューを表示したい場合も、ビューのURLにリダイレクトします。

### ❌ DON'T: コントローラー内でビューを直接読み込まない

```php
// ❌ 悪い例
public function main(): void
{
    require_once(__DIR__ . '/../views/profile.php');
}
```

コントローラーからビューを直接読み込むのは避けてください。
代わりにビューのURLにリダイレクトし、ビューは `'view'` タイプのルートとして定義してください。

## ビュー

### ✅ DO: ビュー内でデータを取得する

```php
// ビューファイル内
<?php
use ayutenn\core\database\DbConnector;
use ayutenn\skeleton\app\database\UserManager;

$pdo = DbConnector::connectWithPdo();
$user_manager = new UserManager($pdo);
$result = $user_manager->getUser($user_id);
$user = $result->data[0];
?>
```

ビューで必要なデータは、ビュー内で直接取得してください。
コントローラーからビューへのデータ受け渡しは行いません。

### ✅ DO: リダイレクトは `Redirect` クラスを使用

```php
use ayutenn\core\utils\Redirect;

if (!$result->isSucceed()) {
    Redirect::redirect('./error');
}
```

### ❌ DON'T: `header()` 関数でリダイレクトしない

```php
// ❌ 悪い例
header('Location: ./error');
exit;
```

`header()` 関数は使用せず、`Redirect::redirect()` を使用してください。

## データベース

### ✅ DO: `QueryResult` の `getData()` メソッドでデータを取得

```php
$result = $user_manager->getUser($user_id);

if ($result->isSucceed()) {
    $user = $result->getData()[0]; // ← getData() でデータを取得
}
```

`QueryResult` クラスには `getData()` メソッドがあります。

### ✅ DO: データは配列の配列として扱う

```php
$result = $user_manager->getUsers(0, 100);
$users = $result->getData(); // ← 複数行の配列

foreach ($users as $user) {
    echo $user['user_name'];
}
```

`UserManager::getUser()` の結果も、単一行であっても配列の配列として返されます。
そのため `$result->getData()[0]` で最初の行を取得する必要があります。

## セッション

### ✅ DO: `FlashMessage::getMessages()` で全メッセージを取得

```php
use ayutenn\core\session\FlashMessage;

$session_messages = FlashMessage::getMessages();
$alert_messages = [];
$info_messages = [];

foreach ($session_messages as $msg) {
    if ($msg['alert_type'] === FlashMessage::ALERT) {
        $alert_messages[] = $msg['text'];
    } elseif ($msg['alert_type'] === FlashMessage::INFO) {
        $info_messages[] = $msg['text'];
    }
}
```

### メッセージの追加方法

```php
FlashMessage::info('成功メッセージ');
FlashMessage::alert('警告メッセージ');
FlashMessage::error('エラーメッセージ');
```

## セキュリティ

### ✅ DO: 出力時は必ず `htmlspecialchars()` を使用

```php
<p><?= htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8') ?></p>
```

XSS攻撃を防ぐため、ユーザー入力や外部データを表示する際は必ずエスケープしてください。

### ✅ DO: フォームには必ずCSRFトークンを含める

```php
use ayutenn\core\utils\CsrfTokenManager;

$csrf_manager = new CsrfTokenManager();
$csrf_token = $csrf_manager->getToken();
?>

<form method="POST" action="./login">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
    <!-- フォーム項目 -->
</form>
```

## バリデーション

### ✅ DO: 緩いバリデーションには `all_ok.json` を使用

プロフィール文など、自由入力を許可したい場合:

```json
// /app/model/all_ok.json
{
    "type": "string",
    "min": 0,
    "max": 1000,
    "regexp": "//"
}
```

```php
protected array $RequestParameterFormat = [
    'profile' => ['name' => 'プロフィール', 'format' => 'all_ok'],
];
```

## 認証

### ✅ DO: `Auth` ヘルパーを活用

```php
use ayutenn\skeleton\app\helper\Auth;

// ログイン状態の確認
if (Auth::isLogined()) {
    // ログイン中の処理
}

// ログインユーザー情報の取得
$login_user = Auth::getLoginUser();
$user_id = $login_user['id'];
```

### ✅ DO: サイドバーなどでログイン状態に応じた表示切り替え

```php
if (Auth::isLogined()) {
    $menu_items = [
        ['name' => 'プロフィール', 'url' => './profile'],
        ['name' => 'ログアウト', 'url' => './logout'],
    ];
} else {
    $menu_items = [
        ['name' => 'ログイン', 'url' => './login'],
        ['name' => 'ユーザー登録', 'url' => './register'],
    ];
}
```

## よくあるエラーと対処法

### エラー: "requireからクラスのインスタンスが取得できません"

**原因**: コントローラーファイルの末尾に `return new ClassName;` がない

**対処法**: ファイル末尾に `return new ClassName;` を追加

### エラー: "Class not found"

**原因**: 名前空間がファイルパスと一致していない

**対処法**: 名前空間をファイルの配置場所と完全に一致させる

### エラー: "Call to undefined method getData()"

**原因**: `QueryResult` に `getData()` メソッドは存在しない

**対処法**: `$result->data` で直接アクセス

### エラー: セッションがHTTPで動作しない

**原因**: `session_set_cookie_params(['secure' => true])` が固定されている

**対処法**: `'secure' => !empty($_SERVER['HTTPS'])` に変更

## まとめ

- コントローラーはインスタンスを返し、リダイレクトする
- ビューはデータを取得し、HTMLを生成する
- `QueryResult` の `data` プロパティに直接アクセス
- リダイレクトは `Redirect` クラスを使用
- セキュリティ対策を忘れずに
