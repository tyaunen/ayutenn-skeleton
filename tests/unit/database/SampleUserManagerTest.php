<?php
namespace ayutenn\core\tests\database;

use PHPUnit\Framework\TestCase;
use ayutenn\skeleton\app\database\SampleUserManager;
use ayutenn\core\config\Config;
use ayutenn\core\database\DbConnector;
use PDO;

class SampleUserManagerTest extends TestCase
{
    private ?PDO $pdo = null;

    protected function setUp(): void
    {
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

        $this->pdo = DbConnector::connectWithPdo();

        // テーブル作成
        $this->createTables();
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
    }

    public function testCreateUser()
    {
        $manager = new SampleUserManager($this->pdo);
        $result = $manager->createUser('testuser', 'Test User', 'password123');

        $this->assertTrue($result->isSucceed());

        // DBに保存されているか確認
        $stmt = $this->pdo->query("SELECT * FROM user WHERE user_id = 'testuser'");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($user);
        $this->assertEquals('testuser', $user['user_id']);
        $this->assertEquals('Test User', $user['user_name']);
        $this->assertTrue(password_verify('password123', $user['password']));
    }

    public function testGetUser()
    {
        $manager = new SampleUserManager($this->pdo);
        $manager->createUser('testuser', 'Test User', 'password123');

        $result = $manager->getUser('testuser');
        $this->assertTrue($result->isSucceed());

        // QueryResultのdataは配列の配列
        $userData = $result->data[0];
        $this->assertEquals('testuser', $userData['user_id']);
        $this->assertEquals('Test User', $userData['user_name']);
    }

    public function testUpdateUser()
    {
        $manager = new SampleUserManager($this->pdo);
        $manager->createUser('testuser', 'Test User', 'password123');

        $result = $manager->updateUser('testuser', 'Updated User', 'New Profile');
        $this->assertTrue($result->isSucceed());

        $result = $manager->getUser('testuser');
        $userData = $result->data[0];
        $this->assertEquals('Updated User', $userData['user_name']);
        $this->assertEquals('New Profile', $userData['profile']);
    }

    public function testDeleteUser()
    {
        $manager = new SampleUserManager($this->pdo);
        $manager->createUser('testuser', 'Test User', 'password123');

        $result = $manager->deleteUser('testuser');
        $this->assertTrue($result->isSucceed());

        // 通常取得では取得できないはず
        $result = $manager->getUser('testuser');
        $this->assertFalse($result->isSucceed());

        // 削除済み込みで取得できるか
        $result = $manager->getUser('testuser', true);
        $this->assertTrue($result->isSucceed());
        $userData = $result->data[0];
        $this->assertEquals(1, $userData['is_deleted']);
    }

    public function testDuplicateUser()
    {
        $manager = new SampleUserManager($this->pdo);
        $manager->createUser('testuser', 'Test User', 'password123');

        // 同じIDで作成しようとする
        $result = $manager->createUser('testuser', 'Another User', 'password456');

        $this->assertFalse($result->isSucceed());
        $this->assertEquals('【警告】 同じIDのユーザーが存在します。', $result->getErrorMessage());
    }
}
