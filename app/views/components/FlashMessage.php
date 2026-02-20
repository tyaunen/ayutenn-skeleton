<?php
declare(strict_types=1);

namespace ayutenn\skeleton\app\views\components;

use ayutenn\core\session\FlashMessage as CoreFlashMessage;

/**
 * FlashMessage View Component
 *
 * ayutenn-coreのFlashMessageセッションを表示するためのコンポーネント
 */
class FlashMessage
{
    // メッセージタイプとCSSクラスのマッピング
    private const TYPE_CLASS_MAP = [
        CoreFlashMessage::INFO  => 'alert-info',
        CoreFlashMessage::ERROR => 'alert-error',
        CoreFlashMessage::ALERT => 'alert-warning',
    ];

    /**
     * フラッシュメッセージを描画する
     *
     * @return void
     */
    public static function render(): void
    {
        $messages = CoreFlashMessage::getMessages();

        if (empty($messages)) {
            return;
        }

        foreach ($messages as $message) {
            $type = $message['alert_type'] ?? CoreFlashMessage::INFO;
            $text = $message['text'] ?? '';

            // クラスの取得
            $type_class = self::TYPE_CLASS_MAP[$type] ?? 'alert-info';
            $class = "alert {$type_class}";

            // XSS対策のためエスケープ
            $escaped_text = h($text);

            echo "<div class=\"{$class}\">{$escaped_text}</div>";
        }
    }
}
