<?php
namespace ayutenn\skeleton\app\controller\session;

use ayutenn\core\requests\Controller;
use ayutenn\core\utils\Logger;
use ayutenn\core\session\FlashMessage;
use ayutenn\skeleton\app\helper\Auth;

class Login extends Controller{

    protected bool $remainRequestParameter = true;
    protected array $RequestParameterFormat = [
        'user-id' => ['name' => 'ユーザーID', 'format' => 'user_id'],
        'password' => ['name' => 'パスワード', 'format' => 'password'],
    ];

    protected string $redirectUrlWhenError = '/login';

    public function main(): void
    {
        try {
            $user_id = $this->parameter['user-id'];
            $password = $this->parameter['password'];

            if(Auth::login($user_id, $password)){
                FlashMessage::info('ログインに成功しました！');
                self::unsetRemain();
                $this->redirect('/top');
                return;

            }else{
                FlashMessage::alert('IDかパスワードが違うみたいです。');
                $this->redirect($this->redirectUrlWhenError);
                return;
            }
        } catch (\Throwable $th) {
            $logger = Logger::setup(APP_ROOT . '/storage/log');
            $logger->debug("DB接続失敗:\r\n{$th}");

            FlashMessage::alert('DB接続に失敗しました。');

            $this->redirect($this->redirectUrlWhenError);
            return;
        }
    }
}
return new Login;
