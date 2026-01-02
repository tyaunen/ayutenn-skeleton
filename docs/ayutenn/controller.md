# Controller

このドキュメントでは、フォーム処理を行うControllerの実装方法を説明します。

## フレームワークリファレンス

Controller基底クラスの詳細仕様：
- **[requests.md](../../vendor/tyaunen/ayutenn-core/docs/requests.md)**

---

## 命名規則
Controllerファイルは、クラス名がファイル名と一致する必要があります。
例えば、Login.phpのようなクラスファイルです。

## 格納ディレクトリ
Controllerは、/app/controllerに格納される単一のクラスファイルである必要があります。分類・整理のためにサブディレクトリを作っても構いません。（例: /app/controller/option）
処理が1ファイルに収まらない複雑さになりそうなら、/app/helperに処理を分割することを検討してください。

## ayutennのControllerを継承する
Controllerは、ayutenn\core\requests\Controllerを、必ず継承してください。

## main()メソッドを実装する
処理本体となるmain()メソッドを、protected関数として実装してください。
このメソッドは、バリデーションが成功した後に自動的に呼び出されます。
main()メソッドの中で、データベース処理や他のヘルパーを呼び出し、最後にredirect()を行ってください。

## シンプルな例

```php
// /app/controller/session/Login.php
namespace ayutenn\skeleton\app\controller\session;

use ayutenn\core\requests\Controller;
use ayutenn\core\session\FlashMessage;
use ayutenn\skeleton\app\helper\Auth;

class Login extends Controller
{
    protected string $redirectUrlWhenError = '/';

    public function main(): void
    {
        // この時点でバリデーションは完了しているので、
        // $this->parameter から型変換済みのパラメータを取得できます
        $user_id = $this->parameter['user-id'];
        $password = $this->parameter['password'];

        if(Auth::login($user_id, $password)){
            FlashMessage::info('ログインに成功しました！');
            $this->redirect('/top');
            return;
        }else{
            FlashMessage::alert('IDかパスワードが違います。');
            $this->redirect($this->redirectUrlWhenError);
            return;
        }
    }
}

return new Login;
```

このControllerはルーターによって起動されたとき、バリデーションを実行し、成功すればmain()を実行してリダイレクトします。

## オプション
### リクエストパラメータをバリデート、取得する

Controllerにはバリデート機能があります。
リクエストパラメータを受け取るときは、必ずこの機能を使用してください。
バリデートを行うためには、以下のようにprotectedなプロパティを上書きした上で、jsonでモデルファイルを作成する必要があります。
モデルファイルの記述方法は、モデルファイルのドキュメントを確認してください。

```json
// /app/model/user_id.json
{
    "name": "user_id",
    "type": "int",
    "min": 1,
    "condition": ["numeric"]
}
```

```json
// /app/model/password.json
{
    "name": "password",
    "type": "string",
    "min_length": 8,
    "max_length": 255,
    "condition": ["symbols"]
}
```

```php
// /app/controller/session/Login.php
namespace ayutenn\skeleton\app\controller\session;

use ayutenn\core\requests\Controller;
use ayutenn\core\session\FlashMessage;
use ayutenn\skeleton\app\helper\Auth;

class Login extends Controller
{
    // リクエストパラメータのフォーマット定義
    protected array $RequestParameterFormat = [
        'user-id' => ['name' => 'ユーザーID', 'format' => 'user_id'],
        'password' => ['name' => 'パスワード', 'format' => 'password'],
    ];

    protected string $redirectUrlWhenError = '/';

    public function main(): void
    {
        // $this->parameterに、バリデート+キャスト済のパラメータが格納されています
        // この場合user-idは1以上の整数、passwordは8-255文字の英数記号文字列です
        $user_id = $this->parameter['user-id'];
        $password = $this->parameter['password'];

        if(Auth::login($user_id, $password)){
            FlashMessage::info('ログインに成功しました！');
            $this->redirect('/top');
            return;
        }else{
            FlashMessage::alert('IDかパスワードが違います。');
            $this->redirect($this->redirectUrlWhenError);
            return;
        }
    }
}

return new Login;
```

バリデートに失敗したときは、自動的にエラーメッセージがセッションに保存され、`$redirectUrlWhenError`にリダイレクトされます。
main()メソッドは実行されません。

リクエストパラメータを任意設定にしたい場合は、以下のように書きます。

```php
class MyController extends Controller
{
    protected array $RequestParameterFormat = [
        'user-id' => ['name' => 'ユーザーID', 'format' => 'user_id'],
        'password' => ['name' => 'パスワード', 'format' => 'password'],
        'remember-me' => ['name' => 'ログイン状態を保持', 'format' => 'boolean', 'require' => false], // この項目は任意
    ];

    protected string $redirectUrl = '/top';
    protected string $redirectUrlWhenError = '/';

    public function main(): void
    {
        $user_id = $this->parameter['user-id'];
        $password = $this->parameter['password'];
        $remember = $this->parameter['remember-me'] ?? false; // 任意項目はissetされていない可能性がある

        // 処理...
    }
}
```

