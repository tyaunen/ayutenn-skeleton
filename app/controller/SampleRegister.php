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
 * - name()で一意の名前を返す
 * - RequestParameterFormatでバリデーションルールを定義
 * - main()で処理を実装し、最後にredirect()する
 * - ファイル末尾で return new ClassName(); を忘れずに
 */
namespace ayutenn\skeleton\app\controller;

use ayutenn\core\requests\Controller;
use ayutenn\core\session\AlertsSession;
use ayutenn\core\database\DbConnector;
use ayutenn\skeleton\app\database\UserManager;

class SampleRegister extends Controller
{
    /**
     * コントローラーの一意な名前を返す
     * フォーム入力値の一時保存などに使用される
     */
    public static function name(): string { return 'sample_register'; }

    /**
     * リクエストパラメータのバリデーションルール定義
     * 各パラメータに対してmodel/ディレクトリのJSONファイルを参照
     */
    protected array $RequestParameterFormat = [
        'user-id' => ['name' => 'ユーザーID', 'format' => 'user_id'],
        'user-name' => ['name' => 'ユーザー名', 'format' => 'user_name'],
        'password' => ['name' => 'パスワード', 'format' => 'password'],
        'password-confirm' => ['name' => 'パスワード(確認用)', 'format' => 'password'],
    ];

    /** バリデーションエラー時に入力値を保持する */
    protected bool $remainRequestParameter = true;

    /** 処理成功時のリダイレクト先 */
    protected string $redirectUrl = '/';

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
            AlertsSession::putAlertMessageIntoSession('パスワードが一致しません。');
            $this->redirect($this->redirectUrlWhenError);
            exit;
        }

        try {
            // DataManagerのインスタンス化
            $pdo = DbConnector::connectWithPdo();
            $userManager = new UserManager($pdo);

            // ユーザー作成
            $result = $userManager->createUser($user_id, $user_name, $password);

            if ($result->isSucceed()) {
                // 成功時: メッセージを設定してリダイレクト
                AlertsSession::putInfoMessageIntoSession('ユーザー登録が完了しました！');
                $this->unsetRemain(); // 保持していた入力値をクリア
                $this->redirect($this->redirectUrl);
            } else {
                // 失敗時: エラーメッセージを設定してリダイレクト
                AlertsSession::putAlertMessageIntoSession($result->getErrorMessage());
                $this->redirect($this->redirectUrlWhenError);
            }
            exit;

        } catch (\Throwable $th) {
            // 例外時の処理
            AlertsSession::putAlertMessageIntoSession('【エラー】ユーザー登録に失敗しました。');
            $this->redirect($this->redirectUrlWhenError);
            exit;
        }
    }
}

// 重要: ファイル末尾で必ずインスタンスを返す
return new SampleRegister();
