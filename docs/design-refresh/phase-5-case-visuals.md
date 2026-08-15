# Phase 5: USE CASES のタブ別ビジュアル

## 0. 前提（必ず最初に読む）

1. `docs/design-refresh/00-common-constraints.md` — 全フェーズ共通の制約
2. `docs/design-refresh/00-brand-tokens.md` — ブランド仕様（「3. アイコン作図ルール」）
3. `docs/design-refresh/PROGRESS.md` — **Phase 2 の記録を必ず読む**（そこで採用した図像と揃える必要がある）

対象リポジトリ: `C:\Users\murph\Desktop\sei-ko.org`

**Phase 0 と Phase 2 の完了が前提。** Phase 2 で作ったサービスアイコンとトーンを揃えるため、先にそちらを終わらせること。

---

## 1. ゴール

「活用イメージ」セクション（`#cases`）には WEB / EC / AI の3タブがあり、タブを切り替えると見出し・説明・指標・引用がすべて入れ替わる。

**しかし左側のビジュアル（`.case-visual`）だけは、3タブとも同じCSS製の抽象図形。** 変わるのは中央の大きな文字（`WEB` / `EC` / `AI`）とキャプションだけ。

現在の図形（`.blueprint`）の中身:

- 斜めに傾けた（`transform: skewY(-8deg)`）L字の枠
- 水平線2本・垂直線1本・正方形2個
- 何を表しているのか説明のつかない装飾

これは「タブを切り替えても左半分がほぼ変わらない」という体験になっており、セクション全体で最も面積を取っている場所が、最も情報を持っていない状態。

**このフェーズが完了すると:**

- 3タブそれぞれに、その分野の内容を示す線画のSVG図解が入る
- タブ切り替え時に左側のビジュアルも変わる
- ビジュアルの配色がサイトのシアン系に統一される（現在は緑が混ざっている）

### 副次的に直す問題

`.bp-line` / `.bp-square` の線の色が **`#86b89e`（くすんだ緑）**。周囲は `#cfeef1`（淡いシアン背景）・`#55bfc9`（シアン枠）・`#107a91`（濃いシアン文字）なので、**この線だけが緑**で浮いている。旧ブランドの名残と思われる。該当ルールは今回まるごと削除するため、自然に解消される。

---

## 2. 対象ファイルと現状

### 2-1. `index.html:267-279`（現在のビジュアル部分）

```html
<article class="case-panel" id="casePanel">
  <div class="case-visual">
    <p class="visual-tag">USE CASE / <span id="caseNo">01</span></p>
    <div class="blueprint">
      <span class="bp-line l1"></span><span class="bp-line l2"></span><span class="bp-line l3"></span><span
        class="bp-square s1"></span><span class="bp-square s2"></span><b id="caseBadge">WEB</b>
    </div>
    <p class="visual-caption" id="caseVisualText">
      <span class="nobr">直したい場所だけ、</span>
      <br class="pc-only" />
      <span class="nobr">すぐ直せる状態に。</span>
    </p>
  </div>
  <div class="case-content">
    ...
  </div>
</article>
```

### 2-2. `script.js` の該当箇所

**`CASE_DATA`（44〜78行）** — 3つのキー `web` / `ec` / `ai` を持ち、各々が以下のプロパティを持つ:

```
no, type, title, desc, badge, metric, metricLabel, quote, visual
```

`visual` は**キャプションのテキスト**（図ではない）。

**`elements`（84〜100行）** — DOM参照を起動時に1度だけ取得している:

```js
const elements = {
  casePanel: ..., caseTabs: ..., caseNo: ..., caseType: ...,
  caseTitle: ..., caseDesc: ..., caseMetric: ..., caseMetricLabel: ...,
  caseQuote: ..., caseVisualText: ..., caseBadge: ...,
  ...
};
```

**`updateCaseContent(data)`（120〜130行）** — 各要素に値を流し込む:

```js
function updateCaseContent(data) {
  elements.caseNo.textContent = data.no;
  ...
  elements.caseVisualText.innerHTML = data.visual;
  elements.caseBadge.textContent = data.badge;
}
```

> **重要**: `elements` は起動時にDOM参照を固定している。HTMLから要素を削除すると、対応する `elements.xxx` が `null` になり、`updateCaseContent` が `TypeError` で落ちて**タブ切り替えが完全に停止する**。要素の削除とJS側の修正は必ずセットで行うこと。

