# ユニットテストガイドライン

このドキュメントでは、skeletonプロジェクトにおけるユニットテストの実装方針と、よくあるトラブルへの対処法をまとめます。

## 基本方針

- **PHPUnit** を使用します。
- テストコードは `tests/unit/` 配下に配置します。
    - データベース関連: `tests/unit/database/`
    - コントローラー関連: `tests/unit/controller/`
    - API関連: `tests/unit/api/`
- 外部リソース（DB、APIなど）への依存を極力排除し、**SQLiteインメモリデータベース** やモック機能を活用してテストを行います。

## データベーステストの実装

`ayutenn/core/database/DataManager` を継承したクラスのテスト方法です。

### SQLiteインメモリデータベースの使用

MySQLではなく、SQLiteのインメモリデータベースを使用することで、高速かつ環境に依存しないテストを実現します。

1.  **Configの設定**: `setUp` メソッドで `Config::setConfigForUnitTest` を使用し、DSNを `sqlite::memory:` に書き換えます。
2.  **DbConnectorのリセット**: シングルトンパターンで実装されている `DbConnector` をリフレクションを使用してリセットし、新しい接続（SQLite）を確立させます。
3.  **テーブル作成**: SQLite互換の `CREATE TABLE` 文を実行して、テスト用テーブルを作成します。

**実装例:**

```php
protected function setUp(): void
{
    // Configのベースディレクトリを設定（空のconfig.jsonがあるディレクトリ）
    $configDir = realpath(__DIR__ . '/config');
    Config::reset($configDir);

    // SQLiteインメモリDBを使用
    $dsn = 'sqlite::memory:';

    // DbConnectorのリセット（リフレクションを使用）
    $this->resetDbConnection();

    // Configを書き換え
    Config::setConfigForUnitTest('config', 'PDO_DSN', $dsn);
    Config::setConfigForUnitTest('config', 'PDO_USERNAME', '');
    Config::setConfigForUnitTest('config', 'PDO_PASSWORD', '');

    $this->pdo = DbConnector::connectWithPdo();

    // テーブル作成（SQLite互換SQL）
    $this->createTables();
}

private function resetDbConnection(): void
{
    $reflection = new \ReflectionClass(DbConnector::class);
    $property = $reflection->getProperty('connection');
    $property->setValue(null);
}
```

### SQLの互換性

プロダクトコード内のSQLは、MySQLとSQLiteの両方で動作するように記述する必要があります。

- **NG**: `CURRENT_TIMESTAMP()` （MySQL固有）
- **OK**: `CURRENT_TIMESTAMP` （標準SQL、両方で動作）

## コントローラー・APIテストの実装

`ayutenn/core/requests/Controller` や `ayutenn/core/requests/Api` を継承したクラスのテスト方法です。

### Redirectのモック化

`Redirect` クラスのテストモードを使用することで、実際のリダイレクト（`header()` + `exit`）やAPIレスポンスの出力（`echo` + `exit`）を防ぎ、結果を検証できます。

```php
use ayutenn\core\utils\Redirect;

protected function setUp(): void
{
    // ...Config設定など...

    // Redirectをテストモードに
    Redirect::$isTest = true;
    Redirect::$lastRedirectUrl = '';
    Redirect::$lastApiResponse = [];
}

protected function tearDown(): void
{
    Redirect::$isTest = false;
    // ...
}
```

#### コントローラーのリダイレクト検証

```php
public function testRedirect()
{
    // ...コントローラー実行...

    // リダイレクト先の検証
    $this->assertEquals('/', Redirect::$lastRedirectUrl);
}
```

#### APIレスポンスの検証

APIクラスの `run()` メソッドは、通常 `json_encode` して `exit` しますが、テストモードでは `Redirect::$lastApiResponse` に結果が格納されます。
これにより、リフレクションを使用せずに `run()` メソッド（バリデーション含む）全体をテストできます。

```php
public function testApi()
{
    // ...パラメータ設定...
    $api = new SampleApi();
    $api->run();

    // レスポンスの検証
    $response = Redirect::$lastApiResponse;
    $this->assertEquals(0, $response['status']);
    $this->assertEquals('Expected Value', $response['payload']['key']);
}
```

### 必要な設定の注入

コントローラーやAPIは `Config` クラスを通じて設定値（`PATH_ROOT`、`MODEL_DIR` など）を参照します。これらを `Config::setConfigForUnitTest` で注入する必要があります。

```php
// app.jsonのPATH_ROOTを設定（Redirectで使用）
Config::setConfigForUnitTest('app', 'PATH_ROOT', '/');

// MODEL_DIRを設定（RequestValidatorで使用）
$modelDir = realpath(__DIR__ . '/../../../app/model');
Config::setConfigForUnitTest('app', 'MODEL_DIR', $modelDir);
```

### パス解決のトラブル対策

`Model` クラスなどで `$_SERVER['DOCUMENT_ROOT']` を使用している場合、CLI環境ではパスが正しく解決されないことがあります。テストの `setUp` で空文字に設定することで回避できます。

```php
$_SERVER['DOCUMENT_ROOT'] = '';
```

### exitの回避

コントローラー内で `exit` が使用されていると、テスト実行がそこで終了してしまいます。
テスト可能なコードにするため、`exit` の代わりに `return` を使用するか、構造を見直す必要があります。
`Redirect::redirect` は本番環境では `exit` しますが、テストモードでは `exit` しないため、その直後に `return` を記述することで、本番・テスト両方で意図した動作になります。

**修正前:**
```php
$this->redirect($url);
exit;
```

**修正後:**
```php
$this->redirect($url);
return;
```

## トラブルシューティング

### Q. `config.jsonファイルが見つかりませんでした` エラーが出る

**A.** `Config::reset($dir)` で指定したディレクトリに `config.json` が存在しません。
テスト用のディレクトリ（例: `tests/unit/database/config`）を作成し、そこに空の `config.json` を配置してください。

### Q. `app.jsonファイルが見つかりませんでした` エラーが出る

**A.** `Config::setConfigForUnitTest('app', ...)` を使用する場合、`app.json` が存在する必要があります。
`config.json` と同様に、テスト用のディレクトリに空の `app.json` を配置してください。

### Q. `modelファイルが見つかりませんでした` エラーが出る

**A.** `MODEL_DIR` の設定が間違っているか、`$_SERVER['DOCUMENT_ROOT']` が悪さをしています。
`Config::setConfigForUnitTest('app', 'MODEL_DIR', ...)` で正しい絶対パスを指定し、`$_SERVER['DOCUMENT_ROOT'] = ''` を設定してください。

### Q. テストが途中で止まる / 結果が出ない

**A.** テスト対象のコード内で `exit` や `die` が実行されています。
該当箇所を探し、`return` に置き換えるなどの修正を行ってください。
APIテストの場合は、`Redirect::$isTest = true` を設定し忘れていないか確認してください。
