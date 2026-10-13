# プロジェクト独自のルール(作業報告書作成ツール)

設計の正は docs/design.md。仕様を変えたら同じ PR で design.md も更新する。

## 実行環境とGitHub

- このシェルは .zshrc を読まない。PHP・composer は `export PATH="$HOME/Library/Application Support/Herd/bin:$PATH"` を実行してから `herd php` / `herd composer` で動かす
- このリポジトリの GitHub 操作は nozomu-eto で行う。gh の操作の前に gh auth status で Active account を確認する

## 用語とモデル名(design.md 10-2)

| 画面の表記 | モデル(テーブル) | 意味 |
| --- | --- | --- |
| 顧客 | Customer(customers) | 作業の依頼主 |
| 現場 | Site(sites) | 作業する場所。1顧客に複数の現場 |
| 作業 | WorkOrder(work_orders) | いつ・どの現場で・誰が・どのテンプレートで作業するかの予定 |
| 報告書 | Report(reports) | 作業1件につき1件 |
| テンプレート | Template(templates) | 報告書の型 |
| テンプレートの版 | TemplateVersion(template_versions) | テンプレートの中身(JSON)。報告書は作成時の版を持つ |
| 状態の履歴 | ReportStatusHistory(report_status_histories) | 提出・承認・差し戻し・承認取り消しの記録 |
| ユーザー | User(users) | 役割は worker(作業者)/ admin(管理者) |
| 担当作業者 | work_orders.assigned_user_id | 作業に割り当てられた作業者 |

- 「作業」を Job にしない(App\Jobs やキューの jobs テーブルとぶつかる)。モデルは WorkOrder
- 「差し戻し」に return を使わない(予約語)。`sent_back` / `sendBack()`
- 「作業」は予定(WorkOrder)だけを指す。報告書の記述欄は「作業内容」
- 報告書の状態:`draft` / `submitted` / `sent_back` / `approved`
- 操作:`submit()` / `approve()` / `sendBack()` / `revokeApproval()`
- 作業一覧の表示:報告書なし=未着手、draft=作成中、submitted=承認待ち、sent_back=差し戻し、approved=完了

## 禁止事項・ルール(design.md 10-8)

- AI は勝手にコミット・push しない。差分を人が確認してから行う
- main に直接コミットしない。GitHub Flow(作業ごとの短いブランチ → PR → CI が通ってから人がマージ)
- コミットメッセージは Conventional Commits の種類(feat / fix / test / docs / refactor / chore)+日本語の説明。例:`feat(report): 提出時の必須チェックを追加`
- 10-5 の権限と 10-1 の状態遷移は、Pest のテストを必ず書いて実行する
- テストは SQLite ではなく MariaDB のテスト用DB(work_report_testing)で動かす
- .env の実際の値をコミットしない(管理するのは .env.example だけ)
- spatie/laravel-permission は使わない(役割は users.role)
- コマンドやパッケージの使い方は記憶で書かず、公式ドキュメント(Search Docs)で確認する。確認できなかった点は「未確認」と報告する
- PR の完了条件:CI の4つのチェック(Pint・Larastan・Pest・composer audit)が通っていること
