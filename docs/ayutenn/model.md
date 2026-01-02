# Model (バリデーションルール定義)

このドキュメントでは、リクエストパラメータのバリデーションルールを定義する方法を説明します。
モデルファイルは、Controller/APIのバリデーションやマイグレーションのテーブル定義でも使用されます。

## フレームワークリファレンス

バリデーションシステムの詳細仕様：
- **[validation.md](../../vendor/tyaunen/ayutenn-core/docs/validation.md)**

---

## モデルファイルとは
モデルファイルは、リクエストパラメータの型や許容範囲を定義するJSON形式のファイルです。
ControllerやAPIで使用され、自動的にバリデーションと型変換を行います。

## 格納ディレクトリ
モデルファイルは、`/app/model`に格納するJSON形式のファイルです。分類・整理のためにサブディレクトリを作っても構いません。

## ファイル形式
拡張子は`.json`である必要があります。
ファイル名が、ControllerやAPIの`$RequestParameterFormat`で`format`として指定する名前になります。

## シンプルな例

```json
// /app/model/user_id.json
{
    "name": "user_id",
    "type": "int",
    "min": 1
}
```

このモデルファイルを使用すると、1以上の整数のみを受け付けるバリデーションが行われます。

## 基本プロパティ

### name (オプション)
項目の名前を指定します。エラーメッセージに使用されます。

```json
{
    "name": "ユーザーID",
    "type": "int"
}
```

### type (必須)
値の型を指定します。指定可能な型は以下の通りです:

- **`string`** - 文字列
- **`int`** - 整数
- **`number`** - 数値(小数を含む)
- **`boolean`** - 真偽値(true, false, 0, 1, '0', '1')
- **`array`** - 配列

```json
{
    "type": "string"
}
```

## 文字列型のプロパティ

文字列型(`"type": "string"`)の場合、以下のプロパティが使用できます。

### min_length
最小文字数を指定します。

```json
{
    "type": "string",
    "min_length": 8
}
```

### max_length
最大文字数を指定します。

```json
{
    "type": "string",
    "max_length": 255
}
```

### min_line
最小行数を指定します。改行を含むテキストエリアなどで使用します。

```json
{
    "type": "string",
    "min_line": 3
}
```

### max_line
最大行数を指定します。

```json
{
    "type": "string",
    "max_line": 10
}
```

### 文字列型の例

```json
// /app/model/password.json
{
    "name": "password",
    "type": "string",
    "min_length": 8,
    "max_length": 255
}
```

```json
// /app/model/bio.json
{
    "name": "自己紹介",
    "type": "string",
    "max_length": 1000,
    "min_line": 1,
    "max_line": 10
}
```

## 数値型のプロパティ

数値型(`"type": "int"` または `"type": "number"`)の場合、以下のプロパティが使用できます。

### min
最小値を指定します。

```json
{
    "type": "int",
    "min": 0
}
```

### max
最大値を指定します。

```json
{
    "type": "int",
    "max": 100
}
```

### 数値型の例

```json
// /app/model/age.json
{
    "name": "年齢",
    "type": "int",
    "min": 0,
    "max": 150
}
```

```json
// /app/model/price.json
{
    "name": "価格",
    "type": "number",
    "min": 0.01
}
```

## 条件(condition)プロパティ

`condition`プロパティを使用すると、値の形式を細かく制限できます。
配列形式で複数の条件を指定できます。

### 使用可能な条件

#### numeric
数値形式であることを検証します。

```json
{
    "type": "int",
    "condition": ["numeric"]
}
```

#### int
整数形式であることを検証します。

```json
{
    "condition": ["int"]
}
```

#### boolean
真偽値(0, 1, '0', '1')であることを検証します。

```json
{
    "type": "boolean",
    "condition": ["boolean"]
}
```

#### email
メールアドレス形式であることを検証します。

```json
{
    "name": "メールアドレス",
    "type": "string",
    "condition": ["email"]
}
```

#### url
URL形式であることを検証します。

```json
{
    "name": "ウェブサイトURL",
    "type": "string",
    "condition": ["url"]
}
```

#### alphabets
英字のみであることを検証します。

```json
{
    "type": "string",
    "condition": ["alphabets"]
}
```

#### alphanumeric
英数字のみであることを検証します。

```json
{
    "name": "ユーザー名",
    "type": "string",
    "min_length": 3,
    "max_length": 16,
    "condition": ["alphanumeric"]
}
```

#### symbols
英数字+記号のみであることを検証します。パスワードなどで使用します。

```json
{
    "name": "パスワード",
    "type": "string",
    "min_length": 8,
    "max_length": 255,
    "condition": ["symbols"]
}
```

#### datetime
日付+時刻形式(`Y/m/d H:i:s`、例: `2025/12/03 12:34:56`)であることを検証します。

```json
{
    "name": "予約日時",
    "type": "string",
    "condition": ["datetime"]
}
```

#### color_code
カラーコード形式(`#RRGGBB`、例: `#a1b2c3`)であることを検証します。

```json
{
    "name": "テーマカラー",
    "type": "string",
    "condition": ["color_code"]
}
```

#### local_file
ローカルファイルパス形式であることを検証します。

```json
{
    "name": "ファイルパス",
    "type": "string",
    "condition": ["local_file"]
}
```

## 実践的な例

### ユーザーID(整数)

```json
// /app/model/user_id.json
{
    "name": "ユーザーID",
    "type": "int",
    "min": 1,
    "condition": ["numeric"]
}
```

### パスワード

```json
// /app/model/password.json
{
    "name": "パスワード",
    "type": "string",
    "min_length": 8,
    "max_length": 255,
    "condition": ["symbols"]
}
```

