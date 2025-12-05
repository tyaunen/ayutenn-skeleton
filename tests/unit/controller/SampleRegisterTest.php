<?php
namespace ayutenn\core\tests\routing;

use PHPUnit\Framework\TestCase;
use ayutenn\skeleton\app\controller\SampleRegister;
use ayutenn\core\config\Config;
use ayutenn\core\database\DbConnector;
use ayutenn\core\utils\Redirect;
use ayutenn\core\session\AlertsSession;
use PDO;

class SampleRegisterTest extends TestCase
{
    private ?PDO $pdo = null;

    protected function setUp(): void
    {
        // DOCUMENT_ROOTを空にする（Model.phpでのパス結合対策）
        $_SERVER['DOCUMENT_ROOT'] = '';

        // セッションの初期化
        $_SESSION = [];

        // Configのベースディレクトリを設定
        // テストデータはマニュアル設定するので、
        // 空のconfig.json, app.jsonファイルさえあればなんでもよい
        $configDir = realpath(__DIR__ . '/config');
        Config::reset($configDir);

        // SQLiteインメモリDBを使用
        $dsn = 'sqlite::memory:';
        $user = '';
        $pass = '';

        // DbConnectorのリセット（リフレクションを使用）
        $this->resetDbConnection();

        // Configを書き換え
        Config::setConfigForUnitTest('config', 'PDO_DSN', $dsn);
        Config::setConfigForUnitTest('config', 'PDO_USERNAME', $user);
        Config::setConfigForUnitTest('config', 'PDO_PASSWORD', $pass);

        // app.jsonのPATH_ROOTを設定（Redirectで使用）
        Config::setConfigForUnitTest('app', 'PATH_ROOT', '/');

        // MODEL_DIRを設定（RequestValidatorで使用）
        $modelDir = realpath(__DIR__ . '/../../../app/model');
        Config::setConfigForUnitTest('app', 'MODEL_DIR', $modelDir);

        $this->pdo = DbConnector::connectWithPdo();

        // テーブル作成
        $this->createTables();

        // Redirectをテストモードに
        Redirect::$isTest = true;
        Redirect::$lastRedirectUrl = '';
    }

    private function resetDbConnection(): void
    {
        $reflection = new \ReflectionClass(DbConnector::class);
        $property = $reflection->getProperty('connection');
        $property->setValue(null);
    }

    private function createTables(): void
    {
        // SQLite互換のDDL
        $sql = <<<SQL
            CREATE TABLE `user` (
              `user_id` TEXT NOT NULL,
              `user_name` TEXT NOT NULL,
              `profile` TEXT,
              `password` TEXT NOT NULL,
              `last_login` TEXT,
              `on_create` TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `on_update` TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `is_deleted` INTEGER NOT NULL DEFAULT 0,
              PRIMARY KEY (`user_id`)
            );
        SQL;
        $this->pdo->exec($sql);
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
        $this->resetDbConnection();
        Redirect::$isTest = false;
        $_SESSION = [];
        $_POST = [];
    }

    public function test_通常のユーザー登録()
    {
        // POSTデータをセット
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'user-id' => 'newuser',
            'user-name' => 'New User',
            'password' => 'password123',
            'password-confirm' => 'password123',
        ];

        $controller = new SampleRegister();
        $controller->run();

        // リダイレクト先の確認
        $this->assertEquals('/', Redirect::$lastRedirectUrl);

        // DBに登録されているか確認
        $stmt = $this->pdo->query("SELECT * FROM user WHERE user_id = 'newuser'");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotFalse($user);
        $this->assertEquals('newuser', $user['user_id']);
        $this->assertEquals('New User', $user['user_name']);

        // セッションメッセージの確認
        $alerts = AlertsSession::getAlerts();
        $this->assertNotEmpty($alerts);
        $this->assertEquals('info', $alerts[0]['alert_type']);
        $this->assertEquals('ユーザー登録が完了しました！', $alerts[0]['text']);
    }

    public function test_パスワード不一致()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'user-id' => 'newuser',
            'user-name' => 'New User',
            'password' => 'password123',
            'password-confirm' => 'password456', // 不一致
        ];

        $controller = new SampleRegister();
        $controller->run();

        // リダイレクト先の確認（エラー時は /sample-register）
        $this->assertEquals('/sample-register', Redirect::$lastRedirectUrl);

        // DBに登録されていないことを確認
        $stmt = $this->pdo->query("SELECT * FROM user WHERE user_id = 'newuser'");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($user);

        // セッションメッセージの確認
        $alerts = AlertsSession::getAlerts();
        $this->assertNotEmpty($alerts);
        $this->assertEquals('alert', $alerts[0]['alert_type']);
        $this->assertEquals('パスワードが一致しません。', $alerts[0]['text']);
    }

    public function test_同じユーザーIDがすでに存在する()
    {
        // 事前にユーザーを作成
        $sql = "INSERT INTO user (user_id, user_name, password) VALUES ('existing', 'Existing User', 'hash')";
        $this->pdo->exec($sql);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = [
            'user-id' => 'existing', // 重複ID
            'user-name' => 'New User',
            'password' => 'password123',
            'password-confirm' => 'password123',
        ];

        $controller = new SampleRegister();
        $controller->run();

        // リダイレクト先の確認
        $this->assertEquals('/sample-register', Redirect::$lastRedirectUrl);

        // セッションメッセージの確認
        $alerts = AlertsSession::getAlerts();
        $this->assertNotEmpty($alerts);
        $this->assertEquals('alert', $alerts[0]['alert_type']);
        $this->assertStringContainsString('同じIDのユーザーが存在します', $alerts[0]['text']);
    }
}
