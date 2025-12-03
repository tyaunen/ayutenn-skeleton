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

    new RouteGroup('', [
        new Route('GET', '/top',              'view', '/main/top'),
    ], [$need_auth])
];
