# API

このドキュメントでは、skeletonプロジェクトでのAPIファイルの作成方法を説明します。

## フレームワークリファレンス

Api基底クラスのAPIリファレンスについては、ayutenn-coreのドキュメントを参照してください：

- **[requests.md](../../vendor/tyaunen/ayutenn-core/docs/requests.md)** - Controller/Api基底クラスの詳細仕様、プロパティ、メソッド一覧

## 命名規則
APIファイルは、Apiで終わる名前のクラスファイルである必要があります。
例えば、GetUserApi.phpのような形です。

## 格納ディレクトリ
APIは、/app/apiに格納される単一のクラスファイルである必要があります。分類・整理のためにサブディレクトリを作っても構いません。
処理が1ファイルに収まらない複雑さになりそうなら、/app/helperに処理を分割することを検討してください。

## ayutennのAPIを継承する
APIは、ayutenn\core\requests\Apiを、必ず継承してください。

## ファイル末尾で、自身のインスタンスを返す
ファイル末尾で、ファイル末尾で、自身のインスタンスを返してください。
つまり、ファイル末尾で return new XXXX(); としてください。

## シンプルな例

```php
// /app/api/GetRandomNumberApi.php
namespace MyProject\app\api;

use ayutenn\core\requests\Api;

class GetRandomNumberApi extends Api
{
    public function main(): array
    {
        return $this->createResponse(
            true,
            [
                'number' => mt_rand(0, 100)
            ]
        );
    }
}

return new GetRandomNumberApi();
```

このApiはルーターによって起動されたとき、以下のようなレスポンスを返します。
```json
{
    "status": 0,
    "payload": [
        "number": 14
    ]
}
```

## オプション
### 与えられたリクエストパラメタをバリデート、取得する

Apiにはバリデート機能があります。
リクエストパラメタを受け取るときは、必ずこの機能を使用してください。
バリデートを行うためには、以下のようにprotectedなプロパティを上書きした上で、jsonでモデルファイルを作成する必要があります。
モデルファイルの記述方法は、モデルファイルのドキュメントを確認してください。

```json
// /app/model/number.json
{
    "name": "easy_calc_number",
    "type": "int",
    "min": 0,
    "condition": []
}
```
```php
// /app/api/GetRandomNumberApi.php
namespace MyProject\app\api;

use ayutenn\core\requests\Api;

class AddApi extends Api
{
    protected array $RequestParameterFormat = [
        'number1' => ['name' => '数値1', 'format' => 'easy_calc_number'],
        'number2' => ['name' => '数値2', 'format' => 'easy_calc_number'],
    ];

    // $this->parameterに、バリデートされたリクエストパラメタが格納されています。
    // リクエストパラメタがstringでない場合は、バリデートと一緒にキャストも済ませています。
    // つまりこの場合num1、num2はそれぞれ0以上の数値であり、intです。
    $num1 = $this->parameter['number1'];
    $num2 = $this->parameter['number2'];

    $result = $num1 + $num2;

    public function main(): array
    {
        return $this->createResponse(
            true,
            [
                'result' => $result
            ]
        );
    }
}

return new GetRandomNumberApi();
```

このAPIは、number1=3、number2=5だった場合以下のレスポンスを返します。

```json
// number1 = 3、
// number2 = 5 だった場合
{
    "status": 0,
    "payload": [
        "result": 8
    ]
}
```

バリデートに失敗したときは、エラーのレスポンスを返します。

```json
// number1 = 'a'、
// number2 は未設定だった場合
{
    "status": 9,
    "payload": [
        "message": "リクエストパラメータにエラーがあります。",
        "errors": [
            "数値1は数値である必要があります。",
            "リクエストに必要な値が設定されていません。（数値2）"
        ]
    ]
}
```

リクエストパラメタを任意設定にしたい場合は、以下のように書きます。

```php
// /app/api/GetRandomNumberApi.php
namespace MyProject\app\api;

use ayutenn\core\requests\Api;

class AddApi extends Api
{
    protected array $RequestParameterFormat = [
        'number1' => ['name' => '数値1', 'format' => 'easy_calc_number'],
        'number2' => ['name' => '数値2', 'format' => 'easy_calc_number'],
        'number3' => ['name' => '数値3', 'format' => 'easy_calc_number', 'require' => false], // この項目は任意
    ];

    // 任意項目は、issetではなかった場合のみバリデートをスキップします。
    $num1 = $this->parameter['number1'];
    $num2 = $this->parameter['number2'];
    $num3 = $this->parameter['number3'] ?? 0;

    $result = $num1 + $num2 + $num3;

    public function main(): array
    {
        return $this->createResponse(
            true,
            [
                'result' => $result
            ]
        );
    }
}

return new GetRandomNumberApi();
```