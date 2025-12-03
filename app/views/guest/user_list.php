<?php
use ayutenn\core\config\Config;
use ayutenn\core\database\DbConnector;
use ayutenn\skeleton\app\database\UserManager;

// ユーザーデータ取得
$pdo = DbConnector::connectWithPdo();
$userManager = new UserManager($pdo);
// ページネーションは一旦省略し、全件取得（limit 100）
$result = $userManager->getUsers(0, 100);
$users = $result->isSucceed() ? $result->data : [];

?>

<!DOCTYPE html>
<html lang="ja" data-bs-theme="dark" prefix="og: http://ogp.me/ns#">

<head>
    <title>ユーザーリスト <?= Config::getAppSetting('APP_TITLE') ?></title>
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
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.05);
        }
        .user-table th, .user-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .user-table th {
            background: rgba(0, 0, 0, 0.2);
            color: #fff;
            font-weight: bold;
        }
        .user-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }
        }
    </style>
</head>

<body data-page-name='user_list'>
    <header class="main-header">
        <div class="container-fluid">
            <h2><?= Config::getAppSetting('APP_TITLE') ?></h2>
        </div>
    </header>

    <div class="wrapper">
        <?php require(__DIR__ . '/../components/sidebar.php'); ?>

        <main class="main-content">
            <h1>ユーザーリスト</h1>

            <?php if (empty($users)): ?>
                <p>ユーザーが見つかりませんでした。</p>
            <?php else: ?>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>名前</th>
                            <th>登録日時</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['user_id'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['user_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['on_create'], ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>
