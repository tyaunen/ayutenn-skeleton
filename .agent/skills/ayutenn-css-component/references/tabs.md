# タブの使い方

## 基本構造

```html
<!-- タブヘッダー -->
<div class="tabs">
  <div class="tab active" data-tab-group="t1" data-tab-target="c1">Tab 1</div>
  <div class="tab" data-tab-group="t1" data-tab-target="c2">Tab 2</div>
</div>

<!-- タブコンテンツ -->
<div class="tab-content show" data-tab-group="t1" data-tab-id="c1">
  タブ1のコンテンツ
</div>
<div class="tab-content" data-tab-group="t1" data-tab-id="c2">
  タブ2のコンテンツ
</div>
```

## 必要なファイル

```html
<link rel="stylesheet" href="assets/css/ayutenn/ayutenn.css">
<script src="assets/js/ayutenn/tab.js"></script>
```

## クラス

| クラス | 用途 |
|--------|------|
| `.tabs` | タブヘッダーコンテナ |
| `.tab` | 個々のタブ |
| `.active` | アクティブなタブに自動付与 |
| `.tab-content` | タブコンテンツ |
| `.show` | 表示中のコンテンツに自動付与 |

## データ属性

| 属性 | 用途 |
|------|------|
| `data-tab-group` | タブのグループ名 |
| `data-tab-target` | タブに設定。対象コンテンツのID |
| `data-tab-id` | コンテンツの識別子 |

## 初期表示

- 初期表示するタブに `.active` クラスを付与
- 初期表示するコンテンツに `.show` クラスを付与

## サンプル参照

詳細なサンプルは `sample-tabs.php` を参照してください。
