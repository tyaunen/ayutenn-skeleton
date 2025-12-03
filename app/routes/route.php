<?php
use ayutenn\core\routing\Route;
use ayutenn\core\routing\RouteGroup;
use ayutenn\skeleton\app\routes\middleware\NeedAuth;

$need_auth = new NeedAuth('redirect', '');

return [
    new Route('GET',  '/',                 'view', '/guest/top'),
    new Route('GET',  '/test',             'view', '/guest/top'),

    new Route('GET',  '/api/get/number',   'api',        '/GetRamdomNumberApi'),
    new Route('POST', '/user/add',         'controller', '/CreateUserController'),

    // ユーザー登録
    new Route('GET',  '/register',         'view',       '/guest/register'),
    new Route('POST', '/register',         'controller', '/Register'),

    // ユーザーリスト
    new Route('GET',  '/user/list',        'view',       '/guest/user_list'),

    // ログイン・ログアウト
    new Route('GET',  '/login',            'view',       '/guest/login'),
    new Route('POST', '/session/login',    'controller', '/session/Login'),
    new Route('GET',  '/logout',           'controller', '/session/Logout'),

    new RouteGroup('', [
        new Route('GET',  '/top',              'view',       '/main/top'),
        new Route('GET',  '/profile',          'controller', '/Profile'),
        new Route('POST', '/profile/update',   'controller', '/UpdateProfile'),
    ], [$need_auth])
];
