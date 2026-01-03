<?php
/**
 * ルート定義ファイル
 *
 * URLパターンと処理（Controller、API、View）のマッピングを定義します。
 * /app/routes/ディレクトリ内の全.phpファイルが自動的に読み込まれます。
 */
use ayutenn\core\routing\Route;

return [
    new Route('GET',  '/',                 'view', '/guest/top'),
];