### リクエストパラメータを保存する (Form Remain機能)

バリデーションエラー時など、ユーザーが入力した内容を再表示したい場合があります。
`$remainRequestParameter`をtrueに設定すると、リクエストパラメータをセッションに自動保存します。

```php
class Register extends Controller
{
    // リクエストパラメータをセッションに保存する
    protected bool $remainRequestParameter = true;

    protected array $RequestParameterFormat = [
        'user-id' => ['name' => 'ユーザーID', 'format' => 'user_id'],
        'password' => ['name' => 'パスワード', 'format' => 'password'],
    ];

    protected string $redirectUrlWhenError = '/register-form';

    public function main(): void
    {
        // 登録処理...

        // 登録成功時は保存されたリクエストパラメータをクリア
        self::unsetRemain();
        $this->redirect('/top');
        return;
    }
}

return new Register;
```

ビュー側では、以下のように保存されたパラメータを取得できます。

```php
// /app/views/register-form.php
$remain = \ayutenn\skeleton\app\controller\Register::getRemainRequestParameter();
$user_id = $remain['user-id'] ?? '';
?>
<form method="POST" action="/register">
    <input type="text" name="user-id" value="<?= h($user_id) ?>">
    <!-- フォームの続き -->
</form>
```

### GETパラメータを保持してリダイレクトする

検索処理などで、リダイレクト先にもGETパラメータを引き継ぎたい場合があります。
`$keepGetParameter`をtrueに設定すると、リダイレクト時にGETパラメータが自動的に付与されます。

```php
class Search extends Controller
{
    // リダイレクト先にGETパラメータを引き継ぐ
    protected bool $keepGetParameter = true;

    protected array $RequestParameterFormat = [
        'keyword' => ['name' => '検索キーワード', 'format' => 'search_keyword'],
    ];

    protected string $redirectUrlWhenError = '/search-form';

    public function main(): void
    {
        // 検索処理...
        $this->redirect('/search-result');
        return;
    }
}

return new Search;
```

### エラー時のリダイレクト先を指定する

バリデーションエラーが発生した場合、自動的に`$redirectUrlWhenError`で指定したパスにリダイレクトされます。

```php
class MyController extends Controller
{
    // バリデーションエラー時のリダイレクト先
    protected string $redirectUrlWhenError = '/error-page';

    // ...
}
```

## Controllerの実行フロー

1. ルーターがControllerのrun()メソッドを呼び出す
2. リクエストメソッド(GET/POST)に応じてパラメータを取得
3. `$remainRequestParameter`がtrueの場合、パラメータをセッションに保存
4. `$RequestParameterFormat`に基づいてバリデーションを実行
5. バリデーションエラーがある場合:
   - エラーメッセージをセッションに保存
   - `$redirectUrlWhenError`にリダイレクト
   - main()は実行されない
6. バリデーション成功の場合:
   - キャスト済みパラメータを`$this->parameter`に格納
   - main()メソッドを実行

## まとめ

Controllerは以下の役割を持ちます:

- リクエストパラメータの自動バリデーション
- バリデーション済みデータの型変換
- フォーム入力の一時保存
- 処理後のリダイレクト

これらの機能により、安全で保守性の高いコードを簡潔に記述できます。

## 重要な注意点

### コントローラーファイル末尾でインスタンスを返す

**必須**: コントローラーファイルの末尾には、必ず `return new ClassName;` を記述してください。

```php
class Login extends Controller
{
    public function main(): void
    {
        // 処理...
        $this->redirect('/top');
        return;
    }
}
return new Login; // ← 必須！
```

これを忘れると「requireからクラスのインスタンスが取得できません」というエラーが発生します。

### 名前空間をファイルパスと一致させる

名前空間は、ファイルの配置場所と完全に一致させる必要があります:

```php
// ファイル: /app/controller/session/Login.php
namespace ayutenn\skeleton\app\controller\session; // ← ファイルパスと一致

// ファイル: /app/controller/Register.php
namespace ayutenn\skeleton\app\controller; // ← ファイルパスと一致
```

不一致の場合、オートローダーがクラスを見つけられず、"Class not found" エラーが発生します。

### コントローラーはビューを直接表示しない

コントローラーは処理を行った後、必ずリダイレクトしてください。
ビューを表示したい場合でも、ビュー内で直接表示するのではなく、ビューのURLにリダイレクトします:

```php
// ✅ 正しい例
public function main(): void
{
    // 処理...
    $this->redirect('/profile'); // ビューのURLにリダイレクト
    exit;
}
```

```php
// ❌ 間違った例
public function main(): void
{
    require_once(__DIR__ . '/../views/profile.php'); // ビューを直接読み込まない
}
```

ビューは `route.php` で `'view'` タイプとして定義し、ビュー内で必要なデータを取得してください。
