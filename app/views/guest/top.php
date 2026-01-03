<?php
use ayutenn\core\config\Config;
use ayutenn\skeleton\app\views\components\FlashMessage;
?>

<!DOCTYPE html>
<html lang="ja" prefix="og: http://ogp.me/ns#">

<head>
    <title><?= Config::get('APP_TITLE') ?></title>
    <?php require(PROJECT_ROOT . '/app/views/components/flat/head.php'); ?>
</head>

<body data-page-name='top'>
    <?php FlashMessage::render(); ?>
    <main class="main-content">
        <main-content>
            <div class="hero">
                <div class="hero-content">
                    <h1><span class="text-accent">やった～</span></h1>
                    <p>ayutennの準備が整いました！</p>
                    <a href="https://github.com/tyaunen/ayutenn-skeleton" class="btn">GitHub</a>
                </div>
                <div class="hero-image">
                    <img src="./assets/img/common/icon.png" alt="Ayutenn Logo">
                </div>
            </div>
        </main-content>
    </main>
</body>
</html>
