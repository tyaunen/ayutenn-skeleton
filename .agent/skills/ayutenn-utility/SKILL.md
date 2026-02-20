---
name: ayutenn-utility
description: ayutenn-coreフレームワークのユーティリティ機能。CSRF保護の追加、ファイルアップロード機能の実装、フラッシュメッセージ（一時通知）の表示、ログ出力の追加を行う場合にこのスキルを使用する。
---

# ayutenn ユーティリティスキル

このプロジェクトは **ayutenn-core** フレームワークを使用しています。
ユーティリティ機能（CSRF保護、ファイルアップロード、フラッシュメッセージ、ログ出力）を実装する際は、以下の手順に従ってください。

## 手順

### CSRF保護の追加
1. フォームに `CsrfTokenManager::getToken()` でトークンを埋め込む
2. `index.php` でPOST時にトークンを検証
- 詳細: `references/csrf.md` を参照

### ファイルアップロードの追加
1. `enctype="multipart/form-data"` のフォームを作成
2. コントローラーで `FileHandler` クラスを使用
- 詳細: `references/file-upload.md` を参照

### フラッシュメッセージの追加
1. コントローラー/API側で `FlashMessage::info/alert/error()` を呼出
2. ビュー側で `FlashMessage::getMessages()` で取得・表示
- 詳細: `references/flash-message.md` を参照

### ログ出力の追加
1. `Logger::setup()` でロガーを初期化
2. `$log->info/error/critical()` 等でログ出力
- 詳細: `references/logging.md` を参照

## 制約
- エラーハンドリングでは `FlashMessage` を使用してユーザーに通知すること
- 詳細な実装パターンは必ず各 `references/` ファイルを確認してから実装すること
