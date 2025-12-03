<?php
use ayutenn\core\config\Config;
use ayutenn\core\session\AlertsSession;
use ayutenn\core\utils\CsrfTokenManager;

// $user 変数は Profile コントローラーから渡される想定

// CSRFトークン生成
$csrf_manager = new CsrfTokenManager();
$csrf_token = $csrf_manager->getToken();

// アラートメッセージ取得
$session_messages = AlertsSession::getAlerts();
$alert_messages = [];
$info_messages = [];

foreach ($session_messages as $msg) {
    if ($msg['alert_type'] === AlertsSession::ALERT) {
        $alert_messages[] = $msg['text'];
    } elseif ($msg['alert_type'] === AlertsSession::INFO) {
        $info_messages[] = $msg['text'];
    }
}
?>

<!DOCTYPE html>
<html lang="ja" data-bs-theme="dark" prefix="og: http://ogp.me/ns#">

<head>
    <title>プロフィール <?= Config::getAppSetting('APP_TITLE') ?></title>
    <?php require(__DIR__ . '/../compornents/flat/head.php'); ?>
    <style>
        .wrapper {
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            padding: 30px;
        }
        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        .profile-form h1 {
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
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #444;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #007bff;
            background: rgba(255, 255, 255, 0.15);
        }
        .form-group textarea {
            height: 150px;
            resize: vertical;
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
        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }
        }
    </style>
</head>

<body data-page-name='profile'>
    <header class="main-header">
        <div class="container">
            <h2><?= Config::getAppSetting('APP_TITLE') ?></h2>
        </div>
    </header>

    <div class="wrapper">
        <?php require(__DIR__ . '/../compornents/sidebar.php'); ?>

        <main class="main-content">
            <div class="profile-container">
                <div class="profile-form">
                    <h1>プロフィール編集</h1>

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

                    <form method="POST" action="./profile/update">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

                        <div class="form-group">
                            <label>ユーザーID</label>
                            <input
                                type="text"
                                value="<?= htmlspecialchars($user['user_id'], ENT_QUOTES, 'UTF-8') ?>"
                                disabled
                                style="background: rgba(255, 255, 255, 0.05); color: #aaa;"
                            >
                        </div>

                        <div class="form-group">
                            <label for="user-name">ユーザー名</label>
                            <input
                                type="text"
                                id="user-name"
                                name="user-name"
                                value="<?= htmlspecialchars($user['user_name'], ENT_QUOTES, 'UTF-8') ?>"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="profile">プロフィール</label>
                            <textarea
                                id="profile"
                                name="profile"
                            ><?= htmlspecialchars($user['profile'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <button type="submit" class="submit-btn">更新</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
