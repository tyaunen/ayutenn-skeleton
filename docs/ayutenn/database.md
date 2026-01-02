# Database (データベース操作)

このドキュメントでは、skeletonプロジェクトでのDataManagerクラスの作成方法を説明します。

## フレームワークリファレンス

DataManager、DbConnector、QueryResultクラスのAPIリファレンスについては、ayutenn-coreのドキュメントを参照してください：

- **[database.md](../../vendor/tyaunen/ayutenn-core/docs/database.md)** - クラスの詳細仕様、メソッド一覧、使用例

## DataManagerとは
DataManagerは、データベースへのアクセスを管理する抽象クラスです。
このクラスを継承したマネージャークラスを作成し、テーブルごとの操作をメソッドとして実装します。

## 格納ディレクトリ
DataManagerを継承したクラスは、`/app/database`に格納します。
テーブルやドメインごとに分類・整理することを推奨します。

## ayutennのDataManagerを継承する
データベース操作を行うクラスは、`ayutenn\core\database\DataManager`を必ず継承してください。

## シンプルな例

```php
// /app/database/UserManager.php
namespace ayutenn\skeleton\app\database;

use ayutenn\core\database\DataManager;
use ayutenn\core\database\QueryResult;
use PDO;

class UserManager extends DataManager
{
    /**
     * ユーザーIDでユーザーを取得
     */
    public function getUser(string $user_id): QueryResult
    {
        $sql = <<<SQL
            SELECT
                user.user_id,
                user.user_name,
                user.profile
            FROM
                user
            WHERE
                user.user_id = :user_id
        SQL;

        $params = [
            ':user_id' => [$user_id, PDO::PARAM_STR],
        ];

        $results = $this->executeAndFetchAll($sql, $params);

        if (count($results) !== 0) {
            return QueryResult::success(data: $results);
        } else {
            return QueryResult::alert(message: 'ユーザーは存在しませんでした。');
        }
    }
}
```

## DataManagerの基本メソッド

DataManagerは、以下の2つの基本メソッドを提供します。

### executeStatement()
SQLを実行し、PDOStatementオブジェクトを返します。
INSERT、UPDATE、DELETEなど、結果を取得しない処理で使用します。

```php
protected function executeStatement(string $sql, array $params): PDOStatement
```

**使用例:**
```php
public function createUser(string $user_id, string $password): QueryResult
{
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = <<<SQL
        INSERT INTO user(
            user_id,
            password,
            on_create
        )
        VALUES(
            :user_id,
            :password,
            CURRENT_TIMESTAMP()
        );
    SQL;

    $params = [
        ':user_id' => [$user_id, PDO::PARAM_STR],
        ':password' => [$hashed_password, PDO::PARAM_STR],
    ];

    $this->executeStatement($sql, $params);
    return QueryResult::success();
}
```

### executeAndFetchAll()
SQLを実行し、結果を連想配列の配列として返します。
SELECTなど、結果を取得する処理で使用します。

```php
protected function executeAndFetchAll(string $sql, array $params): array
```

**戻り値の形式:**
```php
[
    ['user_id' => '1', 'user_name' => 'Alice', 'email' => 'alice@example.com'],
    ['user_id' => '2', 'user_name' => 'Bob', 'email' => 'bob@example.com'],
    // ...
]
```

**使用例:**
```php
public function getUsers(int $page, int $count): QueryResult
{
    $sql = <<<SQL
        SELECT
            user.user_id,
            user.user_name,
            user.email
        FROM
            user
        ORDER BY
            user.user_id
        LIMIT
            :page,
            :count
    SQL;

    $params = [
        ':page' => [$page, PDO::PARAM_INT],
        ':count' => [$count, PDO::PARAM_INT],
    ];

    $results = $this->executeAndFetchAll($sql, $params);
    return QueryResult::success('取得に成功しました。', $results);
}
```

## パラメータのバインド

SQLのプレースホルダにパラメータをバインドする際は、以下の形式で配列を作成します。

