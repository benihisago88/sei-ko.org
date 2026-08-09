# Phase 7: 仕上げと全体検証

## 0. 前提（必ず最初に読む）

1. `docs/design-refresh/00-common-constraints.md` — 全フェーズ共通の制約
2. `docs/design-refresh/00-brand-tokens.md` — ブランド仕様
3. `docs/design-refresh/PROGRESS.md` — **Phase 0〜6 の記録をすべて読む**。特に各フェーズの「未検証」項目と「申し送り」

対象リポジトリ: `C:\Users\murph\Desktop\sei-ko.org`

**Phase 0〜6 がすべて完了していること。** 未完了のフェーズがある場合は、何が残っているかをユーザーに報告してから、実施済み範囲だけで検証を行う。

---

## 1. ゴール

このフェーズは**2つの性格**を持つ。

### A. 全体検証（必須）

Phase 0〜6 は別々のセッションで実行されるため、**フェーズ間の不整合が残りやすい**。特に:

- キャッシュバスターが4ページで揃っていない
- あるフェーズで削除したファイルへの参照が別ページに残っている
- `<head>` の構成がページごとにずれている

これらを横断的に洗い出して潰す。**このフェーズの主目的はこちら。**

### B. 残りの視覚的な補強（任意・優先度低）

デザインレビューで「優先度：低」と判定された項目。**時間と必要性に応じて、ユーザーと相談して取捨選択する。全部やる必要はない。**

- プロセスのタイムライン（STEP 01〜04）が数字のみ
- 料金カード3枚に視覚的な差がほとんどない
- `404.html` にOGPタグがない

---

## 2. 対象ファイルと現状

### 2-1. 4ページの `<head>` 構成

| ページ | robots | canonical | OGP | 構造化データ |
|---|---|---|---|---|
| `index.html` | index,follow | あり | あり | ProfessionalService / WebSite / FAQPage |
| `guide.html` | index,follow | あり | あり | Article |
| `privacy.html` | **noindex**,follow | あり | あり | なし |
| `404.html` | **noindex**,follow | なし | **なし** | なし |

### 2-2. タイムライン（`index.html:311-344`）

```html
<ol class="timeline">
  <li class="reveal">
    <span>01</span>
    <div><h3>相談内容を送る</h3><p>困っていることをそのまま送信。まとまっていなくて大丈夫です。</p></div>
    <b>STEP 01</b>
  </li>
  ... 04まで
</ol>
```

関連CSS: `styles.css:914`（9. プロセス・タイムライン）※Phase 4 でセクション番号が繰り下がっている場合あり

### 2-3. 料金カード（`index.html:360-379`）

```html
<article class="price-card reveal">           <p class="plan-label">SPOT</p> ...
<article class="price-card featured reveal delay-1"> <p class="plan-label">RECOMMENDED</p> ...
<article class="price-card reveal delay-2">   <p class="plan-label">CONTINUOUS</p> ...
```

関連CSS: `styles.css:1021` 付近（`.price-card` / `.price-card.featured`）

### 2-4. `sitemap.xml`

```xml
<loc>https://www.sei-ko.org/</loc>          <lastmod>2026-08-09</lastmod>
<loc>https://www.sei-ko.org/guide.html</loc> <lastmod>2026-08-09</lastmod>
```

コンテンツを更新したので `lastmod` を更新する。

---

## 3. 作業手順

### Step 1: 全体整合の検証を先に実行する

**補強作業より先に検証を回す。** 不整合が見つかったらそちらを直すのが最優先。

検証は「6. 検証手順」の 6-1〜6-6 を上から順に実行する。見つかった不整合をすべて修正してから Step 2 へ進む。

### Step 2: 未検証項目の棚卸し

`PROGRESS.md` から「**未検証**」と書かれた項目を全部拾い出し、リスト化してユーザーに提示する。

既知のもの:

