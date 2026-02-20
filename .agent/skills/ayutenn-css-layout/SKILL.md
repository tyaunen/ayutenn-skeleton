---
name: ayutenn-css-layout
description: ayutenn-cssフレームワークのページレイアウト機能。グリッドレイアウト（12カラム・レスポンシブ）、フォーム要素のスタイリング、スペーシング・表示ユーティリティクラスを使用する場合にこのスキルを使用する。
---

# ayutenn-css レイアウトスキル

このプロジェクトは **ayutenn-css** フレームワークを使用しています。
ページレイアウト・フォーム・ユーティリティクラスを実装する際は、以下の手順に従ってください。

## 手順

### グリッドレイアウト
- 12カラムのレスポンシブグリッドシステム
- `.grid-row` + `.col-*` で構成、`.g-*` でガター設定
- レスポンシブ: `.col-sm-*`, `.col-md-*`, `.col-lg-*` 等
- 詳細: `references/grid.md` を参照

### フォーム
- `.label`, `.checkbox-group`, `.radio-group` 等の基本スタイル
- カスタムセレクトボックス（`.custom-select`）対応
- 詳細: `references/forms.md` を参照

### ユーティリティ
- スペーシング: `.m-*`, `.p-*`（margin/padding）
- 表示: `.d-none`, `.d-block`, `.d-flex`
- JS: `escapeHtml()`, `switchView()` 等
- 詳細: `references/utilities.md` を参照

## 制約
- 詳細なクラス名・使用例は必ず各 `references/` ファイルを確認すること
