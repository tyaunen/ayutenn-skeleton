# アラート・トーストの使い方

## アラート

静的な通知メッセージ（フラッシュメッセージ表示に使用）：

```html
<div class="alert alert-info">ℹ️ Info Alert</div>
<div class="alert alert-success">✅ Success Alert</div>
<div class="alert alert-warning">⚠️ Warning Alert</div>
<div class="alert alert-error">❌ Error Alert</div>
```

### アラートクラス

| クラス | 用途 |
|--------|------|
| `.alert` | 基本スタイル |
| `.alert-info` | 情報（青系） |
| `.alert-success` | 成功（緑系） |
| `.alert-warning` | 警告（黄系） |
| `.alert-error` | エラー（赤系） |

## トースト

一定時間後に自動で消える通知。**`toast-container` をページに配置する必要があります。**

### HTML

```html
<div id="toast-container"></div>
```

### JavaScript

```javascript
// 基本
ayutenn.toast.showToast('メッセージ', 'info');

// タイプ: 'info', 'success', 'warning', 'error'
ayutenn.toast.showToast('成功しました', 'success');
ayutenn.toast.showToast('エラーが発生しました', 'error');

// 表示時間を指定（ミリ秒、デフォルト3000）
ayutenn.toast.showToast('5秒表示', 'info', 5000);
```

### 必要なファイル

```html
<link rel="stylesheet" href="assets/css/ayutenn/ayutenn.css">
<script src="assets/js/ayutenn/toast.js"></script>
```

### 動作

- 自動的に指定時間後にフェードアウト
- クリックで即座に閉じることも可能
- 複数のトーストを同時に表示可能

## サンプル参照

詳細なサンプルは `sample-alerts.php` を参照してください。