```php
$params = [
    ':placeholder' => [値, PDO型定数],
];
```

### PDO型定数

- **PDO::PARAM_STR** - 文字列型
- **PDO::PARAM_INT** - 整数型
- **PDO::PARAM_BOOL** - 真偽値型
- **PDO::PARAM_NULL** - NULL型

**例:**
```php
$params = [
    ':user_id' => [$user_id, PDO::PARAM_STR],
    ':age' => [$age, PDO::PARAM_INT],
    ':is_active' => [$is_active, PDO::PARAM_BOOL],
];
```

## QueryResultクラス

DataManagerのメソッドは、`QueryResult`クラスのインスタンスを返すことを推奨します。
QueryResultは、処理の成否、メッセージ、データを含む結果オブジェクトです。

### QueryResultの生成

#### 成功時
```php
// データなし
return QueryResult::success();

// メッセージ指定
return QueryResult::success('ユーザーの作成に成功しました。');

// データあり
return QueryResult::success(data: $results);

// メッセージとデータ両方
return QueryResult::success('取得に成功しました。', $results);
```

#### 警告時(データが見つからないなど)
```php
return QueryResult::alert('ユーザーは存在しませんでした。');
```

#### エラー時
```php
return QueryResult::error('データベースエラーが発生しました。');
```

### QueryResultの使用

```php
// ControllerやAPIで使用
$result = $userManager->getUser($user_id);

if ($result->isSucceed()) {
    // 成功時の処理
    $user_data = $result->getData();
    // ...
} else {
    // 失敗時の処理
    $error_message = $result->getErrorMessage();
    FlashMessage::alert($error_message);
    // ...
}
```

### QueryResultの主要メソッド

- **isSucceed()**: 処理が成功したか判定 (bool)
- **getErrorMessage()**: エラーメッセージを取得 (string|null)
- **getCodeName()**: 終了状態の名前を取得 (string)
- **data**: 処理結果のデータ (public プロパティ)

### 重要: QueryResultのdataプロパティへのアクセス

`QueryResult` クラスにはデータ取得用のメソッド `getData()` があります。

```php
// getData() でデータを取得
$result = $userManager->getUser($user_id);
if ($result->isSucceed()) {
    $user = $result->getData()[0]; // getData() でデータを取得し、[0]で最初の行
}
```

### 重要: データは配列の配列として扱う

`executeAndFetchAll()` は常に配列の配列を返します。
単一行を取得する場合でも、`data[0]` で最初の要素を取得する必要があります。

```php
// ユーザーを1件取得する場合
$result = $userManager->getUser($user_id);
if ($result->isSucceed()) {
    $user = $result->data[0]; // ← [0] で最初の行を取得
    echo $user['user_name'];
}

// 複数件取得する場合
$result = $userManager->getUsers(0, 100);
if ($result->isSucceed()) {
    $users = $result->data; // ← 配列の配列
    foreach ($users as $user) {
        echo $user['user_name'];
    }
}
```

## 実践的な例

### ユーザーの作成

```php
public function createUser(string $user_id, string $password): QueryResult
{
    // すでに同じユーザーIDが存在する場合はエラー
    if($this->getUser($user_id)->isSucceed() !== false) {
        return QueryResult::alert('同じIDのユーザーが存在します。');
    }

    // パスワードを暗号化
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = <<<SQL
        INSERT INTO user(
            user_id,
            password,
            on_create,
            on_update
        )
        VALUES(
            :user_id,
            :password,
            CURRENT_TIMESTAMP(),
            CURRENT_TIMESTAMP()
        );
    SQL;

    $params = [
        ':user_id' => [$user_id, PDO::PARAM_STR],
        ':password' => [$hashed_password, PDO::PARAM_STR],
    ];

    $this->executeStatement($sql, $params);
    return QueryResult::success();
}
```

### ユーザーの取得(単一)

