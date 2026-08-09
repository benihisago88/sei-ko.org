# 進捗ログ

各フェーズのAIが**完了時に必ず追記する**引き継ぎログ。次フェーズのAIは着手前にこれを読む。

追記は下の「フェーズ記録」に、Phase番号順に追加すること。既存の記録を書き換えないこと。

---

## 記入フォーマット

```markdown
### Phase N: <タイトル>

- **状態**: 完了 / 部分完了 / 中断
- **日付**: YYYY-MM-DD
- **キャッシュバスター**: このフェーズで設定した `?v=` の値
- **変更ファイル**:
  - `path/to/file` — 何をしたか
- **新規ファイル**:
  - `path/to/file` — 何か
- **検証結果**: 実際に実行したコマンドと結果。通らなかった項目は正直に書く
- **次フェーズへの申し送り**: 次の担当が知らないと困ること
- **スコープ外で気づいた点**: 直していないが報告すべきこと
```

---

## 全体の状態

| Phase | 内容 | 状態 |
|---|---|---|
| 0 | ブランド色の一元化 | 完了 |
| 1 | ファビコン一式の刷新 | 未着手 |
| 2 | サービスアイコンSVG化 | 未着手 |
| 3 | ヒーロー画像の軽量化 | 未着手 |
| 4 | 運営者セクション新設 | 未着手 |
| 5 | USE CASES タブ別ビジュアル | 未着手 |
| 6 | guide.html 強化 | 未着手 |
| 7 | 仕上げと全体検証 | 未着手 |

> フェーズ完了時、この表の状態も更新すること。

---

## 着手前の基準値（2026-08-09 計測）

以降のフェーズで「改善した」と言うための比較元。

| 項目 | 値 |
|---|---|
| 旧ブランド色の残存箇所 | grep で **11行**（`#0e3d38` ×7 / `#d7ee87` ×3 / `#fff4d6` ×1） |
| ケース図解の緑 `#86b89e` | `styles.css` の `.bp-line` / `.bp-square`（Phase 5 で削除） |
| `assets/hero-consultation.png` | 1,740,979 bytes（1.74MB） |
| `assets/ogp.jpg` | 122,044 bytes / 1200×630。**中身が全面的に旧ブランド**（「リモートお助け隊」「SAITAMA / PC & AI SUPPORT」「初回相談無料」黄緑バッジ） |
| `assets/ogp-guide.jpg` | **存在しない**（guide.html は ogp.jpg を共用） |
| `favicon-32.png` | 32×32 |
| `apple-touch-icon.png` | 180×180 |
| `icon-192.png` / `icon-512.png` | 192×192 / 512×512 |
| `favicon.ico` | **存在しない** |
| 48px ファビコン | **存在しない** |
| maskable アイコン | **存在しない** |
| CSSキャッシュバスター | `?v=20260809.1` |
| JSキャッシュバスター | `?v=20260808.2` |
| アイコン系キャッシュバスター | `?v=20260801`（`favicon.svg` のみ `?v=20260716.5`） |

---

## フェーズ記録

<!-- 各フェーズのAIはこの下に追記する -->

### Phase 0: ブランド色の一元化

- **状態**: 完了
- **日付**: 2026-08-09
- **キャッシュバスター**: favicon.svg `?v=20260809.1` / styles.css `?v=20260809.2`
  - styles.css は着手時点で既に `?v=20260809.1`（同日）だったため、共通制約 §3 の同日連番ルールに従い `.2` へ繰り上げた
- **変更ファイル**:
  - `index.html` / `guide.html` / `privacy.html` / `404.html` — theme-color を `#11153f` に、favicon.svg と styles.css のキャッシュバスターを更新
  - `site.webmanifest` — theme_color を `#11153f` に
  - `styles.css` — :315 box-shadow `#0e3d3824` → `#11153f24`（アルファ値 `24` 保持）、:1304 フッター `.brand-dot` `#d7ee87` → `#47c4d0`
  - `favicon.svg` — 色のみ置換（`#0e3d38`→`#11153f` / `#d7ee87`→`#35bfd2` / `#fff4d6`→`#ffffff`）。図柄と aria-label は未変更
  - `.htaccess` — `RewriteEngine On` 直後に `RewriteRule ^(?:docs|tools)/ - [F,L]` を追加
- **検証結果**:
  - 旧色 grep（`0e3d38|d7ee87|fff4d6`）: **0件**（確認済み）
  - theme-color 5箇所すべて `#11153f`: 確認済み
  - キャッシュバスター: favicon.svg 4ページ同値 / styles.css 4ページ同値、確認済み
  - ローカルサーバー（`python -m http.server 8777`）で4ページすべてを開き、**コンソールエラー・CSP違反ゼロ**を確認
  - 計算済みスタイルで色を実測: ヘッダー `.brand-dot` = `rgb(31,174,195)`（`#1faec3`、**変更なし**）／フッター `.brand-dot` = `rgb(71,196,208)`（`#47c4d0`）で、隣接する `.footer-inner .brand-mark` の背景色と完全一致
  - 375px / 768px / 1280px で横スクロール発生なし（`scrollWidth === clientWidth`）
  - `.htaccess` の docs/tools 配信拒否: **本番で検証済み**（2026-08-09、https://www.sei-ko.org/ で実測）
    - `403`: `/docs/design-refresh/README.md` / `/docs/design-refresh/PROGRESS.md` / `/docs/design-refresh/phase-0-brand-unify.md` / `/tools/google-sheets-relay.gs` / `/docs/` / `/tools/`
    - `200`（巻き添えなし）: `/styles.css?v=20260809.2` / `/favicon.svg?v=20260809.1` / `/` / `/guide.html` / `/privacy.html` / `/404.html`
    - apex → www の 301 も維持されている（`https://sei-ko.org/` → `https://www.sei-ko.org/` を確認）。新ルールを既存リダイレクトの前に挿入したが干渉なし
  - 本番配信内容の実測（2026-08-09）: 4ページとも theme-color `#11153f` / favicon `?v=20260809.1` / css `?v=20260809.2`。配信中の `favicon.svg` に含まれる色は `#11153f` `#35bfd2` `#35bfd2` `#ffffff` のみで、旧ブランド色は含まれない
  - 本番トップページの読み込みでコンソールエラー・CSP違反なし（HTML / hero webp / styles.css / script.js すべて 200）
