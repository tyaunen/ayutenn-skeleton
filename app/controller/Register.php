<?php
namespace ayutenn\skeleton\app\controller;

use ayutenn\core\requests\Controller;
use ayutenn\core\session\AlertsSession;
use ayutenn\core\database\DbConnector;
use ayutenn\skeleton\app\database\UserManager;

class Register extends Controller
{
    public static function name(): string { return 'register'; }

    protected array $RequestParameterFormat = [
        'user-id' => ['name' => 'ユーザーID', 'format' => 'user_id'],
        'user-name' => ['name' => 'ユーザー名', 'format' => 'user_name'],
        'password' => ['name' => 'パスワード', 'format' => 'password'],
        'password-confirm' => ['name' => 'パスワード(確認用)', 'format' => 'password'],
    ];

    protected bool $remainRequestParameter = true;
    protected string $redirectUrl = '/';
    protected string $redirectUrlWhenError = '/register';

    public function main(): void
    {
        $user_id = $this->parameter['user-id'];
        $user_name = $this->parameter['user-name'];
        $password = $this->parameter['password'];
        $password_confirm = $this->parameter['password-confirm'];

        // パスワード一致確認
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
                AlertsSession::putInfoMessageIntoSession('ユーザー登録が完了しました！');
                $this->unsetRemain();
                $this->redirect($this->redirectUrl);
            } else {
                AlertsSession::putAlertMessageIntoSession($result->getErrorMessage());
                $this->redirect($this->redirectUrlWhenError);
            }
            exit;

        } catch (\Throwable $th) {
            AlertsSession::putAlertMessageIntoSession('【エラー】ユーザー登録に失敗しました。');
            $this->redirect($this->redirectUrlWhenError);
            exit;
        }
    }
}

return new Register();
