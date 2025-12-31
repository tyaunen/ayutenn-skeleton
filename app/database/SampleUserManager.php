<?php
namespace ayutenn\skeleton\app\database;

use ayutenn\core\database\DataManager;
use ayutenn\core\database\QueryResult;
use PDO;

class SampleUserManager extends DataManager
{
    /**
     * ユーザー作成
     *
     * @param string $user_id
     * @param string $user_name
     * @param string $password
     * @return QueryResult
     */
    public function createUser(
        string $user_id,
        string $user_name,
        string $password
    ): QueryResult
    {
        // すでに同じユーザIDでユーザが存在する場合はエラー
        if($this->getUser($user_id)->isSucceed() !== false) {
            return QueryResult::alert('同じIDのユーザーが存在します。');
        }

        // パスワードを暗号化
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = <<<SQL
            INSERT INTO user(
                user_id,
                user_name,
                profile,
                password,
                last_login,
                on_create,
                on_update,
                is_deleted
            )
            VALUES(
                :user_id,
                :user_name,
                '',
                :password,
                CURRENT_TIMESTAMP,
                CURRENT_TIMESTAMP,
                CURRENT_TIMESTAMP,
                0
            );
        SQL;

        $params = [
            ':user_id' => [$user_id, PDO::PARAM_STR],
            ':user_name' => [$user_name, PDO::PARAM_STR],
            ':password' => [$hashed_password, PDO::PARAM_STR],
        ];

        $this->executeStatement($sql, $params);
        return QueryResult::success();
    }

    /**
     * ユーザー取得
     *
     * @param string $user_id
     * @param bool $include_deleted_user
     * @return QueryResult
     */
    public function getUser(
        string $user_id,
        bool $include_deleted_user = false
    ): QueryResult
    {
        $where_clause = '';
        if (!$include_deleted_user) {
            $where_clause = 'and user.is_deleted <> 1';
        }

        $sql = <<<SQL
            SELECT
                user.*
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

    /**
     * ユーザー一覧取得
     *
     * @param integer $page
     * @param integer $count
     * @param bool $include_deleted_user
     * @return QueryResult
     */
    public function getUsers(
        int $page,
        int $count,
        bool $include_deleted_user = false
    ): QueryResult
    {
        $where_clause = '';
        if (!$include_deleted_user) {
            $where_clause = 'user.is_deleted <> 1';
        }

        $sql = <<<SQL
            SELECT
                user.*
            FROM
                user
            WHERE
                $where_clause
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


    public function updateUser(
        string $user_id,
        string $user_name,
        string $user_profile,
    ): QueryResult
    {
        $sql = <<<SQL
            UPDATE user
            SET
                user_name     = :user_name,
                profile  = :user_profile,
                on_update     = CURRENT_TIMESTAMP
            WHERE
                user_id = :user_id
            ;
        SQL;

        $params = [
            ':user_id' => [$user_id, PDO::PARAM_STR],
            ':user_name' => [$user_name, PDO::PARAM_STR],
            ':user_profile' => [$user_profile, PDO::PARAM_STR],
        ];

        $this->executeStatement($sql, $params);
        return QueryResult::success();
    }

    public function deleteUser(string $user_id): QueryResult
    {
        $sql = <<<SQL
            UPDATE user
            SET
                is_deleted = :is_deleted
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
}