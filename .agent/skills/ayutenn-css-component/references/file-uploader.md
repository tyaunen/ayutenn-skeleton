# ファイルアップローダーの使い方

ドラッグ&ドロップ対応のUIファイルアップローダーです。
（バックエンドのアップロード処理は ayutenn-utility スキルの `references/file-upload.md` を参照）

## HTML構造

```html
<div id="uploader" class="file-uploader">
  <div class="icon">📁</div>
  <div class="file-uploader-text">ファイルをドラッグ&ドロップ</div>
  <div class="file-uploader-hint">またはクリックして選択</div>
</div>
```

## 必要なファイル

```html
<link rel="stylesheet" href="assets/css/ayutenn/ayutenn.css">
<script src="assets/js/ayutenn/FileUploader.js"></script>
```

## JavaScript初期化

```javascript
new ayutenn.FileUploader({
  containerId: 'uploader',
  endpoint: '/api/upload',
  maxFileSize: 5 * 1024 * 1024,  // 5MB
  acceptedTypes: 'image/*',
  multiple: true,
  params: {
    csrf_token: 'YOUR_CSRF_TOKEN'
  },
  onUploadComplete: (file, response) => {
    console.log('Uploaded:', file.name);
  },
  onError: (message) => {
    console.error(message);
  }
});
```

## オプション一覧

| オプション | 型 | デフォルト | 説明 |
|-----------|-----|----------|------|
| `containerId` | string | null | コンテナ要素のID（必須） |
| `endpoint` | string | '/oreore/upload_icon' | アップロード先URL |
| `maxFileSize` | number | 5MB | 最大ファイルサイズ（バイト） |
| `acceptedTypes` | string | null | 許可するMIMEタイプ |
| `multiple` | boolean | true | 複数ファイル選択の許可 |
| `params` | object | {} | 追加パラメータ |
| `onUploadComplete` | function | null | 完了時コールバック |
| `onError` | function | null | エラー時コールバック |

## acceptedTypes の例

- `'image/*'` - すべての画像
- `'image/png,image/jpeg'` - PNG と JPEG のみ
- `'application/pdf'` - PDF のみ
- `'.doc,.docx'` - Word ドキュメント

## サンプル参照

詳細なサンプルは `sample-uploader.php` を参照してください。