### 2-3. 削除対象のCSS（`styles.css` 776〜835行）

| セレクタ | 行 |
|---|---|
| `.blueprint` | 776 |
| `.blueprint b` | 788 |
| `.bp-line` | 796 |
| `.l1` / `.l2` / `.l3` | 801 / 807 / 813 |
| `.bp-square` | 820 |
| `.s1` / `.s2` | 827 / 832 |

**残すもの**: `.case-visual`（762）/ `.visual-tag`（769）/ `.visual-caption`（837）

### 2-4. レスポンシブ

`styles.css:1529` 付近に `.case-visual { height: 260px }` がある（768px以下）。PC時は `.case-panel` のグリッドで高さが決まる。**新しい図解はこの両方の高さで成立する必要がある。**

---

## 3. 作業手順

### Step 1: 図解の仕様を決める

Phase 2 のアイコンは 24×24 の小さなアイコンだったが、ここは**もっと大きな図解**。`00-brand-tokens.md` の作図ルールを、サイズに合わせて拡張する。

| 項目 | 規定 |
|---|---|
| `viewBox` | `0 0 320 240` に統一（3つとも同じ） |
| 描画方式 | 線画（stroke）。Phase 2 と揃える |
| `fill` | 原則 `none`。面で示す必要がある箇所のみ `#ffffff` または `rgba` の淡い塗り |
| `stroke` | `currentColor` |
| `stroke-width` | `2.5`（320幅に対する見た目が 24幅の 1.75 と釣り合う値） |
| `stroke-linecap` / `stroke-linejoin` | `round` |
| `aria-hidden` | `true`（意味は隣の見出し・キャプションが担う） |
| `width`/`height` 属性 | 付けない。CSSで制御 |
| 余白 | 実描画は 20〜300 / 20〜220 の範囲に収める |

**色は `.case-visual` 側の CSS で `color` を指定し、`currentColor` で拾う。** 背景 `#cfeef1` の上なので、`#107a91`（既存の `.blueprint b` と同じ濃いシアン）が読みやすい。

### Step 2: 3つの図解を設計する

各タブの内容（`script.js` の `desc` より）に対応させる。

| タブ | 内容の要点 | 図解の方向性 |
|---|---|---|
| **web** | 問い合わせフォームが動かない / 一文だけ直したい / WordPress・ドメイン | ブラウザウィンドウの中の一部分だけがハイライトされ、そこにカーソルや修正マークが当たっている図。「全体ではなく一部を直す」が伝わること |
| **ec** | 初期設定・商品登録・決済・配送・テーマ / 送料設定だけ分からない | 店舗の導線図。商品 → カート → 決済 → 配送 の流れを4つのノードで示し、1つに「設定中」の印を付ける |
| **ai** | ChatGPTで文章 / スプレッドシート整理 / 定型業務 / **最終確認は必ず人が行う** | 繰り返し作業の束が絞り込まれ、最後に人のチェックが入る図。**「人の確認」を必ず含めること**（このタブの `quote` と `metricLabel` が明示している核心） |

**制約:**

- 3つを並べたときに**線の密度・要素数が揃っている**こと。1つだけ描き込みが多いと切り替えたときに違和感が出る
- 具体的なサービスのロゴ（Shopify、OpenAI、WordPress など）を**描かない**。商標
- 文字を図の中に入れない（多言語化・可読性の問題。ラベルは既存の `#caseBadge` と `.visual-caption` が担う）

### Step 3: `script.js` に図解データを追加する

`CASE_DATA` の各キーに `figure` プロパティを追加する。

```js
const CASE_DATA = {
  web: {
    no: '01',
    ...
    visual: '<span class="nobr">直したい場所だけ、</span>...',
    figure: '<svg viewBox="0 0 320 240" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">...</svg>'
  },
  ...
};
```

- **既存の `visual` プロパティは残す**（キャプションのテキストで、別の役割）
- SVG文字列は静的リテラルのみ。外部入力を混ぜない

### Step 4: `elements` に参照を追加する

```js
const elements = {
  ...
  caseVisualText: document.querySelector('#caseVisualText'),
  caseFigure: document.querySelector('#caseFigure'),   // ← 追加
  caseBadge: document.querySelector('#caseBadge'),
  ...
};
```

