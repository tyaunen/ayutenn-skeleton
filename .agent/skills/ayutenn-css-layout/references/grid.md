# グリッドレイアウトの使い方

ayutenn-cssは12カラムのレスポンシブグリッドシステムを提供します。

## 基本構造

```html
<div class="grid-row g-2">
  <div class="col-4">コンテンツ1</div>
  <div class="col-4">コンテンツ2</div>
  <div class="col-4">コンテンツ3</div>
</div>
```

## クラス一覧

### カラム幅
- `.col-1` ～ `.col-12`: カラム幅（12カラム中何カラム分）

### レスポンシブプレフィクス
- `.col-*`: 全サイズ（0px～）
- `.col-sm-*`: ≥576px（スマートフォン横）
- `.col-md-*`: ≥768px（タブレット）
- `.col-lg-*`: ≥992px（デスクトップ）
- `.col-xl-*`: ≥1200px（大型デスクトップ）
- `.col-xxl-*`: ≥1400px（大型デスクトップ）
- `.col-xxxl-*`: ≥1600px（超大型モニタ）

### ガター（カラム間スペース）
- `.g-1` ～ `.g-10`: カラム間のスペース（1px～10px）

## レスポンシブ例

```html
<!-- モバイル: 1列、sm: 2列、md: 3列、lg: 4列、xl: 6列 -->
<div class="grid-row g-2">
  <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
    コンテンツ
  </div>
</div>
```

## コンテナ

`.container`クラスで画面幅に応じた最大幅を自動設定：
- sm (≥576px): max-width: 540px
- md (≥768px): max-width: 720px
- lg (≥992px): max-width: 960px
- xl (≥1200px): max-width: 1140px
- xxxl (≥1680px): max-width: 1600px

## サンプル参照

詳細なサンプルは `sample-grid.php` を参照してください。
