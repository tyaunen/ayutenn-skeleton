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

class SampleApi extends Api
{
    /**
     * リクエストパラメータのバリデーションルール
     * 空の場合はバリデーションなし
     */
    protected array $RequestParameterFormat = [];

    /**
     * APIのメイン処理
     * @return array createResponse()で作成したレスポンス配列を返す
     */
    public function main(): array
    {
        // レスポンスデータを作成
        $data = [
            'message' => 'これはサンプルAPIです',
            'random_number' => mt_rand(0, 100),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // createResponse(成功/失敗, データ)でレスポンスを返す
        return $this->createResponse(true, $data);
    }
}

// 重要: ファイル末尾で必ずインスタンスを返す
return new SampleApi();