### メールアドレス

```json
// /app/model/email.json
{
    "name": "メールアドレス",
    "type": "string",
    "max_length": 255,
    "condition": ["email"]
}
```

### ユーザー名(英数字のみ)

```json
// /app/model/username.json
{
    "name": "ユーザー名",
    "type": "string",
    "min_length": 3,
    "max_length": 16,
    "condition": ["alphanumeric"]
}
```

### 年齢

```json
// /app/model/age.json
{
    "name": "年齢",
    "type": "int",
    "min": 0,
    "max": 150
}
```

### プロフィール(複数行テキスト)

```json
// /app/model/profile.json
{
    "name": "プロフィール",
    "type": "string",
    "max_length": 1000,
    "min_line": 1,
    "max_line": 20
}
```

### 予約日時

```json
// /app/model/reservation_datetime.json
{
    "name": "予約日時",
    "type": "string",
    "condition": ["datetime"]
}
```

### テーマカラー

```json
// /app/model/theme_color.json
{
    "name": "テーマカラー",
    "type": "string",
    "condition": ["color_code"]
}
```

## ControllerやAPIでの使用方法

モデルファイルを作成したら、ControllerやAPIの`$RequestParameterFormat`で使用します。

### Controllerでの使用例

```php
// /app/controller/UserRegister.php
namespace ayutenn\skeleton\app\controller;

use ayutenn\core\requests\Controller;

class UserRegister extends Controller
{
    protected array $RequestParameterFormat = [
        'username' => ['name' => 'ユーザー名', 'format' => 'username'],
        'email' => ['name' => 'メールアドレス', 'format' => 'email'],
        'password' => ['name' => 'パスワード', 'format' => 'password'],
        'age' => ['name' => '年齢', 'format' => 'age', 'require' => false], // 任意項目
    ];

    protected string $redirectUrlWhenError = '/register-form';

    public function main(): void
    {
        // この時点で、すべてのパラメータはバリデート済み+型変換済み
        $username = $this->parameter['username']; // 英数字3-16文字の文字列
        $email = $this->parameter['email'];       // メール形式の文字列
        $password = $this->parameter['password']; // 英数記号8-255文字の文字列
        $age = $this->parameter['age'] ?? null;  // 0-150の整数、または未設定

        // 登録処理...
    }
}

return new UserRegister;
```

### APIでの使用例

```php
// /app/api/CreatePostApi.php
namespace ayutenn\skeleton\app\api;

use ayutenn\core\requests\Api;

class CreatePostApi extends Api
{
    protected array $RequestParameterFormat = [
        'title' => ['name' => 'タイトル', 'format' => 'post_title'],
        'body' => ['name' => '本文', 'format' => 'post_body'],
        'color' => ['name' => 'カラー', 'format' => 'theme_color', 'require' => false],
    ];

    public function main(): array
    {
        $title = $this->parameter['title'];
        $body = $this->parameter['body'];
        $color = $this->parameter['color'] ?? '#000000';

        // 投稿作成処理...

        return $this->createResponse(true, ['post_id' => $new_post_id]);
    }
}

return new CreatePostApi();
```

## バリデーションと型変換

モデルファイルを使用すると、以下の処理が自動的に行われます:

1. **必須チェック**: `require => false`が指定されていない限り、値の存在をチェック
2. **型チェック**: `type`プロパティに基づいて型を検証
3. **範囲チェック**: `min`, `max`, `min_length`, `max_length`などで範囲を検証
4. **形式チェック**: `condition`プロパティで指定された形式を検証
5. **型変換(キャスト)**: バリデーション成功後、`type`に応じて適切な型に変換

### 型変換の例

```json
// /app/model/user_id.json
{
    "type": "int",
    "min": 1
}
```

このモデルを使用すると:
- 入力: `$_POST['user-id'] = '123'` (文字列)
- バリデーション: 整数形式で1以上かチェック
- 型変換: `$this->parameter['user-id'] = 123` (整数)

## エラーメッセージ

バリデーションエラーが発生すると、以下のようなメッセージが自動生成されます:

- 型エラー: `データの形式が不正です。`
- 最小文字数エラー: `8文字以上である必要があります。(現在: 5文字)`
- 最大文字数エラー: `255文字以下である必要があります。(現在: 300文字)`
- 最小値エラー: `0以上である必要があります。(現在: -1)`
- 最大値エラー: `100以下である必要があります。(現在: 150)`
- 条件エラー: `メールアドレスの形式である必要があります。`

`name`プロパティを指定すると、エラーメッセージに項目名が含まれます:
```
ユーザーIDは、データの形式が不正です。
パスワードは、8文字以上である必要があります。
```

## ベストプラクティス

### 1. 再利用可能なモデルを作成する
よく使う型(user_id, email, passwordなど)は、共通のモデルファイルとして作成し、複数のControllerやAPIで再利用しましょう。

### 2. nameプロパティを必ず設定する
わかりやすいエラーメッセージのために、`name`プロパティを必ず設定しましょう。

### 3. 適切な条件を組み合わせる
パスワードには`symbols`、ユーザー名には`alphanumeric`など、用途に応じた条件を設定しましょう。

### 4. 範囲を明確にする
`min`/`max`や`min_length`/`max_length`を設定して、想定外の値を防ぎましょう。

## まとめ

モデルファイルは以下の役割を持ちます:

- リクエストパラメータの型定義
- バリデーションルールの定義
- 自動型変換の指定
- わかりやすいエラーメッセージの生成

これらの機能により、ControllerやAPIでバリデーションコードを書く必要がなくなり、コードの保守性と安全性が向上します。