```php
public function getUser(
    string $user_id,
    bool $include_deleted = false
): QueryResult
{
    $where_clause = '';
    if (!$include_deleted) {
        $where_clause = 'AND user.is_deleted <> 1';
    }

    $sql = <<<SQL
        SELECT
            user.user_id,
            user.user_name,
            user.email,
            user.profile
        FROM
            user
        WHERE
            user.user_id = :user_id
            {$where_clause}
    SQL;

    $params = [
        ':user_id' => [$user_id, PDO::PARAM_STR],
    ];

    $results = $this->executeAndFetchAll($sql, $params);

    if (count($results) !== 0) {
        return QueryResult::success(data: $results);
    } else {
        return QueryResult::alert(message: 'ユーザーは存在しませんでした。');
    }
}
```

### ユーザーの一覧取得(ページネーション)

```php
public function getUsers(
    int $page,
    int $count,
    bool $include_deleted = false
): QueryResult
{
    $where_clause = '';
    if (!$include_deleted) {
        $where_clause = 'user.is_deleted <> 1';
    }

    $sql = <<<SQL
        SELECT
            user.user_id,
            user.user_name,
            user.email
        FROM
            user
        WHERE
            {$where_clause}
        ORDER BY
            user.user_id
        LIMIT
            :page,
            :count
    SQL;

    $params = [
        ':page' => [$page, PDO::PARAM_INT],
        ':count' => [$count, PDO::PARAM_INT],
    ];

    $results = $this->executeAndFetchAll($sql, $params);
    return QueryResult::success('取得に成功しました。', $results);
}
```

### ユーザーの更新

```php
public function updateUser(
    string $user_id,
    string $user_name,
    string $profile
): QueryResult
{
    $sql = <<<SQL
        UPDATE user
        SET
            user_name = :user_name,
            profile = :profile,
            on_update = CURRENT_TIMESTAMP()
        WHERE
            user_id = :user_id
        ;
    SQL;

    $params = [
        ':user_id' => [$user_id, PDO::PARAM_STR],
        ':user_name' => [$user_name, PDO::PARAM_STR],
        ':profile' => [$profile, PDO::PARAM_STR],
    ];

    $this->executeStatement($sql, $params);
    return QueryResult::success();
}
```

### ユーザーの削除(論理削除)

```php
public function deleteUser(string $user_id): QueryResult
{
    $sql = <<<SQL
        UPDATE user
        SET
            is_deleted = :is_deleted,
            on_update = CURRENT_TIMESTAMP()
        WHERE
            user_id = :user_id
        ;
    SQL;

    $params = [
        ':user_id' => [$user_id, PDO::PARAM_STR],
        ':is_deleted' => [true, PDO::PARAM_BOOL],
    ];

    $this->executeStatement($sql, $params);
    return QueryResult::success();
}
```

## ControllerやAPIでの使用方法

### Controllerでの使用例

```php
// /app/controller/UserRegister.php
namespace ayutenn\skeleton\app\controller;

use ayutenn\core\requests\Controller;
use ayutenn\core\session\FlashMessage;
use ayutenn\core\database\DbConnector;
use ayutenn\skeleton\app\database\UserManager;

class UserRegister extends Controller
{
    protected array $RequestParameterFormat = [
        'user-id' => ['name' => 'ユーザーID', 'format' => 'user_id'],
        'password' => ['name' => 'パスワード', 'format' => 'password'],
    ];

    protected string $redirectUrlWhenError = '/register-form';

    public function main(): void
    {
        $user_id = $this->parameter['user-id'];
        $password = $this->parameter['password'];

        // DataManagerのインスタンス化
        $pdo = DbConnector::connectWithPdo();
        $userManager = new UserManager($pdo);

        // ユーザー作成
        $result = $userManager->createUser($user_id, $password);

        if ($result->isSucceed()) {
            FlashMessage::info('ユーザー登録が完了しました！');
            $this->redirect('/register-complete');
        } else {
            FlashMessage::alert($result->getErrorMessage());
            $this->redirect($this->redirectUrlWhenError);
        }
        return;
    }
}

return new UserRegister;
```

