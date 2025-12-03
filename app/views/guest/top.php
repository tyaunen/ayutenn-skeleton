<?php
use ayutenn\core\config\Config;
use ayutenn\core\session\AlertsSession;

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
    <title>ayutenn <?= Config::getAppSetting('APP_TITLE') ?></title>
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
        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }
        }
    </style>
</head>

<body data-page-name='top'>
    <header class="main-header">
        header
    </header>

    <div class="wrapper">
        <?php require(__DIR__ . '/../compornents/sidebar.php'); ?>

        <main class="main-content">
            <div id="toast-container"></div>
            <main-content>
                <div class="content-block">
                    <h1>ayutenn setup ok</h1>

                    <div style="text-align: center; margin: 20px 0;">
                        <img src="./assets/img/common/icon.png" alt="Ayutenn Icon" style="max-width: 200px; height: auto;">
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

                    <p>
                        <a href="./register" class="btn btn-primary">ユーザー登録</a>
                    </p>
                </div>
            </main-content>
        </main>
    </div>
    <script>
        function test() {
            // axiosを使って、apiを叩くサンプル
            axios.get(
                './api/get/number', {}
            )
            .then(response => {
                console.log(response.data.payload);
            })
            .catch(function(error){
                console.log(error)
            });
        }
    </script>
</body>

</html>
