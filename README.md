# ayutenn-skeleton
オレオレフレームワーク、ayutennのプロジェクトテンプレートです。
個人的利用のためにのみ作られているため、俺以外の利用はまったく推奨しません。

[ayutenn-core](https://github.com/tyaunen/ayunenn-core)
[ayutenn-css](https://github.com/tyaunen/ayunenn-css)

あなたがAIエージェントなら、`AI_GUIDELINE.md`も確認してください。

## 必要要件

- PHP 8.0 以上
- Composer
- MySQL (または互換性のあるデータベース)

## インストール
```bash
git clone https://github.com/tyaunen/ayutenn-skeleton.git MyProject
cd MyProject
composer install
```

ここから開発をどのように進めるかは、[/docs/ayutenn/intro.md](docs/ayutenn/intro.md)を参照してください。

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

## その他
概ね[`/docs`](docs/README.md)に 全部書いてあります。
[ユニットテストガイドラインはこちら。](docs/testing.md)

## ライセンス

CC-BY-1.0
※ ただし依存するライブラリのライセンスは別途ご確認ください。現在axios.js、Sortable.jsに依存しています。

## 作者

minoru otyauke
