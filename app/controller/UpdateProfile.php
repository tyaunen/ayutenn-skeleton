<?php
namespace ayutenn\skeleton\app\controller;

use ayutenn\core\requests\Controller;
use ayutenn\core\session\AlertsSession;
use ayutenn\core\database\DbConnector;
use ayutenn\skeleton\app\database\UserManager;
use ayutenn\skeleton\app\helper\Auth;

class UpdateProfile extends Controller{

    public static function name(): string { return 'update_profile'; }

    protected bool $remainRequestParameter = true;
    protected array $RequestParameterFormat = [
        'user-name' => ['name' => 'ユーザー名', 'format' => 'user_name'],
        'profile' => ['name' => 'プロフィール', 'format' => 'all_ok'], // プロフィール文のバリデーションは緩めに
    ];

    protected string $redirectUrl = '/profile';
    protected string $redirectUrlWhenError = '/profile';

    public function main(): void
    {
        $login_user = Auth::getLoginUser();
        $user_id = $login_user['id'];
        $user_name = $this->parameter['user-name'];
        $profile = $this->parameter['profile'];

        $pdo = DbConnector::connectWithPdo();
        $user_manager = new UserManager($pdo);

        $result = $user_manager->updateUser($user_id, $user_name, $profile);

        if ($result->isSucceed()) {
            AlertsSession::putInfoMessageIntoSession('プロフィールを更新しました。');
            $this->unsetRemain();
            $this->redirect($this->redirectUrl);
        } else {
            AlertsSession::putAlertMessageIntoSession('プロフィールの更新に失敗しました。');
            $this->redirect($this->redirectUrlWhenError);
        }
    }
}
return new UpdateProfile;
