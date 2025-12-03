<?php
namespace ayutenn\skeleton\app\controller;

use ayutenn\core\requests\Controller;
use ayutenn\core\database\DbConnector;
use ayutenn\skeleton\app\database\UserManager;
use ayutenn\skeleton\app\helper\Auth;

class Profile extends Controller{

    public static function name(): string { return 'profile'; }

    public function main(): void
    {
        $login_user = Auth::getLoginUser();
        $user_id = $login_user['id'];

        $pdo = DbConnector::connectWithPdo();
        $user_manager = new UserManager($pdo);
        $result = $user_manager->getUser($user_id);

        if ($result->isSucceed()) {
            $user = $result->data[0];
            // ビューに変数を渡す（Controllerクラスの仕様によるが、ここではビューファイル内で直接呼び出す形か、あるいはメンバ変数にセットするか）
            // ayutennのController仕様では、ビューへのデータ渡しは明示的なメソッドがないため、
            // ビュー側でControllerのプロパティにアクセスするか、あるいはビュー側でデータを再取得する必要があるかもしれない。
            // しかし、Controllerの役割としてデータを準備するのが普通。
            // ここでは、ビューファイル側でデータを取得する形（user_list.phpと同様）にするか、
            // あるいはControllerでデータをセットしてビューをincludeする形か。
            // route.phpを見ると、'view'タイプの場合は直接ビューファイルを読み込むが、'controller'タイプの場合はController->main()が呼ばれる。
            // Profile画面はデータを表示する必要があるため、Controllerを経由してビューを表示するのが望ましいが、
            // ayutennの現在の実装（route.php）では、Controllerからビューを表示する仕組み（renderメソッドなど）が見当たらない。
            // 既存の Route クラスの処理を見ると、'view' タイプは単に require するだけ。
            // 'controller' タイプは main() を実行するだけ。
            // したがって、データを表示するページを作るには、
            // 1. 'view' タイプでルート定義し、ビューファイル内でロジックを書く（user_list.php方式）
            // 2. 'controller' タイプでルート定義し、main() 内で require する。

            // 今回は 2 の方式を採用し、Profileコントローラーからビューを読み込む。
            // データを変数として渡せるようにする。

            require_once(__DIR__ . '/../views/main/profile.php');
        } else {
            // エラー処理
            $this->redirect('/logout');
        }
    }
}
return new Profile;
