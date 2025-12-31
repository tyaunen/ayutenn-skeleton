<?php
use ayutenn\core\config\Config;
use ayutenn\core\session\FlashMessage;
use ayutenn\core\utils\CsrfTokenManager;
use ayutenn\skeleton\app\controller\session\Login;

// CSRFトークン生成
$csrf_manager = new CsrfTokenManager();
$csrf_token = $csrf_manager->getToken();

// Form Remain
$remain_params = Login::getRemainRequestParameter();
$user_id = $remain_params['user-id'] ?? '';

// フラッシュメッセージ取得
$session_messages = FlashMessage::getMessages();
$alert_messages = [];
$info_messages = [];

foreach ($session_messages as $msg) {
    if ($msg['alert_type'] === FlashMessage::ALERT) {
        $alert_messages[] = $msg['text'];
    } elseif ($msg['alert_type'] === FlashMessage::INFO) {
        $info_messages[] = $msg['text'];
    }
}
?>

<!DOCTYPE html>
<html lang="ja" data-bs-theme="dark" prefix="og: http://ogp.me/ns#">

<head>
    <title>ログイン <?= Config::get('APP_TITLE') ?></title>
    <?php require(__DIR__ . '/../components/flat/head.php'); ?>
    <style>
        .wrapper {
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            padding: 30px;
        }
        .login-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        .login-form h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #fff;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #ddd;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #444;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #007bff;
            background: rgba(255, 255, 255, 0.15);
        }
        .submit-btn {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .submit-btn:hover {
            background: #0056b3;
        }
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #ff6b6b;
        }
        .alert-info {
            background: rgba(13, 202, 240, 0.2);
            border: 1px solid #0dcaf0;
            color: #6dd5ed;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #aaa;
        }
        .register-link a {
            color: #007bff;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }
        }
    </style>
</head>

<body data-page-name='login'>
    <div class="wrapper">
        <main class="main-content">
            <div class="login-container">
                <div class="login-form">
                    <h1>ログイン</h1>

                    <?php if (!empty($alert_messages)): ?>
                        <?php foreach ($alert_messages as $message): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($info_messages)): ?>
                        <?php foreach ($info_messages as $message): ?>
                            <div class="alert alert-info">
                                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <form method="POST" action="./session/login">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

                        <div class="form-group">
                            <label for="user-id">ユーザーID</label>
                            <input
                                type="text"
                                id="user-id"
                                name="user-id"
                                value="<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="password">パスワード</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                            >
                        </div>

                        <button type="submit" class="submit-btn">ログイン</button>
                    </form>

                    <div class="register-link">
                        アカウントをお持ちでないですか? <a href="./register">新規登録</a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
