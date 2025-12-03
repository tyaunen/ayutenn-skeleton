<?php
require_once '../../core/session/AlertsSession.php';
require_once '../../core/requests/Controller.php';
require_once '../../app/helper/Auth.php';
require_once "../../app/helper/Logger.php";
require_once "../../core/database/DbConnector.php";
require_once "../../app/database/UserManager.php";

class Register extends Controller{

    const funcName = 'Regiter';

    protected bool $remainRequestParameter = true;

    protected array $RequestParameterFormat = [
        'user-id' => ['name' => 'ユーザーID', 'format' => 'user_id'],
        'password' => ['name' => 'パスワード', 'format' => 'password'],
        'password-double-check' => ['name' => 'パスワード', 'format' => 'password'],
        'chara-name' => ['name' => 'キャラクター名', 'format' => 'chara_name'],
        'chara-nickname' => ['name' => '呼び名', 'format' => 'chara_nickname'],
        'chara-kill-stance' => ['name' => '殺しのスタンス', 'format' => 'int_code'],
        'chara-hitokoto' => ['name' => '宴への意気込み', 'format' => 'chara_hitokoto'],
        'ruby-color' => ['name' => 'ルビーの色', 'format' => 'color_code'],
        'ruby-about' => ['name' => 'ルビーの色の特徴', 'format' => 'ruby_about'],
        'player-name' => ['name' => 'PL名', 'format' => 'player_name', 'require' => false],
        'player-text' => ['name' => 'PL連絡欄', 'format' => 'player_text', 'require' => false],
        'fa-ok' => ['name' => '二次創作OKフラグ', 'format' => 'boolean_flag'],
        'ss-ok' => ['name' => 'スクショOKフラグ', 'format' => 'boolean_flag'],
    ];

    protected string $redirectUrl = '/main';
    protected string $redirectUrlWhenError = '/register';

    public function run(): void
    {
        $user_id = $this->parameter['user-id'];
        $password = $this->parameter['password'];
        $password_double_check = $this->parameter['password-double-check'];

        $chara_name = $this->parameter['chara-name'];
        $chara_nickname = $this->parameter['chara-nickname'];
        $chara_kill_stance = $this->parameter['chara-kill-stance'];
        $chara_hitokoto = $this->parameter['chara-hitokoto'];
        $ruby_color = $this->parameter['ruby-color'];
        $ruby_about = $this->parameter['ruby-about'];
        $player_name = $this->parameter['player-name'];
        $player_text = $this->parameter['player-text'];
        $fa_ok_flag = $this->parameter['fa-ok'];
        $ss_ok_flag = $this->parameter['ss-ok'];

        try {
            // パスワード再入力チェック
            if($password !== $password_double_check){
                AlertsSession::putAlertMessageIntoSession(
                    "register_password_double_check",
                    "【エラー】パスワードが、再入力と一致していません。"
                );
                $this->redirect($this->redirectUrlWhenError);
                exit;
            }

            // PDO接続
            $connection = DbConnector::connectWithPdo();

            //ユーザマネージャー
            $user_manager = new UserManager($connection);
            $connection->beginTransaction();

            // ユーザー登録
            $result = $user_manager->createNewCharactor(
                $user_id,
                $chara_name,
                $chara_nickname,
                $chara_kill_stance,
                $chara_hitokoto,
                $ruby_color,
                $ruby_about,
                $player_name,
                $player_text,
                $fa_ok_flag,
                $ss_ok_flag,
                $password
            );

            // すでにユーザーが存在するなどで、登録に失敗した場合
            if($result->isSucceed === false) {
                // エラー情報をセッションに格納
                AlertsSession::putAlertMessageIntoSession(
                    "【エラー】同じIDのユーザーが既に存在します。"
                );
                DbConnector::rollbackWhenExistsTransaction($this->dbConnection ?? null);
                $this->redirect($this->redirectUrlWhenError);
            }

            // ログイン
            Auth::login($user_id, $password);

            $connection->commit();
            $this->unsetRemain();
            $this->redirect($this->redirectUrl);

        } catch (Throwable $th) {
            $logger = Logger::setup();
            $logger->debug("DB接続失敗:\r\n{$th}");

            AlertsSession::putAlertMessageIntoSession('【なんかへんです】DB接続に失敗しました。');

            $this->redirect($this->redirectUrlWhenError);
            exit;
        }
    }
}
