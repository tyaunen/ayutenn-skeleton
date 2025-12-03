<?php
namespace ayutenn\skeleton\app\controller\session;

use ayutenn\core\requests\Controller;
use ayutenn\core\session\AlertsSession;
use ayutenn\skeleton\app\helper\Auth;

class Logout extends Controller{

    public static function name(): string { return 'logout'; }

    public function main(): void
    {
        Auth::logout();
        AlertsSession::putInfoMessageIntoSession('ログアウトしました。');
        $this->redirect('/');
        exit;
    }
}
return new Logout;
