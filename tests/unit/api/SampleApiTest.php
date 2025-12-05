<?php
namespace ayutenn\core\tests\api;

use PHPUnit\Framework\TestCase;
use ayutenn\skeleton\app\api\SampleApi;
use ayutenn\core\config\Config;
use ayutenn\core\database\DbConnector;
use ayutenn\core\utils\Redirect;
use PDO;

class SampleApiTest extends TestCase
{
    private ?PDO $pdo = null;

    protected function setUp(): void
    {
        // Configのベースディレクトリを設定（空のconfig.jsonがあるディレクトリを使用）
        $configDir = realpath(__DIR__ . '/../database/config');
        Config::reset($configDir);

        // SQLiteインメモリDBを使用
        $dsn = 'sqlite::memory:';

        // DbConnectorのリセット
        $this->resetDbConnection();

        // Configを書き換え
        Config::setConfigForUnitTest('config', 'PDO_DSN', $dsn);
        Config::setConfigForUnitTest('config', 'PDO_USERNAME', '');
        Config::setConfigForUnitTest('config', 'PDO_PASSWORD', '');

        // MODEL_DIRを設定（RequestValidatorで使用）
        $modelDir = realpath(__DIR__ . '/../../../app/model');
        Config::setConfigForUnitTest('app', 'MODEL_DIR', $modelDir);

        // DOCUMENT_ROOTを空にする
        $_SERVER['DOCUMENT_ROOT'] = '';

        $this->pdo = DbConnector::connectWithPdo();

        // テーブル作成
        $this->createTables();

        // Redirectをテストモードに
        \ayutenn\core\utils\Redirect::$isTest = true;
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
        $_GET = [];
        $_POST = [];
        Redirect::$isTest = false;
    }

    public function testGetUserSuccess()
    {
        // テストデータ投入
        $sql = "INSERT INTO user (user_id, user_name, password) VALUES ('testuser', 'Test User', 'hash')";
        $this->pdo->exec($sql);

        // リクエストパラメータ設定
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['user-id' => 'testuser'];

        $api = new SampleApi();
        $api->run();

        $response = Redirect::$lastApiResponse;

        $this->assertEquals(0, $response['status']);
        $this->assertEquals('Test User', $response['payload']['user_name']);
        $this->assertArrayNotHasKey('user', $response['payload']);
    }

    public function testGetUserNotFound()
    {
        // リクエストパラメータ設定
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST = ['user-id' => 'unknown'];

        $api = new SampleApi();
        $api->run();

        $response = Redirect::$lastApiResponse;

        $this->assertEquals(9, $response['status']);
        $this->assertStringContainsString('ユーザーは存在しません', $response['payload']['message']);
    }
}
