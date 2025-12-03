<?php
require_once '../../core/session/AlertsSession.php';
require_once '../../core/requests/Controller.php';
require_once '../../app/helper/Auth.php';

class Logout extends Controller{

    const funcName = 'Logout';

    protected bool $remainRequestParameter = false;
    protected array $RequestParameterFormat = [];
    protected string $redirectUrlWhenError = '?logout';

    public function run(): void
    {
        Auth::logout();
        AlertsSession::putInfoMessageIntoSession('ログアウトしました。');
        $this->redirect('?logout');
    }
}
