<?php
use ayutenn\skeleton\app\helper\Auth;

// 現在のページ名を取得
$current_uri = $_SERVER['REQUEST_URI'];

$menu_items = [];
if (Auth::isLogined()) {
    $menu_items = [
        ['name' => 'トップ', 'url' => './top', 'active_pattern' => '/\/top/'],
        ['name' => 'ユーザーリスト', 'url' => './user/list', 'active_pattern' => '/\/user\/list/'],
        ['name' => 'プロフィール', 'url' => './profile', 'active_pattern' => '/\/profile/'],
        ['name' => 'ログアウト', 'url' => './logout', 'active_pattern' => '/\/logout/'],
    ];
} else {
    $menu_items = [
        ['name' => 'トップ', 'url' => './', 'active_pattern' => '/\/$/'],
        ['name' => 'ユーザー登録', 'url' => './register', 'active_pattern' => '/\/register/'],
        ['name' => 'ログイン', 'url' => './login', 'active_pattern' => '/\/login/'],
        ['name' => 'ユーザーリスト', 'url' => './user/list', 'active_pattern' => '/\/user\/list/'],
    ];
}
?>
<style>
    .sidebar {
        width: 250px;
        background: rgba(0, 0, 0, 0.2);
        min-height: 100vh;
        padding: 20px;
        box-sizing: border-box;
        border-right: 1px solid rgba(255, 255, 255, 0.1);
    }
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .sidebar-menu li {
        margin-bottom: 10px;
    }
    .sidebar-menu a {
        display: block;
        padding: 10px 15px;
        color: #ccc;
        text-decoration: none;
        border-radius: 5px;
        transition: background 0.2s, color 0.2s;
    }
    .sidebar-menu a:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }
    .sidebar-menu a.active {
        background: #007bff;
        color: #fff;
    }
    /* レスポンシブ対応 */
    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            min-height: auto;
            border-right: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
    }
</style>

<aside class="sidebar">
    <ul class="sidebar-menu">
        <?php foreach ($menu_items as $item): ?>
            <?php
                // 簡易的なアクティブ判定
                $is_active = preg_match($item['active_pattern'], $current_uri);
                $active_class = $is_active ? 'active' : '';
            ?>
            <li>
                <a href="<?= $item['url'] ?>" class="<?= $active_class ?>">
                    <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</aside>
