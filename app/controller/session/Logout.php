<?php
namespace ayutenn\skeleton\app\controller\session;

use ayutenn\core\requests\Controller;
use ayutenn\core\session\FlashMessage;
use ayutenn\skeleton\app\helper\Auth;

class Logout extends Controller{

    public function main(): void
    {
        Auth::logout();
        FlashMessage::info('ログアウトしました。');
        $this->redirect('/');
        return;
    }
}
return new Logout;