### Step 5: `updateCaseContent` に反映処理を追加する

```js
function updateCaseContent(data) {
  ...
  elements.caseVisualText.innerHTML = data.visual;
  elements.caseFigure.innerHTML = data.figure;   // ← 追加
  elements.caseBadge.textContent = data.badge;
}
```

### Step 6: `index.html` を書き換える

```html
<div class="case-visual">
  <p class="visual-tag">USE CASE / <span id="caseNo">01</span></p>
  <div class="case-figure" id="caseFigure" aria-hidden="true">
    <!-- 初期表示（web）のSVGをここに直接書く -->
  </div>
  <b id="caseBadge">WEB</b>
  <p class="visual-caption" id="caseVisualText">
    <span class="nobr">直したい場所だけ、</span>
    <br class="pc-only" />
    <span class="nobr">すぐ直せる状態に。</span>
  </p>
</div>
```

**重要な注意点:**

- **`.blueprint` の `div` と中の5つの `span` を削除する**
- **`<b id="caseBadge">` は削除せず、`.case-visual` 直下に移動する。** 削除すると `elements.caseBadge` が `null` になり、タブ切り替えが `TypeError` で停止する
- **初期表示用のSVGを `#caseFigure` の中に直接書く。** JSが無効・読み込み前でも図が出るようにするため（他の要素も同じ方針でHTMLに初期値が書かれている）
- 書くのは `CASE_DATA.web.figure` と**同一のSVG**。片方だけ直して不整合にしないこと

### Step 7: CSSを差し替える

`styles.css` の 776〜835行（`.blueprint` 〜 `.s2`）を削除し、代わりに追加する。

```css
/* ケース図解: タブ切り替えでSVGが差し替わる */
.case-figure {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: min(78%, 300px);
  color: #107a91;
}

.case-figure svg {
  width: 100%;
  height: auto;
  display: block;
}

/* 分野バッジ */
.case-visual > b {
  position: absolute;
  right: 30px;
  top: 78px;
  color: #107a91;
  font: 500 44px Georgia, serif;
}
```

- `.case-visual` は既に `position: relative`（762行）なので絶対配置の基準になる
- バッジの `font: 500 44px Georgia, serif` は既存の `.blueprint b` から引き継ぐ。**図解と重ならない位置に調整すること**（重なるならバッジ側の位置を動かす）
- `.visual-caption`（837行）は右下に絶対配置されている。**図解と重ならないこと**を必ず目視で確認する

### Step 8: レスポンシブを確認する

768px以下では `.case-visual { height: 260px }`。PC時よりかなり低い。**図解がキャプションやバッジと重ならないか、両方の幅で確認する。** 必要なら 1529行付近のメディアクエリに `.case-figure` の指定を追加する。

### Step 9: キャッシュバスターを更新する

`styles.css` と `script.js` の**両方**を変更したので、4ページ分のクエリを上げる。

- 現在: `styles.css?v=20260809.1` / `script.js?v=20260808.2`
- `script.js` は `index.html` のみが読み込んでいる（他3ページは読み込んでいない）。**`styles.css` は4ページ、`script.js` は index.html のみ**

---

## 4. やってはいけないこと

- **`<b id="caseBadge">` を削除する** — `elements.caseBadge` が `null` になりタブ切り替えが停止する
- **`elements` への追加を忘れて `updateCaseContent` だけ書く** — 同じく `TypeError`
- **`CASE_DATA` の既存プロパティ（`no` / `type` / `title` / `desc` / `badge` / `metric` / `metricLabel` / `quote` / `visual`）を変更・削除する** — 本文コピーはスコープ外
- **初期表示のSVGと `CASE_DATA.web.figure` を別物にする**
- **図の中に文字を描く**
- **サービスのロゴ・商標を描く**
- **`innerHTML` に外部入力を混ぜる** — 静的リテラルのみ
- **`.case-visual` / `.visual-tag` / `.visual-caption` を削除する** — 残すもの
- **3つの図解で作図ルール（viewBox / stroke-width）を変える**
- **`git commit` しない**

---

## 5. 完了条件

