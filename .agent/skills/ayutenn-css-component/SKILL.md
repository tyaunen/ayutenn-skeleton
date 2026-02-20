---
name: ayutenn-css-component
description: ayutenn-cssフレームワークのUIコンポーネント。モーダル、アコーディオン、タブ、ポップオーバー・ツールチップ、アラート・トースト、ファイルアップローダー、テキストカウンターなどのインタラクティブUIを実装する場合にこのスキルを使用する。
---

# ayutenn-css UIコンポーネントスキル

このプロジェクトは **ayutenn-css** フレームワークを使用しています。
インタラクティブなUIコンポーネントを実装する際は、以下の手順に従ってください。

## コンポーネント一覧

### モーダル
- `data-modal-target`/`data-close-button` 属性で制御
- 詳細: `references/modal.md` を参照

### アコーディオン
- `.accordion-btn` + `.accordion` 構成、グループ排他制御対応
- 詳細: `references/accordion.md` を参照

### タブ
- `.tabs` + `.tab-content` 構成、`data-tab-group` で制御
- 詳細: `references/tabs.md` を参照

### ポップオーバー・ツールチップ
- ポップオーバー: `.popover` クリックで表示（JS必要）
- ツールチップ: `.tooltip-container` ホバーで表示（JS不要）
- 詳細: `references/popover.md` を参照

### アラート・トースト
- アラート: `.alert` + `.alert-info/success/warning/error`
- トースト: `ayutenn.toast.showToast()` で表示
- 詳細: `references/alerts.md` を参照

### ファイルアップローダー
- ドラッグ&ドロップ対応、`ayutenn.FileUploader` で初期化
- 詳細: `references/file-uploader.md` を参照

### テキストカウンター
- 文字数・行数リアルタイムカウント、`ayutenn.TextCounter` で初期化
- 詳細: `references/text-counter.md` を参照

## 制約
- 各コンポーネントには個別のJSファイルが必要（`references/` で確認）
- 詳細なHTML構造・data属性・JSオプションは必ず各 `references/` ファイルを確認すること
