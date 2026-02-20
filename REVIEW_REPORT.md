# ayutenn-skeleton レビュー報告書

作成日: 2026-02-20
対象: 構成 (`composer.json`, `config/*`, `scripts/*`)、コード (`app/*`)、ドキュメント (`README.md`, `docs/ayutenn/*`)

## 総評

テンプレートとしての骨格（`config` 分離、ルーティング起点、CSRF チェック、ビューコンポーネント化）は整っています。
一方で、実運用時に障害/脆弱性につながる実装上の問題と、導入者を誤誘導するドキュメント不整合が複数あります。

## 主要指摘（重大度順）

### 2. `HTTP_HOST` を無検証で URL/Cookie に使用している（High）

- 根拠:
`app/public/index.php:35`
`app/public/index.php:69`
`config/config.json.example:6`
- 内容:
`URL_ROOT` と Cookie `domain` を `$_SERVER['HTTP_HOST']` から直接生成。`TRUSTED_HOST` は設定されているが未使用。
- 影響:
Host ヘッダ偽装による誤った絶対 URL 生成リスク。加えて `HTTP_HOST` にポートが含まれると Cookie ドメイン不正を誘発します。
- 対応案:
`TRUSTED_HOST` を用いたホスト検証を追加し、Cookie ドメインはホスト名のみ（ポート除去）で設定。

### 3. エラーメッセージ表示が常時有効（High）

- 根拠:
`app/public/index.php:58`
`app/public/index.php:59`
`config/config.json.example:2`
- 内容:
`display_errors=On` / `E_ALL` が常時適用され、`DEBUG_MODE` と連動していません。
- 影響:
本番環境で内部情報（パス、スタックトレース等）が漏えいするリスク。
- 対応案:
`DEBUG_MODE` に応じて `display_errors` を切替え、非デバッグ時はログ出力に限定。

### 4. アセット URL が OS 依存パス区切りを混入させる（Medium）

- 根拠:
`app/views/components/flat/head.php:21`
`app/views/components/flat/head.php:22`
`app/views/components/flat/head.php:25`
`app/views/components/flat/head.php:26`
- 内容:
`getPathname()` の値をそのまま URL 化しており、Windows では `\` を含む URL が生成されます。
- 影響:
環境によって CSS/JS 読み込み不安定化。移植性低下。
- 対応案:
URL 生成前に `str_replace('\\', '/', $path)` で正規化。

### 5. 依存解決と再現性に課題（Medium）

- 根拠:
`composer.json:14`
`composer.json:15`
`composer.json:25`
`composer.json:29`
`composer.json:33`
- 内容:
依存が `dev-main` 固定、`minimum-stability: dev`、かつ VCS URL が SSH (`git@github.com`)。
- 影響:
環境差によるインストール失敗（SSH鍵必須）や、将来の破壊的変更取り込みリスクが高い。
- 対応案:
安定タグ/範囲指定へ移行し、公開導線は HTTPS 優先にする。

### 6. Composer スクリプトの初期化処理が不整合（Medium）

- 根拠:
`composer.json:41`
`composer.json:47`
`composer.json:48`
- 内容:
`post-create-project-cmd` で `tables` を作成（現行ドキュメントは `migrations/define` 基準）。
`post-install-cmd` では `config/gitignore.gist` の削除や `config.json.example` のリネームを行う。
- 影響:
テンプレート保守時にワークツリーを汚しやすく、構成の理解を難しくします。
- 対応案:
初期化対象を現在のディレクトリ設計に統一し、追跡ファイルの削除/改名は `create-project` 時限定にする。

### 7. テスト実行基盤が実質未整備（Medium）

- 根拠:
`composer.json:37`
`docs/ayutenn/testing.md:13`
- 内容:
`composer test` は実行可能だが、`phpunit.xml` / `tests/` がなく、実テストは走りません。
- 影響:
テンプレート利用開始時に回帰検知の足場がなく、変更品質を担保しにくい。
- 対応案:
最小の `phpunit.xml` と smoke test を同梱し、`composer test` で最低1件は実行される状態にする。

### 8. ドキュメントの不整合・誤記が多い（Medium）

- 根拠:
`README.md:8`
`docs/ayutenn/intro.md:184`
`docs/ayutenn/intro.md:336`
`docs/ayutenn/api.md:24`
`docs/ayutenn/api.md:84`
`docs/ayutenn/api.md:110`
`docs/ayutenn/best-practices.md:106`
`docs/ayutenn/best-practices.md:256`
`docs/ayutenn/controller.md:24`
`docs/ayutenn/controller.md:42`
`docs/ayutenn/controller.md:321`
`docs/ayutenn/testing.md:163`
`docs/ayutenn/view.md:194`
- 内容:
`AI_GUIDELINE.md` 参照先不在（実体は `AGENT.md`）、ルート例のパス誤り、APIサンプルのクラス名/戻り値不一致、`getData()` の説明矛盾、`main()` 可視性の説明矛盾、`exit` 推奨と禁止の混在、ヘルパーパス誤記など。
- 影響:
導入者がそのまま実装すると不具合や学習コスト増加を招きます。
- 対応案:
サンプルコードの静的検証（lint）を含めたドキュメント更新フローを導入し、実コードと同期させる。

## 補足確認

- `php -l`（`app/` と `scripts/` 配下）: 構文エラーは未検出。
- `composer validate --no-check-publish`: スキーマは有効（`version` フィールド推奨警告あり）。
- `composer test`: PHPUnit のヘルプ表示のみで、実テストは実行されない状態。

## 推奨対応順

1. Critical/High（`FlashMessage`、Host/URL/Cookie、エラー表示制御）を即時修正
2. Composer 依存/スクリプトの再設計（再現性と導入性の改善）
3. ドキュメント修正とテスト基盤の最小同梱（テンプレート品質の底上げ）

---
