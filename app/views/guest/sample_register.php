<?php
/**
 * ============================================
 * サンプルビュー: sample_register.php
 * ============================================
 *
 * このファイルはayutennフレームワークのビューの実装例です。
 * 新しいビューを作成する際の参考にしてください。
 *
 * 【ポイント】
 * - ビュー内で必要なデータを取得する
 * - CSRFトークンをフォームに含める
 * - Form Remainでエラー時の入力値を復元
 * - AlertsSessionでメッセージを表示
 * - 出力時はhtmlspecialchars()でエスケープ
 */
use ayutenn\core\config\Config;
use ayutenn\core\session\AlertsSession;
use ayutenn\core\utils\CsrfTokenManager;
use ayutenn\skeleton\app\controller\SampleRegister;

// CSRFトークン生成
$csrf_manager = new CsrfTokenManager();
$csrf_token = $csrf_manager->getToken();

// Form Remain: エラー時の入力値復元
$remain_params = SampleRegister::getRemainRequestParameter();
$user_id = $remain_params['user-id'] ?? '';
$user_name = $remain_params['user-name'] ?? '';

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
    <title>サンプル登録フォーム <?= Config::getAppSetting('APP_TITLE') ?></title>
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
        .register-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        .register-form h1 {
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
        .sample-note {
            background: rgba(255, 193, 7, 0.2);
            border: 1px solid #ffc107;
            color: #ffc107;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body data-page-name='sample-register'>
    <header class="main-header">
        <div class="container">
            <h2><?= Config::getAppSetting('APP_TITLE') ?></h2>
        </div>
    </header>

    <div class="wrapper">
        <?php require(__DIR__ . '/../components/sidebar.php'); ?>

        <main class="main-content">
            <div class="register-container">
                <div class="register-form">
                    <h1>サンプル登録フォーム</h1>

                    <div class="sample-note">
                        📝 これはサンプルビューです。フォーム処理の実装例として参考にしてください。
                    </div>

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

                    <!-- フォームにはCSRFトークンを必ず含める -->
                    <form method="POST" action="./sample-register">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

                        <div class="form-group">
                            <label for="user-id">ユーザーID</label>
                            <input
                                type="text"
                                id="user-id"
                                name="user-id"
                                value="<?= htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="3-16文字の英数字"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="user-name">ユーザー名</label>
                            <input
                                type="text"
                                id="user-name"
                                name="user-name"
                                value="<?= htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="表示名"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="password">パスワード</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="8文字以上の英数記号"
                                required
                            >
                        </div>

                        <div class="form-group">
                            <label for="password-confirm">パスワード(確認用)</label>
                            <input
                                type="password"
                                id="password-confirm"
                                name="password-confirm"
                                placeholder="もう一度入力してください"
                                required
                            >
                        </div>

                        <button type="submit" class="submit-btn">登録</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