| 項目 | 出典 | 確認方法 |
|---|---|---|
| `.htaccess` の `docs/` `tools/` 配信拒否 | Phase 0 | 本番で `https://www.sei-ko.org/docs/design-refresh/README.md` が **403** |
| OGPのSNS実表示 | Phase 6 | 本番反映後、X Card Validator / Facebook Sharing Debugger |

**これらはローカルでは検証できない。** 「本番反映後にユーザーが確認する項目」として明示的に引き渡すこと。**AIが勝手に完了扱いにしない。**

### Step 3:【任意】タイムラインのアイコン追加

**ユーザーに実施するか確認してから着手する。**

- `00-brand-tokens.md`「3. アイコン作図ルール」に従った 24×24 の線画SVGを4つ
- 各 `<li>` の `<span>01</span>` の**代わりではなく併記**にするか、置き換えるかを決める（番号は進行状況を示す情報なので、消すと分かりにくくなる可能性がある）
- 図像: 01=送信 / 02=確認 / 03=見積 / 04=納品 の流れが伝わるもの

**判断基準**: タイムラインは既に「番号 + 見出し + 説明 + STEP ラベル」の4要素があり、情報は足りている。アイコンは装飾の追加になる。**入れることで情報密度が上がりすぎないかを見て決める。**

### Step 4:【任意】料金カードの視覚的な差別化

**ユーザーに実施するか確認してから着手する。**

現状 `.price-card.featured` にのみ装飾が付いている。3枚の違いは `plan-label`（SPOT / RECOMMENDED / CONTINUOUS）と価格の文字だけ。

やるなら**アイコンを足すのではなく、既存の要素で差をつける**方が軽い:

- `plan-label` の色を3段階にする（`--muted` → `--lime` → `--ink` など）
- カード上部のアクセントライン（`.service-card:before` と同じ手法）を3色にする

**アイコンの追加は推奨しない。** 料金表は数字を読ませる場所で、装飾が増えると比較しにくくなる。

### Step 5:【任意】404.html にOGPを追加

**判断**: `404.html` は `noindex` で、SNSで意図的に共有されることはまずない。ただし**リンク切れのURLが共有されたとき**に、プレビューが真っ白になるより「デジタル担当室」と出た方がよい。

やるなら最小限に:

```html
<meta property="og:type" content="website" />
<meta property="og:title" content="ページが見つかりません | デジタル担当室" />
<meta property="og:site_name" content="デジタル担当室" />
<meta property="og:locale" content="ja_JP" />
<meta property="og:image" content="https://www.sei-ko.org/assets/ogp.jpg?v=V" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:image" content="https://www.sei-ko.org/assets/ogp.jpg?v=V" />
```

`canonical` は**追加しない**（404ページに canonical を付けると、存在しないURLを正規化してしまう）。

### Step 6: `sitemap.xml` の `lastmod` を更新

```xml
<lastmod>YYYY-MM-DD</lastmod>
```

`/` と `/guide.html` の両方を、実際に更新した日付にする。**未来の日付を書かない。**

### Step 7: キャッシュバスターの最終統一

すべての変更が終わった時点で、4ページのクエリを**最終的な値に揃える**。

- `styles.css?v=` — 4ページで同一
- `script.js?v=` — `index.html` のみ
- アイコン系 `?v=` — 4ページで同一
- `site.webmanifest?v=` — 4ページで同一
- OGP `?v=` — `index.html` / `privacy.html` は `ogp.jpg`、`guide.html` は `ogp-guide.jpg`

### Step 8: `PROGRESS.md` の最終まとめを書く

全フェーズの結果を1つの表にまとめ、**本番反映後にユーザーがやるべきことのチェックリスト**を書く。

---

## 4. やってはいけないこと

