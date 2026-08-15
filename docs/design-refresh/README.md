# デザイン刷新 トータルプラン

sei-ko.org（デジタル担当室）のファビコン・画像まわりの欠落と不整合を、8つのフェーズに分けて解消する計画書です。

各フェーズは **1回のAIセッションで完結する粒度** に切ってあります。`phase-N-*.md` の中身をそのままAIへの指示として渡してください。

---

## なぜこの作業をするのか

デザインレビューで、以下が判明しました。

1. **ブランドカラーが2系統に割れている（最優先）**
   `favicon.svg` / 4ページの `theme-color` / `site.webmanifest` / `styles.css` の一部は、旧ブランド「さいたま 社長のパソコン・AIお助け隊」由来の**深緑 `#0e3d38` + 黄緑 `#d7ee87`**。
   一方 `styles.css` の `:root` 実体は**濃紺 `#11153f` + シアン `#35bfd2`**。
   ヘッダーの「デジタル」はシアン、フッターの同じ文字は黄緑、という状態になっています。
   → **これを先に直さないと、以降のフェーズで作るアイコンがすべて誤った色になります。**

2. `favicon.svg` の `aria-label` が旧ブランド名のまま。図柄（モニター＋星）もヘッダーロゴ（円形＋「担」）と一致していない

3. `favicon.ico` なし／48px PNG なし／maskable アイコンなし

4. サービスカードのアイコンが記号文字 `⌘ ▤ ✦ ↗`（フォント依存で字形が崩れる、意味が伝わらない）

5. `assets/hero-consultation.png` が **1.74MB**（webp版は58〜92KB）

6. USE CASES の3タブでビジュアルが共通の抽象図形のみ／`guide.html` は画像ゼロ／運営者が見えるセクションが存在しない

7. **OGP画像が全面的に旧ブランドのまま（緊急度：最高）**
   `assets/ogp.jpg` は4ページすべてが参照している「SNSでの顔」。中身を確認したところ、大見出しが **「リモートお助け隊」**（旧ブランド名）、英字ラベルが `SAITAMA / PC & AI SUPPORT`（旧・地域訴求）、バッジが黄緑の「初回相談無料」（廃止色 ＋ サイト本文に存在しない訴求）。
   → **X・LINE・Slack でこのサイトが共有されるたび、サイト名と違うブランド名が表示されている。** サイト本体を整えても、流入前の第一印象がこれでは効果が打ち消される。詳細と対応は Phase 6。

---

## 確定済みの方針

この3点はオーナーの判断として確定しています。各フェーズのAIはこれを前提にしてください。**勝手に変更しないこと。**

| 項目 | 決定 |
|---|---|
| ブランドカラー | **濃紺 `#11153f` + シアン `#35bfd2` に統一**。`styles.css` の `:root` を正とし、旧緑色を置換する |
| 運営者プロフィール | **顔写真なしのテキスト版**で実装する |
| ラスター画像の生成 | **スクラッチパッドに一時的にツールを入れてAIが生成**。リポジトリに `package.json` / `node_modules` を追加しない |

---

## フェーズ一覧

| # | フェーズ | プロンプト | 想定規模 | 依存 |
|---|---|---|---|---|
| 0 | ブランド色の一元化 | [phase-0-brand-unify.md](phase-0-brand-unify.md) | 小 | なし |
| 1 | ファビコン一式の刷新 | [phase-1-favicon.md](phase-1-favicon.md) | 大 | Phase 0 |
| 2 | サービスアイコンSVG化 | [phase-2-service-icons.md](phase-2-service-icons.md) | 中 | Phase 0 |
| 3 | ヒーロー画像の軽量化 | [phase-3-hero-image.md](phase-3-hero-image.md) | 小 | なし |
| 4 | 運営者セクション新設 | [phase-4-profile-section.md](phase-4-profile-section.md) | 中 | Phase 0 |
| 5 | USE CASES タブ別ビジュアル | [phase-5-case-visuals.md](phase-5-case-visuals.md) | 大 | Phase 0, 2 |
| 6 | **OGP刷新** + guide.html 強化 | [phase-6-guide-assets.md](phase-6-guide-assets.md) | 中 | Phase 0, 1 |
| 7 | 仕上げと全体検証 | [phase-7-finishing.md](phase-7-finishing.md) | 中 | 全フェーズ |

> ⚠ **Phase 6 は緊急度が高い。** OGP画像が旧ブランド名のまま公開されているため（上記「7」）、Phase 1 が終わったら **2〜5 を飛ばして先に Phase 6 を実行してよい**。Phase 6 が依存するのは Phase 0（色）と Phase 1（ブランドマーク）だけ。

### 依存関係

```
Phase 0 (色の一元化) ──┬── Phase 1 (ファビコン) ──┬── Phase 6 (OGP刷新+guide) ★緊急
   ★最初に必須         │                          │
                       ├── Phase 2 (サービスアイコン) ── Phase 5 (ケース図解)
                       │
                       └── Phase 4 (運営者セクション)

Phase 3 (ヒーロー軽量化) ← 独立。いつでも実行可

すべて完了後 ── Phase 7 (仕上げ・全体検証)
```

**推奨実行順**：0 → 1 → **6** → 2 → 3 → 4 → 5 → 7

OGPの旧ブランド表示を早く止めたいので、Phase 6 を前倒ししています。急ぎでなければ番号順（0→1→2→…→7）でも構いません。

Phase 3 は他と独立しているので、時間が取れないときの埋め合わせに回して構いません。

---

## 共通ドキュメント

各フェーズのAIは、作業前に必ず以下を読みます。

| ファイル | 内容 |
|---|---|
| [00-common-constraints.md](00-common-constraints.md) | 全フェーズ共通の制約。CSP、キャッシュバスター規約、禁止事項 |
| [00-brand-tokens.md](00-brand-tokens.md) | ブランド仕様書。色・アイコン作図ルール・命名規約 |
| [PROGRESS.md](PROGRESS.md) | 各フェーズの完了ログ。前フェーズが何をしたかを引き継ぐ |

---

## 使い方

1. 新しいAIセッションを開く
2. 「`docs/design-refresh/phase-N-xxx.md` を読んで、その通りに作業して」と指示する
3. AIが `00-common-constraints.md` → `00-brand-tokens.md` → `PROGRESS.md` の順に読んでから着手する
4. 完了時にAIが `PROGRESS.md` へ追記する
5. 次のフェーズへ

各プロンプトには「完了条件」と「検証手順」が入っています。AIが自己申告で完了と言ってきたら、検証手順のコマンドを実際に流して確認してください。

---

## 対象リポジトリ

- パス: `C:\Users\murph\Desktop\sei-ko.org`
- 公開URL: https://www.sei-ko.org/
- 構成: ビルド工程なしの素の静的サイト（HTML + CSS + 素のJS + PHP1本）

### 主要ファイル

```
index.html          トップページ（全セクション）
guide.html          AI活用ガイド記事
privacy.html        プライバシーポリシー
404.html            404ページ
styles.css          全ページ共通スタイル（約2000行）
script.js           ケースタブ切替・診断フォーム・お問い合わせ送信
contact.php         お問い合わせ受信エンドポイント
.htaccess           CSP・キャッシュ・リダイレクト設定
site.webmanifest    PWAマニフェスト
assets/             ヒーロー画像・OGP画像
tools/              Google Sheets 連携スクリプト
```
