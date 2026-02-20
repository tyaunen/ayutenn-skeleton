# テキストカウンターの使い方

textarea または input[type="text"] の文字数・行数をリアルタイムでカウント表示します。

## 基本的な使い方

### HTML

```html
<div class="text-counter-wrapper">
    <textarea id="my-textarea"></textarea>
    <div id="my-counter" class="text-counter"></div>
</div>
```

### JavaScript

```html
<script src="assets/js/ayutenn/textCounter.js"></script>
<script>
new ayutenn.TextCounter({
    targetId: 'my-textarea',
    counterId: 'my-counter'
});
</script>
```

## オプション

| オプション | 型 | デフォルト | 説明 |
|-----------|-----|----------|------|
| `targetId` | string | `null` | 対象要素のID（必須） |
| `counterId` | string | `null` | カウンター要素のID（必須） |
| `showCharCount` | boolean | `true` | 文字数を表示するか |
| `showLineCount` | boolean | `true` | 行数を表示するか（inputは自動false） |
| `maxChars` | number/string | `null` | 最大文字数。`'auto'`でmaxlength取得 |
| `maxLines` | number | `null` | 最大行数（超過時に色変更） |
| `format` | string | `'{chars}文字 / {lines}行'` | 表示フォーマット |
| `onUpdate` | function | `null` | 更新時コールバック |
| `onExceed` | function | `null` | 制限超過時コールバック |

## 使用例

### 文字数のみ

```javascript
new ayutenn.TextCounter({
    targetId: 'my-textarea',
    counterId: 'my-counter',
    showLineCount: false,
    format: '{chars}文字'
});
```

### 最大値制限付き

```javascript
new ayutenn.TextCounter({
    targetId: 'my-textarea',
    counterId: 'my-counter',
    maxChars: 100,
    maxLines: 10
});
```

### Input要素（maxlength自動取得）

```html
<input type="text" id="my-input" maxlength="100">
<div id="my-input-counter" class="text-counter"></div>

<script>
new ayutenn.TextCounter({
    targetId: 'my-input',
    counterId: 'my-input-counter',
    maxChars: 'auto'
});
</script>
```

### コールバック

```javascript
new ayutenn.TextCounter({
    targetId: 'my-textarea',
    counterId: 'my-counter',
    maxChars: 100,
    onUpdate: function(data) {
        console.log('文字数:', data.charCount, '行数:', data.lineCount);
    },
    onExceed: function(data) {
        alert('制限を超過しました！');
    }
});
```

## JavaScript API

```javascript
const counter = new ayutenn.TextCounter({...});
counter.getCharCount();  // 現在の文字数
counter.getLineCount();  // 現在の行数
```

## CSSカスタマイズ

```css
.text-counter .char-count.exceeded { color: red; font-weight: bold; }
.text-counter .line-count.exceeded { color: orange; }
```
