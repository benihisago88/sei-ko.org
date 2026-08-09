# sei-ko.org（デジタル担当室）

ビルド工程なしの素の静的サイト。HTML + CSS + 素のJS + `contact.php` 1本。
公開URL: **https://www.sei-ko.org/**（apex は 301 で www へ集約）

## デザイン刷新プロジェクト進行中

`docs/design-refresh/` に Phase 0〜7 の計画がある。**作業前に必ずこの順で読む:**

1. `docs/design-refresh/00-common-constraints.md` — 全フェーズ共通の制約
2. `docs/design-refresh/00-brand-tokens.md` — 色・アイコン作図ルールの正
3. `docs/design-refresh/PROGRESS.md` — 完了ログと申し送り
4. `docs/design-refresh/phase-N-*.md` — その回の作業指示

**現在: Phase 0 完了（本番反映済み）。次は Phase 1（ファビコン刷新）。**
Phase 1 着手前に PROGRESS.md 末尾の「Phase 1 事前検証メモ」を読むこと（フォント抽出の落とし穴を検証済み）。

推奨実行順は `0 → 1 → 6 → 2 → 3 → 4 → 5 → 7`（Phase 6 のOGPが旧ブランドのままで緊急度が高い）。

## 絶対に守ること

- **色**: 濃紺 `#11153f` + シアン `#35bfd2`。旧ブランド色 `#0e3d38` / `#d7ee87` / `#fff4d6` は使わない
- **CSPを緩めない**。インライン `<style>` / `style="..."` / インライン `<script>` は**すべて不可**。CSSは必ず `styles.css` に書く。インラインSVGは可
- **外部CDN・Webフォントを追加しない**。フレームワーク・バンドラも持ち込まない
- **キャッシュバスター**: アセットを変えたら `?v=YYYYMMDD.N` を上げ、**参照している4ページすべて**（`index.html` / `guide.html` / `privacy.html` / `404.html`）を同時に更新する
- **`contact.php` / `tools/` を触らない**（メール送信とGoogle Sheets連携の本番系）
- **リポジトリに `package.json` / `node_modules` を作らない**。画像生成はスクラッチパッドで行い、完成物だけコピーする
- **`git commit` はユーザーの明示指示があるまでしない**
- **事実を捏造しない**。実績・資格・件数などは `<!-- TODO(user): ... -->` で空欄にする
- **スコープ外を「ついでに」直さない**。気づいた点は PROGRESS.md の申し送りに書く

## 検証

ローカルは `python -m http.server 8777`。ただし **`.htaccess` は解釈されない**ので、
配信拒否・リダイレクト・キャッシュヘッダは**本番 https://www.sei-ko.org/ で自分で確認する**（ユーザーに確認を頼まない）。
4ページすべて、375 / 768 / 1280px で確認し、コンソールにエラー・CSP違反が出ないこと。

各フェーズ完了時は PROGRESS.md に追記する。**検証が通らなかった項目は正直に書く。**