- **次フェーズへの申し送り**:
  - `favicon.svg` は**色だけ**直した状態。図柄はモニター＋星のまま、`aria-label` も旧ブランド名「さいたま 社長のパソコン・AIお助け隊」のまま。Phase 1 で円形＋「担」に作り直し、aria-label も差し替えること
  - Phase 1 でファビコンを作り直したら、キャッシュバスターは `?v=20260809.2` 以降（または作業日の日付）に上げること。現在値は `?v=20260809.1`
  - `favicon-32.png` / `apple-touch-icon.png` / `icon-192.png` / `icon-512.png` は**旧図柄・旧色のまま**（`?v=20260801`）。Phase 0 のスコープ外なので手を付けていない。Phase 1 で SVG と揃えて再生成が必要
- **スコープ外で気づいた点**:
  - `footer { color: #b4c6bc }`（`styles.css:1273`）は緑がかったグレー。旧ブランドの名残の可能性があるが、指定された廃止色3つには含まれないため未変更
  - `styles.css` には `--pine` が `--ink` と同値で残っている。整理は差分が広がるため見送り

---

## Phase 1 事前検証メモ（2026-08-09 実施 / Phase 1 はまだ未着手）

Phase 1 のツールチェーンをスクラッチパッドで**実際に通して確認済み**。次の担当はここから始めれば、環境調査をやり直さなくてよい。
リポジトリには何も追加していない（`package.json` / `node_modules` なし、生成物も未コピー）。

### 確認済みの環境

Node v24.19.0 / npm 11.17.0 / Python 3.14.6。`npm i sharp png-to-ico fontkit @fontsource/noto-sans-jp` は**成功する**（ネットワーク可）。

### ⚠ phase-1-favicon.md の記述と実際の食い違い（重要）

手順書 Step 2 は `fontkit.openSync('<Noto Sans JP Bold の .ttf パス>')` と書いているが、
**`@fontsource/noto-sans-jp` に `.ttf` は含まれない。** 実際には `files/` 配下に
**woff / woff2 のみ、しかも2250ファイルのサブセット分割**で入っている。そのまま書くとパスが見つからず詰まる。

実測で判明した正しい入口:

- 「担」は **`node_modules/@fontsource/noto-sans-jp/files/noto-sans-jp-104-700-normal.woff`** に入っている（700=Bold 相当のサブセットは125個あり、その104番）
- `fontkit.openSync()` は **woff をそのまま読める**（woff2 は不要）
- 取得値: `unitsPerEm: 1000` / bbox `minX:26, minY:-82, maxX:965, maxY:850` / パスコマンド51個
- サブセットを総当たりして `font.layout('担').glyphs[0]` の `id !== 0` かつ `path.commands.length > 0` で判定するのが確実（ファイル名から中身は特定できない）

### ⚠ png-to-ico のインポート形式

手順書の検証コマンドはそのままで問題ないが、生成スクリプト側は注意。
png-to-ico **v3.0.2 は ESM interop 形式**で、`require('png-to-ico')` は関数ではなくオブジェクトを返す。

```js
const pngToIco = require('png-to-ico').default;  // .default が必要
```

### 通ることを確認済みのパイプライン

SVG組み立て → `sharp(Buffer.from(svg), {density: 384}).resize(px, px).png()` → 16/32/48/180/512 すべて生成成功
→ `pngToIco(['t-16.png','t-32.png','t-48.png'])` → **`.ico` に 16x16 / 32x32 / 48x48 の3枚が内包**されることを確認（検証手順 6-2 の期待結果と一致）。

注: sharp のデフォルト density では 512px で粗くなるため `density: 384` を明示した。
また sharp 出力は既定で alpha 付き。`apple-touch-icon.png` は**透過なしが要件**なので `.flatten({background:'#11153f'})` を挟むこと（今回の検証では未適用、全出力が alpha 付きだった）。

### 案A（担そのまま）の16px実測結果

上記パイプラインで円形 `#11153f` 地に白の「担」を実際にラスタライズし、実寸で目視した。

- **16px: 手順書の予測どおり潰れる。** 濃紺の丸に白い横線がにじむだけで、字として判読できない
- 48px: 明瞭に「担」と読める

→ **案A は 16px（ブラウザのタブ実寸）では機能しない見込みが濃厚。** ただし採否は
phase-1-favicon.md Step 1 のとおり**ユーザーが決める**こと。AIが先回りして案Bに確定しない。
比較用の画像は今回セッションのスクラッチパッドにあり次セッションでは消えているため、提示用には作り直しが必要。
