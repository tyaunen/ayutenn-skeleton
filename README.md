# ayutenn-skeleton

ayutennフレームワークのプロジェクトテンプレートです。
このテンプレートを使用することで、素早くWebアプリケーション開発を開始できます。

## 必要要件

- PHP 8.0 以上
- Composer
- MySQL (または互換性のあるデータベース)

## インストール

Composerを使用してプロジェクトを作成します。

```bash
composer create-project tyaunen/ayutenn-skeleton my-project --repository='{"type":"vcs","url":"https://github.com/tyaunen/ayutenn-skeleton"}'
```

※ 現在はプライベートリポジトリのため、`--repository` オプションが必要です。Packagist公開後は不要になります。

## セットアップ手順

### 1. 設定ファイルの準備

インストール時に `config/config.json.example` が `config/config.json` にコピーされます。
環境に合わせて設定値を変更してください。

```json
{
    "DEBUG_MODE": true,
    "PDO_DSN": "mysql:host=localhost;dbname=YOUR_DATABASE_NAME;charset=utf8mb4",
    "PDO_USERNAME": "YOUR_USERNAME",
    "PDO_PASSWORD": "YOUR_PASSWORD",
    ...
}
```

### 2. データベースの準備

データベースを作成し、`database/schema.sql` を実行してテーブルを作成してください。

```bash
mysql -u root -p YOUR_DATABASE_NAME < database/schema.sql
```

### 3. Webサーバーの設定

ドキュメントルートを `public/` ディレクトリに設定してください。
Apacheの場合は `.htaccess` が既に用意されています。

## ディレクトリ構造

```
app/
  ├── api/          # APIクラス
  ├── controller/   # コントローラークラス
  ├── database/     # データベース操作クラス
  ├── helper/       # ヘルパークラス
  ├── model/        # バリデーションルール定義
  ├── public/       # 公開ディレクトリ (index.php, assets)
  ├── routes/       # ルーティング定義
  └── views/        # ビューファイル
config/             # 設定ファイル
doc/                # ドキュメント
vendor/             # Composer依存ライブラリ
```

## サンプルコード

以下のサンプルコードが含まれています。

- **認証**: ログイン、ログアウト (`app/controller/session/`)
- **登録**: サンプル登録フォーム (`app/controller/SampleRegister.php`)
- **API**: サンプルAPI (`app/api/SampleApi.php`)

## ライセンス

CC-BY-1.0

## 作者

minoru otyauke
