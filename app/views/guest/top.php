<?php
use ayutenn\core\config\Config;

?>

<!DOCTYPE html>
<html lang="ja" data-bs-theme="dark" prefix="og: http://ogp.me/ns#">

<head>
    <title>ayutenn <?= Config::getAppSetting('APP_TITLE') ?></title>
    <?php require(__DIR__ . '/../compornents/flat/head.php'); ?>
</head>

<body data-page-name='top'>
    <header class="main-header">
        header
    </header>
    <main class="container">
        <div id="toast-container"></div>
        <main-content>
            <div class="content-block">
                <h1>ayutenn setup ok</h1>
            </div>
        </main-content>
    </main>
    <script>
        function test() {
            // axiosを使って、apiを叩く
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
