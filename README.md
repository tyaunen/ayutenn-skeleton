# ayutenn-skeleton
オレオレフレームワーク、ayutennのプロジェクトテンプレートです。

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

ついでに、app.jsonのパスを適切に書き換えてください。


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

## 作者

minoru otyauke