### APIでの使用例

```php
// /app/api/GetUserApi.php
namespace ayutenn\skeleton\app\api;

use ayutenn\core\requests\Api;
use ayutenn\core\database\DbConnector;
use ayutenn\skeleton\app\database\UserManager;

class GetUserApi extends Api
{
    protected array $RequestParameterFormat = [
        'user-id' => ['name' => 'ユーザーID', 'format' => 'user_id'],
    ];

    public function main(): array
    {
        $user_id = $this->parameter['user-id'];

        // DataManagerのインスタンス化
        $pdo = DbConnector::connectWithPdo();
        $userManager = new UserManager($pdo);

        // ユーザー取得
        $result = $userManager->getUser($user_id);

        if ($result->isSucceed()) {
            return $this->createResponse(true, [
                'user' => $result->data[0] ?? null
            ]);
        } else {
            return $this->createResponse(false, [
                'error' => $result->getErrorMessage()
            ]);
        }
    }
}

return new GetUserApi();
```

## データベース接続

`DbConnector::connectWithPdo()`でPDO接続を取得できます。
この接続はシングルトンパターンで管理され、同じリクエスト内では同じ接続が再利用されます。

```php
$pdo = DbConnector::connectWithPdo();
$userManager = new UserManager($pdo);
```

### 接続設定

データベース接続設定は、`/config/config.json`で定義します。

```json
{
    "PDO_DSN": "mysql:host=localhost;dbname=mydb;charset=utf8mb4",
    "PDO_USERNAME": "root",
    "PDO_PASSWORD": ""
}
```

## ベストプラクティス

### 1. テーブルごとにManagerを作成する
ユーザーテーブルには`UserManager`、投稿テーブルには`PostManager`など、テーブルごとに専用のManagerクラスを作成しましょう。

### 2. 戻り値は必ずQueryResultにする
メソッドの戻り値は常に`QueryResult`にすることで、呼び出し側で統一的なエラーハンドリングができます。

### 3. SQLインジェクション対策を徹底する
必ずプリペアドステートメントを使用し、パラメータは`$params`配列でバインドしてください。
直接SQLに値を埋め込むことは絶対に避けてください。

```php
// ❌ 危険: SQLインジェクションの可能性
$sql = "SELECT * FROM user WHERE user_id = '{$user_id}'";

// ✅ 安全: プリペアドステートメント
$sql = "SELECT * FROM user WHERE user_id = :user_id";
$params = [':user_id' => [$user_id, PDO::PARAM_STR]];
```

### 4. 動的なWHERE句は慎重に扱う
条件が動的に変わる場合は、WHERE句を文字列結合で組み立てますが、プレースホルダは必ず使用してください。

```php
$where_conditions = [];
$params = [];

if ($min_age !== null) {
    $where_conditions[] = 'user.age >= :min_age';
    $params[':min_age'] = [$min_age, PDO::PARAM_INT];
}

if ($max_age !== null) {
    $where_conditions[] = 'user.age <= :max_age';
    $params[':max_age'] = [$max_age, PDO::PARAM_INT];
}

$where_clause = !empty($where_conditions)
    ? 'WHERE ' . implode(' AND ', $where_conditions)
    : '';

$sql = "SELECT * FROM user {$where_clause}";
```

### 5. エラーハンドリングを適切に行う
データが見つからない場合は`alert`、システムエラーは`error`を使い分けましょう。

```php
// データが見つからない(ユーザーの入力ミスなど)
if (count($results) === 0) {
    return QueryResult::alert('ユーザーが見つかりませんでした。');
}

// システムエラー(データベース接続エラーなど)
catch (Exception $e) {
    return QueryResult::error('データベースエラーが発生しました。');
}
```

## まとめ

DataManagerは以下の役割を持ちます:

- データベースへの安全なアクセス
- SQLの実行とデータの取得
- QueryResultによる統一的な結果管理
- PDO接続の管理

これらの機能により、安全で保守性の高いデータベース操作を実現できます。
