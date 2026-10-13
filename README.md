# 作業報告書作成ツール

清掃・点検・退去立会いなど、業種を問わず使える作業報告書作成アプリです。作業者がスマホで入力して提出し、管理者が承認・差し戻しをして、承認済みの報告書を PDF で出力します。業種ごとの違いはテンプレート(JSON)の差し替えで吸収します。

設計は [docs/design.md](docs/design.md) を正とします。

## 技術構成

| 項目 | 内容 |
| --- | --- |
| フレームワーク | Laravel 13 |
| 画面 | Filament 5(/admin:管理者、/app:作業者) |
| PHP | 8.5 |
| DB | MariaDB 12.3 LTS |
| テスト | Pest 5 |
| 静的解析・整形 | Larastan 3、Pint |
| CI | GitHub Actions |

## ローカルの起動手順

前提:Laravel Herd、Docker(Compose)、PHP 8.5(`herd isolate 8.5`)。

```sh
# 依存パッケージ
composer install

# 環境設定(.env は管理しない。.env.example からコピー)
cp .env.example .env
php artisan key:generate

# DB の起動(MariaDB 12.3。127.0.0.1:3306)
docker compose up -d

# マイグレーションと動作確認用ユーザー
php artisan migrate
php artisan db:seed
```

Herd のサイトとして開き、`/admin` と `/app` からログインできます。

| 役割 | メール | パスワード | 入れるパネル |
| --- | --- | --- | --- |
| 管理者 | admin@example.com | password | /admin |
| 作業者 | worker@example.com | password | /app |

### テスト

テストは SQLite ではなく MariaDB のテスト用DB(`work_report_testing`)で動かします。`compose.yaml` の初期化スクリプト(`docker/mariadb/initdb/`)が最初の起動時に作成します。

すでに作成済みのボリュームには初期化スクリプトが効かないため、その場合は手動で作成します。

```sh
docker compose exec mariadb mariadb -uroot -ppassword \
  -e "CREATE DATABASE IF NOT EXISTS work_report_testing"
```

```sh
./vendor/bin/pest                          # テスト
./vendor/bin/pint --test                   # コードスタイル(--test なしで自動修正)
./vendor/bin/phpstan analyse               # 静的解析
composer audit                             # 依存パッケージの脆弱性チェック
```

## 開発の進め方

GitHub Flow(作業ごとのブランチ → プルリクエスト → CI が通ったらマージ)です。CI は Pint・Larastan・Pest・composer audit を実行します。詳細は設計書の 10-8 を参照してください。

## AIの使い方

(あとで書く)
