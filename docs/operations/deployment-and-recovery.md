# 本番配信と復旧の運用手順

最終更新: 2026-08-15

## 1. このサイトの役割分担

| サービス | 役割 | 本番へ影響する操作 |
| --- | --- | --- |
| GitHub | ソース管理・Pull Request・`master` | `master` の公開対象変更でConoHa同期を起動 |
| Netlify | 静的プレビュー | 本番DNS・ConoHaファイルは変更しない |
| ConoHa WING | `www.sei-ko.org` の本番配信、PHP実行 | GitHub Actionsだけで更新する |

NetlifyはPHPを実行しないため、問い合わせフォームを含む本番確認はConoHaで行います。

## 2. 通常の本番配信

1. Pull Requestで内容を確認する。Netlifyプレビューは静的表示の確認に使う。
2. 承認した変更を `master` へマージする。
3. GitHubの [Actions](https://github.com/benihisago88/sei-ko.org/actions/workflows/conoha-production.yml) で **ConoHa production deploy** が成功したことを確認する。
4. 本番 `https://www.sei-ko.org/` を開き、変更箇所を確認する。

ConoHaへ送る対象は、Gitで管理されている公開ファイルだけです。

| 同期する | 同期しない |
| --- | --- |
| ルートのHTML/CSS/JS/PHP/XML/アイコン、`.htaccess`、`assets/` | `docs/`、`tools/`、`.github/`、`.netlify/`、`.idea/`、Git/IDE設定 |

新しい公開用のサブディレクトリを追加する場合は、配信対象かを確認してから
`.github/scripts/deploy_conoha.py` の対象ルールも更新してください。

## 3. GitHubの秘密情報

接続情報はGitHub Environment `conoha-production` にだけ保存します。リポジトリ・
チャット・HTML・JavaScriptへは書かないでください。

設定画面: <https://github.com/benihisago88/sei-ko.org/settings/environments/conoha-production>

必要なEnvironment secrets:

| 名前 | 内容 |
| --- | --- |
| `CONOHA_FTP_HOST` | ConoHaのFTPサーバー名 |
| `CONOHA_FTP_USERNAME` | FTPユーザー名 |
| `CONOHA_FTP_PASSWORD` | FTPパスワード |
| `CONOHA_FTP_REMOTE_DIR` | `/public_html/sei-ko.org/` |

リポジトリ変数 `CONOHA_AUTO_DEPLOY` は `true` を維持します。`false` にすると、
`master`へのpushでの自動同期を停止できます。

パスワードを変更したら、ConoHaでの変更後に必ず `CONOHA_FTP_PASSWORD` も同じ値へ更新します。

## 4. 手動ドライラン

本番へ書き込まず、接続先と差分だけを確認したいときの手順です。

1. [ConoHa production deploy](https://github.com/benihisago88/sei-ko.org/actions/workflows/conoha-production.yml) を開く。
2. **Run workflow** を選ぶ。
3. `dry_run` を `true` のまま実行する。
4. ログの「更新予定」「変更なし」「差分」を確認する。

`dry_run=false` は本番更新です。内容と差分を確認済みの場合だけ使います。

## 5. 失敗時・緊急時

1. Actionsの失敗ログを確認する。パスワード等はログに貼らない。
2. 同じ操作を連続で繰り返さず、まず `dry_run=true` を1回実行する。
3. `master` の対象ファイルと差分を確認する。復旧したい内容は、先にGitへコミットする。
4. `dry_run` が想定どおりなら、必要な場合だけ `dry_run=false` を一度実行する。
5. 本番URLのHTTP応答・表示・お問い合わせフォームの動作を確認する。

同期処理は、対象ファイルを一時名で転送し、サイズを確認してから同一ディレクトリ内で切り替えます。
既存の公開ファイルを削除する処理はありません。失敗時も、元の公開ファイルを直接空にする方式ではありません。

通常時にFTPソフト・curlなどで手動上書きはしないでください。どうしても緊急手動復旧が必要な場合は、
対象ファイルをダウンロードして退避し、1ファイルずつ確認しながら行い、その復旧内容も必ずGitへ戻してください。

## 6. 確認項目

ローカルの `python -m http.server` は `.htaccess` を解釈しません。本番反映後は少なくとも次を確認します。

- `https://sei-ko.org/` が `https://www.sei-ko.org/` へ集約されること
- トップ、料金表、ガイド、プライバシー、存在しないURLの表示
- CSPヘッダがあること
- `contact.php` のコードを変更した場合は、実際のお問い合わせ送信・メール・Google Sheets記録

## 7. 問い合わせフォームに関する注意

`contact.php` はConoHaで実行するPHPです。Netlifyプレビューでは送信できない表示になります。
フォーム送信・メール・Sheets記録を変える作業は、
[../../tools/README.md](../../tools/README.md)を読み、対象ファイルを限定して行ってください。