- **検証より先に補強作業を始める** — 不整合の修正が最優先
- **任意項目をユーザー確認なしに全部やる** — 「優先度：低」と判定された項目。やらない判断も正しい
- **未検証項目を検証済みとして報告する** — `.htaccess` とSNS表示は本番でしか確認できない
- **404.html に `canonical` を追加する**
- **`sitemap.xml` に `privacy.html` / `404.html` を追加する** — 両方 `noindex`
- **`sitemap.xml` に未来の日付を書く**
- **料金カードにアイコンを足す** — 比較しにくくなる
- **これまでのフェーズの成果物を「もっと良くしよう」として作り直す** — 検証と仕上げのフェーズであって、やり直しのフェーズではない
- **`git commit` しない**

---

## 5. 完了条件

- [ ] 検証 6-1〜6-6 をすべて実行し、見つかった不整合を修正した
- [ ] `PROGRESS.md` の「未検証」項目を全部拾い出し、ユーザーに引き渡した
- [ ] 任意項目（Step 3/4/5）について、実施するかどうかユーザーの判断を得た
- [ ] `sitemap.xml` の `lastmod` を更新した
- [ ] 4ページのキャッシュバスターが最終的に揃っている
- [ ] `PROGRESS.md` に全フェーズの総括と、本番反映後のチェックリストを書いた

---

## 6. 検証手順

### 6-1. 旧ブランドの残骸チェック

```bash
grep -rniI "0e3d38\|d7ee87\|fff4d6\|86b89e\|リモートお助け隊\|さいたま\|お助け隊" . \
  --exclude-dir=.git --exclude-dir=docs --exclude-dir=.idea
```

**期待結果: 0件**（`docs/` はこのプラン文書自体が旧色を記述しているので除外する）

### 6-2. 参照切れチェック

HTML内で参照している自サイトのファイルが、実際に存在するかを確認する。

**スクラッチパッドに `linkcheck.js` として保存してから実行する**（`node -e` のワンライナーにすると、シェルのクォート処理で正規表現が壊れる）。

```js
const fs = require('fs');
const ORIGIN = 'https://www.sei-ko.org/';
const pages = ['index.html', 'guide.html', 'privacy.html', '404.html'];
let bad = 0, checked = 0;

for (const p of pages) {
  const html = fs.readFileSync(p, 'utf8');
  const urls = [];
  // href / src はすべて対象
  for (const m of html.matchAll(/(?:href|src)="([^"]+)"/g)) urls.push(m[1]);
  // content= は自サイトの絶対URLのときだけ対象（OGP画像など）
  for (const m of html.matchAll(/content="([^"]+)"/g)) {
    if (m[1].startsWith(ORIGIN)) urls.push(m[1].slice(ORIGIN.length - 1));
  }
  for (let u of urls) {
    if (/^(https?:|mailto:|tel:|data:)/.test(u)) continue;
    const q = u.split('#')[0].split('?')[0].replace(/^\//, '');
    if (!q || q.endsWith('/')) continue;
    checked++;
    if (!fs.existsSync(q)) { console.log('MISSING:', p, '->', u); bad++; }
  }
}
console.log(bad === 0 ? `OK: 参照切れなし (${checked}件を確認)` : `NG: ${bad}件 / ${checked}件中`);
```

```bash
node <スクラッチパッド>/linkcheck.js
```

**期待結果**: `OK: 参照切れなし (N件を確認)`

> `content=` を無条件に対象にすると、`<meta name="description">` などの本文がパス扱いされて大量の誤検知が出る。上のスクリプトが自サイトの絶対URLだけに絞っているのはそのため。**この条件を外さないこと。**
>
> 着手前（Phase 0〜7 実施前）の状態でこのスクリプトを流すと **49件を確認して0件欠落**になる。実施後は追加・削除したファイルの分だけ件数が増減する。

### 6-3. `<head>` の整合チェック

```bash
grep -rn "rel=\"icon\"\|apple-touch-icon\|rel=\"manifest\"\|theme-color" --include="*.html" . | grep -v "^./docs"
```

**期待結果**: 4ページで、アイコン・manifest・theme-color の構成と値が一致（クエリも含めて）

```bash
grep -rn "styles.css?v=" --include="*.html" . | grep -v "^./docs"
```

**期待結果**: 4行すべて同じ値

