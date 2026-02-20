---
name: ayutenn-request
description: ayutenn-coreフレームワークでのリクエスト処理。コントローラー作成（POST処理）、API作成（JSONエンドポイント）、バリデーションルール定義、ルーティング定義を行う場合にこのスキルを使用する。
---

# ayutenn リクエスト処理スキル

このプロジェクトは **ayutenn-core** フレームワークを使用しています。
リクエスト処理（コントローラー、API、ルーティング、バリデーション）を実装する際は、以下の手順に従ってください。

## 手順

### コントローラーの作成（POSTフォーム処理）
1. `controllers/` に Controller を継承したクラスを作成
2. `$RequestParameterFormat` でバリデーションを定義（modelファイル参照を推奨）
3. `routes/` にPOSTルートを追加
- 詳細: `references/controller.md` を参照

### APIの作成（JSONエンドポイント）
1. `api/` に Api を継承したクラスを作成
2. `$RequestParameterFormat` でバリデーションを定義
3. `routes/` にAPIルートを追加
- 詳細: `references/api.md` を参照

### ルーティングの定義
1. `routes/` 内のPHPファイルにRouteを追加
2. routeAction: `view`, `controller`, `api`, `redirect` から選択
3. ミドルウェアはRouteGroupで適用
- 詳細: `references/routing.md` を参照

### バリデーションルールの定義
1. 繰り返し使う複合ルールは `models/` にJSONファイルで定義
2. フラグ等の単純な値はインライン定義
3. `db` セクションでマイグレーションとの連携が可能
- 詳細: `references/validation.md` を参照

## 制約
- `$RequestParameterFormat` のフォーマットは可能な限りmodelファイルに定義すること
- コントローラーではPRGパターンを使用すること（ビュー表示でもredirectを使用）
- 詳細な実装パターンは必ず各 `references/` ファイルを確認してから実装すること
