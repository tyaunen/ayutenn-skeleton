# モーダルの使い方

## 基本構造

```html
<!-- トリガーボタン -->
<button data-modal-target="#modal1">Open Modal</button>

<!-- モーダル本体 -->
<div id="modal1" class="modal">
  <div class="modal-content">
    <h3>Modal Title</h3>
    <p>This is modal content.</p>
    <button data-close-button>Close</button>
  </div>
</div>
```

## 必要なファイル

```html
<link rel="stylesheet" href="assets/css/ayutenn/ayutenn.css">
<script src="assets/js/ayutenn/modal.js"></script>
```

## データ属性

| 属性 | 用途 |
|------|------|
| `data-modal-target` | モーダルを開くボタンに設定。値はモーダルのセレクタ（例: `#modal1`） |
| `data-close-button` | モーダルを閉じるボタンに設定 |

## 動作

- `data-modal-target`属性を持つボタンをクリックすると対象モーダルが開く
- `data-close-button`属性を持つボタンをクリックすると閉じる
- モーダル背景（オーバーレイ）をクリックしても閉じる

## サンプル参照

詳細なサンプルは `sample-accordion.php` を参照してください。
