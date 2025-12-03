<?php
namespace ayutenn\skeleton\app\routes\middleware;

use ayutenn\core\session\AlertsSession;
use ayutenn\core\routing\Middleware;
use ayutenn\skeleton\app\helper\Auth;

class NeedAuth extends Middleware
{
    public function canOverrideRoute(): bool
    {
        if (!Auth::isLogined()) {
            AlertsSession::putInfoMessageIntoSession(
                "ログインが必要です。"
            );
            return true;
        } else {
            return false;
        }
    }
}