- [ ] `web` / `ec` / `ai` の3つの図解SVGができている
- [ ] 3つとも `viewBox="0 0 320 240"` / `stroke="currentColor"` / `stroke-width="2.5"` / `aria-hidden="true"`
- [ ] SVGに `width` / `height` 属性がない
- [ ] `CASE_DATA` の3キーすべてに `figure` がある
- [ ] `elements.caseFigure` が追加されている
- [ ] `updateCaseContent` に `caseFigure` の反映行がある
- [ ] `index.html` の初期表示SVGが `CASE_DATA.web.figure` と一致している
- [ ] `.blueprint` / `.bp-line` / `.bp-square` / `.l1〜.l3` / `.s1` / `.s2` がHTML・CSS双方から消えている
- [ ] `#caseBadge` が残っており、タブ切り替えで `WEB`/`EC`/`AI` が変わる
- [ ] 緑（`#86b89e`）がケースセクションから消えている
- [ ] 図解がバッジ・キャプションと重なっていない（PC / 768px以下の両方）
- [ ] `styles.css`（4ページ）と `script.js`（index.html）のキャッシュバスターを更新した
- [ ] `PROGRESS.md` に記録を追記した

---

## 6. 検証手順

### 6-1. 旧ブループリントが消えたことの確認

```bash
grep -n "blueprint\|bp-line\|bp-square" index.html styles.css
grep -n "86b89e" styles.css
```

**期待結果: いずれも0件**

### 6-2. JS側の整合確認

```bash
grep -n "caseFigure\|figure:" script.js
```

**期待結果**: `elements` に1件、`updateCaseContent` に1件、`CASE_DATA` に3件（`figure:`）＝計5件

### 6-3. 初期表示SVGとJSデータの一致確認

`index.html` の `#caseFigure` 内のSVGと、`script.js` の `CASE_DATA.web.figure` の中身を**目視で突き合わせる**。パスデータが1文字でも違えば、初回表示とタブ再選択時で図が変わってしまう。

### 6-4. タブ切り替えの動作確認【最重要】

```bash
python -m http.server 8777
```

`http://localhost:8777/` を開き、**ブラウザのコンソールを開いたまま**:

1. 「WEB制作・改善」→「Shopify・EC」→「AI・業務効率化」→「WEB制作・改善」の順にクリックする
2. **毎回、図解・バッジ・見出し・説明・指標・引用がすべて切り替わる**こと
3. **コンソールに `TypeError` が1件も出ないこと**（`elements.caseFigure is null` などが出たら Step 4 の漏れ）
4. キーボードの矢印キーでもタブ移動できること（既存のアクセシビリティ実装）

### 6-5. レイアウト確認

- **PC（1280px）**: 図解 / バッジ / キャプション（右下）が重なっていない
- **768px以下**: `.case-visual` の高さが260pxに縮む。この状態でも重なっていない
- **375px**: 図解が小さくなりすぎて潰れていないか

3タブすべて × 3幅で確認すること（計9パターン）。

### 6-6. 図解の意味が伝わるかの確認

**ユーザーに3つの図を見てもらい、説明なしで何を表しているか分かるか確認する。** 特に AI タブは「最終確認は人が行う」が伝わるかを重点的に確認する。分からないと言われたら描き直す。

---

## 7. PROGRESS.md への追記内容

```markdown
### Phase 5: USE CASES のタブ別ビジュアル

- **状態**: 完了
- **日付**: YYYY-MM-DD
- **キャッシュバスター**: styles.css `?v=...`（4ページ）/ script.js `?v=...`（index.html のみ）
- **変更ファイル**:
  - `index.html` — .blueprint を削除し #caseFigure を追加、#caseBadge を .case-visual 直下へ移動
  - `script.js` — CASE_DATA に figure を3件追加、elements に caseFigure、updateCaseContent に反映行
  - `styles.css` — .blueprint 系9ルールを削除、.case-figure / .case-visual > b を追加
- **採用した図解**: web=... / ec=... / ai=...（何を描いたか一言ずつ）
- **副次的に解消**: `.bp-line` / `.bp-square` の緑 `#86b89e` を削除
- **検証結果**: 上記6-1〜6-6の結果。特に 6-4 のタブ切り替え×コンソールエラーなしを明記
- **次フェーズへの申し送り**:
  - Phase 6（guide のフロー図）は、ここで決めた大きめ図解の仕様（viewBox 320×240 / stroke-width 2.5）を参考にすること
- **スコープ外で気づいた点**: （あれば）
```
