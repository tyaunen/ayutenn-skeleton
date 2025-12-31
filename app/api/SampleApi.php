<?php
/**
 * ============================================
 * サンプルAPI: SampleApi
 * ============================================
 *
 * このファイルはayutennフレームワークのAPIの実装例です。
 * 新しいAPIを作成する際の参考にしてください。
 *
 * 【ポイント】
 * - Apiを継承する
 * - RequestParameterFormatでバリデーションルールを定義（任意）
 * - main()でJSON形式のレスポンスを返す
 * - createResponse(bool, array)で標準形式のレスポンスを作成
 * - ファイル末尾で return new ClassName(); を忘れずに
 */
namespace ayutenn\skeleton\app\api;

use ayutenn\core\requests\Api;
use ayutenn\core\database\DbConnector;
use ayutenn\skeleton\app\database\SampleUserManager;

class SampleApi extends Api
{
    /**
     * リクエストパラメータのバリデーションルール
     * 空の場合はバリデーションなし
     *
     * 以下のように定義します。
     * 'リクエストパラメタのキー' => ['name' => 'エラー時に表示される項目名', 'format' => '/model中のモデルファイル名(.jsonは省略)']
     *
     * モデルファイルがない場合、新規作成する必要があります。
     */
    protected array $RequestParameterFormat = [
        'user-id' => ['name' => 'ユーザーID', 'format' => 'user_id'],
    ];

    /**
     * APIのメイン処理
     * @return array createResponse()で作成したレスポンス配列
     */
    public function main(): array
    {
        $user_id = $this->parameter['user-id'];

        // ユーザー情報を取得
        $pdo = DbConnector::connectWithPdo();
        $manager = new SampleUserManager($pdo);
        $result = $manager->getUser($user_id);

        if ($result->isSucceed()) {
            // 成功時: ユーザー情報を返す
            // getUserの結果は配列の配列なので、最初の要素を取得
            $userData = $result->data[0];
            // ユーザー名のみを返す
            return $this->createResponse(true, ['user_name' => $userData['user_name']]);
        } else {
            // 失敗時: エラーメッセージを返す
            return $this->createResponse(false, ['message' => $result->getErrorMessage()]);
        }
    }
}

// 重要: ファイル末尾で必ずインスタンスを返す
return new SampleApi();
