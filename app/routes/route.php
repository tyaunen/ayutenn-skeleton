<?php
/**
 * ルート定義ファイル
 *
 * URLパターンと処理（Controller、API、View）のマッピングを定義します。
 * /app/routes/ディレクトリ内の全.phpファイルが自動的に読み込まれます。
 */
use ayutenn\core\routing\Route;
use ayutenn\core\routing\RouteGroup;
use ayutenn\skeleton\app\routes\middleware\NeedAuth;

// 認証が必要なページ用のミドルウェア
$need_auth = new NeedAuth('redirect', '');

return [
    // ゲストページ（認証不要）
    new Route('GET',  '/',                 'view', '/guest/top'),
    new Route('GET',  '/sample-login',            'view', '/guest/sample_login'),

    // ログイン・ログアウト処理
    new Route('POST', '/session/login',    'controller', '/session/Login'),
    new Route('GET',  '/logout',           'controller', '/session/Logout'),

    // サンプル: 登録フォームとAPI
    new Route('GET',  '/sample-register',  'view',       '/guest/sample_register'),
    new Route('POST', '/sample-register',  'controller', '/SampleRegister'),
    new Route('GET',  '/api/sample',       'api',        '/SampleApi'),

    // 認証が必要なページ
    new RouteGroup('', [
        new Route('GET',  '/top',          'view',       '/main/top'),
    ], [$need_auth])
];
