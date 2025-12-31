<?php
namespace ayutenn\skeleton\app\routes\middleware;

use ayutenn\core\session\FlashMessage;
use ayutenn\core\routing\Middleware;
use ayutenn\skeleton\app\helper\Auth;

class NeedAuth extends Middleware
{
    /**
     * 副作用処理を実行する
     * ログインしていない場合、フラッシュメッセージを表示
     */
    public function handle(): void
    {
        if (!Auth::isLogined()) {
            FlashMessage::info("ログインが必要です。");
        }
    }

    /**
     * ルートを上書きすべきかどうかを判定する
     * ログインしていない場合はtrue（ルートを上書き）
     */
    public function shouldOverride(): bool
    {
        return !Auth::isLogined();
    }
}
