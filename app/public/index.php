<?php
require_once(__DIR__ . '/../../vendor/autoload.php');
use ayutenn\core\config\Config;
use ayutenn\core\routing\Router;
use ayutenn\core\session\AlertsSession;
use ayutenn\core\utils\Uuid;
use ayutenn\core\utils\CsrfTokenManager;
use ayutenn\core\utils\Redirect;

/**
 * 【概要】
 * エントリーポイント
 *
 * 【解説】
 * ルーティングの初期化+ディスパッチ、セッションの開始、などのリクエスト単位での初期セットアップを行います。
 *
 * 【無駄口】
 * ログアウトまでの時間とかはcongig.jsonに書くべきかも
 *
 */
/*-------------------------------------------------
* ayutennの設定
--------------------------------------------------*/
Config::reset(__DIR__ . '/../../config');

// URLトップ
define('URL_ROOT', (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . Config::getAppSetting('PATH_ROOT'));
define('APP_ROOT', __DIR__ . '/../..');

/*-------------------------------------------------
* ログイン時間関係の設定
--------------------------------------------------*/
ini_set('session.gc_maxlifetime', 60*60*24*7);
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('display_errors', "On");
ini_set('error_reporting', E_ALL);

/*-------------------------------------------------
* セッション開始
--------------------------------------------------*/
$session_path = $_SERVER['DOCUMENT_ROOT'] . Config::getAppSetting('SESSION_DIR');
session_save_path($session_path);
session_set_cookie_params([
    'lifetime' => 60*60*24*7,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => !empty($_SERVER['HTTPS']),      // HTTPSの場合のみ送信
    'httponly' => true,    // JavaScriptからのアクセスを禁止
    'samesite' => 'Lax'
]);

session_start();
date_default_timezone_set('Asia/Tokyo');

/*-------------------------------------------------
* CSRFチェック
--------------------------------------------------*/
// POSTリクエストのときは常にcsrfが必要
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrf_manager = new CsrfTokenManager();
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!$csrf_manager->validateToken($submittedToken)) {
        // CSRFトークンの時間切れ
        http_response_code(403);
        AlertsSession::putErrorMessageIntoSession('タイムアウトです。ページを開いてからデータを送信するまで時間がかかりすぎたかもしれません。');
        Redirect::redirect(URL_ROOT);
    } else {
        // トークンの最終アクセス時刻を更新
        $csrf_manager->getToken();
    }
}

// ログとかに使うID
$_SESSION['AY_ACCESS_KEY'] = Uuid::generateUuid7();

$path_root = Config::getAppSetting('PATH_ROOT');
$route_dir = $_SERVER['DOCUMENT_ROOT'] . Config::getAppSetting('ROUTE_DIR');
$router = new Router($route_dir, $path_root);

// 実行
try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], strtok($_SERVER['REQUEST_URI'], '?'));
} catch (Throwable $th) {
    if (Config::getConfig('DEBUG_MODE')) {
        echo nl2br($th);
    } else {
        echo 'サーバーエラー';
    }
}
