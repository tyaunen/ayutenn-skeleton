<?php
/**
 * ============================================
 * サンプルコントローラー: SampleRegister
 * ============================================
 *
 * このファイルはayutennフレームワークのコントローラーの実装例です。
 * 新しいコントローラーを作成する際の参考にしてください。
 *
 * 【ポイント】
 * - Controllerを継承する
 * - RequestParameterFormatでバリデーションルールを定義
 * - main()で処理を実装し、最後にredirect()する
 * - ファイル末尾で return new ClassName(); を忘れずに
 */
namespace ayutenn\skeleton\app\controller;

use ayutenn\core\requests\Controller;
use ayutenn\core\session\FlashMessage;
use ayutenn\core\database\DbConnector;
use ayutenn\skeleton\app\database\SampleUserManager;

class SampleRegister extends Controller
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
        'user-name' => ['name' => 'ユーザー名', 'format' => 'user_name'],
        'password' => ['name' => 'パスワード', 'format' => 'password'],
        'password-confirm' => ['name' => 'パスワード(確認用)', 'format' => 'password'],
    ];

    /** バリデーションエラー時に入力値を保持する */
    protected bool $remainRequestParameter = true;

    /** エラー時のリダイレクト先 */
    protected string $redirectUrlWhenError = '/sample-register';

    /**
     * メイン処理
     * バリデーション成功後に自動的に呼び出される
     */
    public function main(): void
    {
        // バリデーション済みのパラメータを取得
        $user_id = $this->parameter['user-id'];
        $user_name = $this->parameter['user-name'];
        $password = $this->parameter['password'];
        $password_confirm = $this->parameter['password-confirm'];

        // パスワード一致確認（カスタムバリデーション例）
        if ($password !== $password_confirm) {
            FlashMessage::alert('パスワードが一致しません。');
            $this->redirect($this->redirectUrlWhenError);
            return;
        }

        try {
            // DataManagerのインスタンス化
            $pdo = DbConnector::connectWithPdo();
            $userManager = new SampleUserManager($pdo);

            // ユーザー作成
            $result = $userManager->createUser($user_id, $user_name, $password);

            if ($result->isSucceed()) {
                // 成功時: メッセージを設定してリダイレクト
                FlashMessage::info('ユーザー登録が完了しました！');
                self::unsetRemain(); // 保持していた入力値をクリア
                $this->redirect('/');
                return;
            } else {
                // 失敗時: エラーメッセージを設定してリダイレクト
                FlashMessage::alert($result->getErrorMessage());
                $this->redirect($this->redirectUrlWhenError);
                return;
            }

        } catch (\Throwable $th) {
            // 例外時の処理
            FlashMessage::alert('ユーザー登録に失敗しました。');
            $this->redirect($this->redirectUrlWhenError);
            return;

        }
    }
}

// 重要: ファイル末尾で必ずインスタンスを返す
return new SampleRegister();
