<?php
namespace ayutenn\skeleton\app\controller;

use ayutenn\core\requests\Controller;
use ayutenn\core\utils\Logger;
use ayutenn\core\session\AlertsSession;
use ayutenn\skeleton\app\helper\Auth;

class Login extends Controller{

    public static function name(): string { return 'login'; }

    protected bool $remainRequestParameter = true;
    protected array $RequestParameterFormat = [
        'user-id' => ['name' => 'ユーザーID', 'format' => 'user_id'],
        'password' => ['name' => 'パスワード', 'format' => 'password'],
    ];

    protected string $redirectUrl = '/top';
    protected string $redirectUrlWhenError = '/';

    public function main(): void
    {
        try {
            $user_id = $this->parameter['user-id'];
            $password = $this->parameter['password'];

            if(Auth::login($user_id, $password)){
                AlertsSession::putInfoMessageIntoSession('ログインに成功しました！');
                $this->unsetRemain();
                $this->redirect($this->redirectUrl);
                exit;

            }else{
                AlertsSession::putAlertMessageIntoSession('【エラー】IDかパスワードが違うみたいです。');
                $this->redirect($this->redirectUrlWhenError);
                exit;
            }
        } catch (\Throwable $th) {
            $logger = Logger::setup();
            $logger->debug("DB接続失敗:\r\n{$th}");

            AlertsSession::putAlertMessageIntoSession('【なんかへんです】DB接続に失敗しました。');

            $this->redirect($this->redirectUrlWhenError);
            exit;
        }
    }
}
