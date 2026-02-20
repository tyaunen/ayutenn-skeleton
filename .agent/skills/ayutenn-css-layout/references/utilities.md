# ユーティリティの使い方

## スペーシング（マージン・パディング）

### クラス形式

- `.m-{0-10}` - margin 全方向
- `.mt-*`, `.mb-*`, `.ms-*`, `.me-*` - margin top/bottom/start/end
- `.mx-*`, `.my-*` - margin 水平/垂直
- `.p-{0-10}` - padding 全方向
- `.pt-*`, `.pb-*`, `.ps-*`, `.pe-*` - padding top/bottom/start/end
- `.px-*`, `.py-*` - padding 水平/垂直

### 例

```html
<div class="mt-3 mb-2 p-3">
  コンテンツ
</div>
```

## 表示ユーティリティ

- `.d-none` - 非表示
- `.d-block` - ブロック表示
- `.d-flex` - flexbox表示

## JavaScript ユーティリティ

### 必要なファイル

```html
<script src="assets/js/ayutenn/util.js"></script>
```

### escapeHtml()

HTMLエスケープ処理（XSS対策）：

```javascript
const escaped = ayutenn.util.escapeHtml('<script>alert("XSS")</script>');
// 結果: &lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;
```

### switchView()

同じグループ内の要素の表示を切り替え：

```html
<div data-display-group="grp" data-display-key="a">A</div>
<div data-display-group="grp" data-display-key="b" class="d-none">B</div>
```

```javascript
// Bを表示、Aを非表示
ayutenn.util.switchView('grp', 'b');
```

## Flexbox

```html
<div class="flex-row g-2">
  <div>Item 1</div>
  <div>Item 2</div>
</div>
```

## サンプル参照

詳細なサンプルは `sample-utils.php` と `sample-grid.php` を参照してください。
