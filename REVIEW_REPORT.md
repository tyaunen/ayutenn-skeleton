# ayutenn-skeleton レビュー報告書

作成日: 2026-02-20  
対象: 構成 (`composer.json`, `config/*`, `scripts/*`)、コード (`app/*`)、ドキュメント (`README.md`, `docs/ayutenn/*`)

## 総評

テンプレートとしての骨格（`config` 分離、ルーティング起点、CSRF チェック、ビューコンポーネント化）は整っています。  
一方で、実運用時に障害/脆弱性につながる実装上の問題と、導入者を誤誘導するドキュメント不整合が複数あります。

## 主要指摘（重大度順）

### 1. `FlashMessage` 描画で実行時エラーになる（Critical）

- 根拠:
`app/views/components/FlashMessage.php:44`  
`app/helper/shorthands.php:7`
- 内容:
`h()` は 1 引数関数ですが、`FlashMessage::render()` 内で 3 引数で呼び出されています。
- 影響:
フラッシュメッセージ表示時に Fatal Error が発生し、画面描画が停止します。
- 対応案:
`h($text)` に修正、または `htmlspecialchars($text, ENT_QUOTES, 'UTF-8')` を直接使用。

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

## 追補: ayutenn-core 利用方法レビュー（本体品質ではなく利用側観点）

確認対象:

- 本プロジェクト側コード（`app/public/index.php`, `config/app.json`, `app/api/SampleApi.php`）
- `vendor/tyaunen/ayutenn-core` の公開実装・テスト（`src/*`, `tests/unit/*`）

### A1. `FrameworkPaths::init()` の必須キー不足で起動時例外になる（Critical）

- 根拠:
`app/public/index.php:43`  
`vendor/tyaunen/ayutenn-core/src/FrameworkPaths.php:38`  
`vendor/tyaunen/ayutenn-core/src/FrameworkPaths.php:44`  
`vendor/tyaunen/ayutenn-core/src/FrameworkPaths.php:65`  
`vendor/tyaunen/ayutenn-core/tests/unit/FrameworkPathsSecurityTest.php:95`
- 内容:
`FrameworkPaths` は `trustedHost` を必須キーとして要求しますが、本プロジェクトの初期化配列に `trustedHost` がありません。
- 実行確認:
同等初期化を再現すると `InvalidArgumentException: 必須のパス設定が見つかりません: trustedHost` を確認。
- 影響:
ayutenn-core 現行仕様ではブートストラップ段階で失敗し、アプリ起動できません。
- 対応案:
`app/public/index.php` の `FrameworkPaths::init()` に  
`'trustedHost' => Config::get('TRUSTED_HOST')` を追加。

### A2. 404ビュー設定値の形式が core の解決仕様と不一致（High）

- 根拠:
`config/app.json:12`  
`vendor/tyaunen/ayutenn-core/src/routing/Route.php:252`  
`vendor/tyaunen/ayutenn-core/tests/unit/routing/RouteNotFoundTest.php:71`
- 内容:
本プロジェクトは `"404_PAGE_FILE": "/system/404"` を設定していますが、core の `showNotFoundPage()` は `notFoundView` を拡張子込みパスとして扱います（`.php` を自動付与しない）。
- 実行確認:
`/system/404` では「404ビューファイルが見つかりません」、`/system/404.php` では正常解決。
- 影響:
未定義ルート時に想定404ではなく例外処理に入り、安定した404応答になりません。
- 対応案:
`config/app.json` の値を `"/system/404.php"` に変更。

### A3. `QueryResult` 取得方法の core API 利用が誤っている（High）

- 根拠:
`app/api/SampleApi.php:53`  
`vendor/tyaunen/ayutenn-core/src/database/QueryResult.php:40`  
`vendor/tyaunen/ayutenn-core/src/database/QueryResult.php:85`
- 内容:
`SampleApi` は `$result->data[0]` を参照していますが、`QueryResult::$data` は private であり直接アクセスできません。core が提供する `getData()` の使用が必要です。
- 実行確認:
`Cannot access private property ayutenn\core\database\QueryResult::$data` を再現確認。
- 影響:
サンプルAPIを流用すると実行時エラーでAPI処理が停止します。
- 対応案:
`$userData = $result->getData()[0] ?? null;` に置換。

### A4. core の `trustedHost` 設計をバイパスする URL 生成（Medium）

- 根拠:
`app/public/index.php:35`  
`app/public/index.php:91`  
`vendor/tyaunen/ayutenn-core/src/FrameworkPaths.php:164`  
`vendor/tyaunen/ayutenn-core/src/FrameworkPaths.php:167`  
`vendor/tyaunen/ayutenn-core/tests/unit/FrameworkPathsSecurityTest.php:71`
- 内容:
core は `FrameworkPaths::getBaseUrl()` で `trustedHost` を利用する設計ですが、本プロジェクトは `URL_ROOT` を `$_SERVER['HTTP_HOST']` から生成し、CSRF失敗時リダイレクトにも使用しています。
- 影響:
core 側で用意されたホスト固定の安全策と一貫しない導線になります。
- 対応案:
`URL_ROOT` 依存を減らし、リダイレクトURLは `FrameworkPaths::getBaseUrl()` を基準に統一。

## 追補の結論

ayutenn-core を「使う側」の観点では、現状は互換性・安全性の両面で修正必須事項があります。  
特に `A1`（`trustedHost`）と `A2`（404ビュー指定）は、core の現行契約に対する不整合であり優先対応が必要です。
