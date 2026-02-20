# フォームの使い方

ayutenn-cssはフォーム要素に最低限のスタイルを適用します。

## 基本的なフォーム要素

```html
<label class="label">Text Input</label>
<input type="text" placeholder="Enter text">

<label class="label">Email</label>
<input type="email" placeholder="example@mail.com">

<label class="label">Password</label>
<input type="password" placeholder="Enter password">

<label class="label">Select</label>
<select>
  <option>Option 1</option>
  <option>Option 2</option>
</select>

<label class="label">Textarea</label>
<textarea placeholder="Enter text..."></textarea>
```

## チェックボックス

```html
<label class="label">Checkbox</label>
<div class="checkbox-group">
  <label class="checkbox-label"><input type="checkbox"> Option A</label>
  <label class="checkbox-label"><input type="checkbox" checked> Option B</label>
</div>
```

## ラジオボタン

```html
<label class="label">Radio</label>
<div class="radio-group">
  <label class="radio-label"><input type="radio" name="demo"> Choice 1</label>
  <label class="radio-label"><input type="radio" name="demo" checked> Choice 2</label>
</div>
```

## カスタムセレクト

HTMLを含む選択肢を表示できるカスタムセレクトボックス：

```html
<div id="demo-select" class="custom-select">
  <input type="hidden" name="val">
  <div class="select-holder">
    <div class="select-holder-content">選択してください</div>
  </div>
  <div class="select-options">
    <div class="select-option" data-value="1">
      <div class="option-header">🍎 りんご</div>
      <div class="option-detail">赤くておいしい</div>
    </div>
    <div class="select-option" data-value="2">
      <div class="option-header">🍊 オレンジ</div>
      <div class="option-detail">黄色くて甘い</div>
    </div>
  </div>
</div>
```

### カスタムセレクトに必要なJS

```html
<script src="assets/js/ayutenn/customSelect.js"></script>
```

## クラス一覧

| クラス | 用途 |
|--------|------|
| `.label` | フォームラベル |
| `.checkbox-group` | チェックボックスグループ |
| `.checkbox-label` | チェックボックスラベル |
| `.radio-group` | ラジオボタングループ |
| `.radio-label` | ラジオボタンラベル |
| `.custom-select` | カスタムセレクトコンテナ |
| `.select-holder` | 選択表示エリア |
| `.select-options` | 選択肢一覧 |
| `.select-option` | 個々の選択肢 |
| `.option-header` | 選択肢のメインテキスト |
| `.option-detail` | 選択肢の詳細テキスト（省略可） |

## サンプル参照

詳細なサンプルは `sample-forms.php` を参照してください。