### 6-4. 孤立ファイルのチェック

どこからも参照されていないファイルが残っていないか確認する。

```bash
node -e "
const fs=require('fs');
const all=fs.readdirSync('.').filter(f=>/\.(png|jpg|jpeg|webp|svg|ico)$/i.test(f))
  .concat(fs.readdirSync('assets').map(f=>'assets/'+f));
const html=['index.html','guide.html','privacy.html','404.html','site.webmanifest','styles.css','script.js']
  .map(f=>fs.readFileSync(f,'utf8')).join('\n');
all.forEach(f=>{ if(!html.includes(f.replace(/^\.\//,''))) console.log('UNREFERENCED:',f); });
console.log('done');
"
```

参照されていないファイルが出たら、**削除してよいかユーザーに確認する**（勝手に消さない）。

### 6-5. 全ページのブラウザ確認

```bash
python -m http.server 8777
```

4ページ × 3幅（375 / 768 / 1280）＝ **12パターン**を確認する。

各パターンで:

- [ ] コンソールにエラー・CSP違反・404が**1件も出ていない**
- [ ] レイアウトが崩れていない
- [ ] ファビコンが新図柄で表示されている

`index.html` では追加で:

- [ ] ケースタブの3つすべてが切り替わり、`TypeError` が出ない
- [ ] 診断フォーム（`#readinessForm`）が動作する
- [ ] スクロールアニメーション（`reveal`）が効いている

### 6-6. アクセシビリティ確認

DevTools の Lighthouse で 4ページの Accessibility を実行する。

- **着手前と比べてスコアが下がっていないこと**（上がっていなくてもよい）
- 新規追加したSVGが原因の指摘（`aria-hidden` 漏れ、role の誤りなど）が出ていないこと

さらに手動で:

- [ ] 装飾用SVG（サービスアイコン / ケース図解 / ブランドマーク）に `aria-hidden="true"` が付いている
- [ ] 情報を持つSVG（guide のフロー図）に `role="img"` + `aria-label` が付いている
- [ ] キーボードのTabだけで、全ページの主要リンク・フォームに到達できる
- [ ] `skip-link`（本文へ移動）が機能する

---

## 7. PROGRESS.md への追記内容

```markdown
### Phase 7: 仕上げと全体検証

- **状態**: 完了
- **日付**: YYYY-MM-DD
- **最終キャッシュバスター**: styles.css `?v=...` / script.js `?v=...` / アイコン系 `?v=...` / OGP `?v=...`
- **検証で見つかった不整合と修正内容**:
  - （見つかったものを列挙。0件なら「なし」と明記）
- **任意項目の判断**:
  - タイムラインのアイコン: 実施 / 見送り（理由）
  - 料金カードの差別化: 実施 / 見送り（理由）
  - 404 のOGP: 実施 / 見送り（理由）
- **変更ファイル**: （実施したもの）
- **検証結果**: 6-1〜6-6 の結果。Lighthouse のスコアは着手前後の数値を記載

---

## 総括

| Phase | 内容 | 状態 |
|---|---|---|
| 0〜7 | （各フェーズの結果を1行ずつ） | |

## 本番反映後にユーザーが確認すること

- [ ] `https://www.sei-ko.org/docs/design-refresh/README.md` が **403** を返す（Phase 0）
- [ ] `https://www.sei-ko.org/tools/google-sheets-relay.gs` が **403** を返す（Phase 0）
- [ ] `https://www.sei-ko.org/favicon.ico` が 200 で画像を返す（Phase 1）
- [ ] X (Twitter) Card Validator で新しいOGPが表示される（Phase 6）
- [ ] Facebook Sharing Debugger で**キャッシュを再取得**し、新しいOGPが表示される（Phase 6）
- [ ] Google Search Console でファビコンの再クロールを待つ（反映に数日〜数週間かかる）
- [ ] スマホでホーム画面に追加し、アイコンの角が欠けていないことを確認（Phase 1）
```
