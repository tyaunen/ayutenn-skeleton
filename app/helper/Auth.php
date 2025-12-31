<?php
namespace ayutenn\skeleton\app\helper;

use ayutenn\core\database\DbConnector;
use ayutenn\skeleton\app\database\SampleUserManager;

class Auth
{
    /**
     * ログインチェックを行う
     * @param string $user_id ユーザーID
     * @param string $password パスワード
     */
    static function login($user_id, $password): bool
    {
        // PDO接続
        $pdo = DbConnector::connectWithPdo();
        $user_manager = new SampleUserManager($pdo);

        // ユーザーの存在チェック
        $result = $user_manager->getUser($user_id);
        if (!$result->isSucceed()) {
            return false;
        }

        // パスワード検証
        $user = $result->data[0];
        if (!password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION['AYLogin'] = [
            'id' => $user_id
        ];

        return true;
    }

    /**
     * ログアウト
     *
     * @return void
     */
    static function logout(): void
    {
        $_SESSION = [];
    }

    /**
     * ログインチェック
     *
     * @return boolean
     */
    static function isLogined(): bool
    {;
        return isset($_SESSION['AYLogin']);
    }

    /**
     * セッションからユーザー情報取得
     *
     * @return array
     */
    static function getLoginUser(): array
    {
        return $_SESSION['AYLogin'];
    }
}