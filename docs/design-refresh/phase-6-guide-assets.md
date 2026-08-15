# Phase 6: OGP画像の刷新と guide.html の図解追加

## 0. 前提（必ず最初に読む）

1. `docs/design-refresh/00-common-constraints.md` — 全フェーズ共通の制約
2. `docs/design-refresh/00-brand-tokens.md` — ブランド仕様
3. `docs/design-refresh/PROGRESS.md` — **Phase 1 の記録を必ず読む**（確定したブランドマークをOGPに使う）

対象リポジトリ: `C:\Users\murph\Desktop\sei-ko.org`

**Phase 0 と Phase 1 の完了が前提。**

> ⚠ **このフェーズには緊急度の高い項目が含まれる。** 下記「1-1」のとおり、現在SNSに表示されている画像が**旧ブランド名のまま**。時間がないときは 1-1 だけでも先に実施してよい。

---

## 1. ゴール

### 1-1.【緊急】現在のOGP画像が旧ブランドのまま

`assets/ogp.jpg` は **4ページすべて**（`index.html` / `guide.html` / `privacy.html`）が `og:image` と `twitter:image` に指定している画像。**X・Facebook・LINE・Slack などでURLを共有したときに表示される、サイトの顔**。

中身を確認したところ、**全面的に旧ブランドのまま**だった。

| 画像内の要素 | 現在の内容 | 問題 |
|---|---|---|
| 英字ラベル | `SAITAMA / PC & AI SUPPORT` | 「さいたま」= 旧ブランドの地域訴求。現サイトは**全国オンライン対応** |
| 大見出し | **「リモートお助け隊」** | **旧ブランド名。現在は「デジタル担当室」** |
| 説明文 | 「パソコン・Excel・AIの困りごとを、社長のそばで解決します。」 | 旧ポジショニング。現在は **WEB・EC・AI**、かつ「そばで」ではなく**オンライン・非同期** |
| バッジ | 「初回相談無料」（**黄緑 = 旧ブランド色**） | 色が廃止色。さらに**この訴求はサイト本文のどこにも書かれていない**（現在の表現は「相談しただけで費用が発生することはありません」）。無料の範囲・回数について誤認を招く可能性がある |
| 運営表記 | 「運営：誠幸（セイコウ）」 | ここだけ現行と一致 |

さらに、HTMLの `og:image:alt` は全ページで

```
デジタル担当室 WEB・EC・AIのご相談窓口。全国オンライン対応。
```

と書かれており、**alt文と画像の実際の中身が一致していない**。

**つまり、SNSでこのサイトが共有されるたびに、サイト名と違うブランド名が表示されている状態。** サイト本体をいくら整えても、流入前の第一印象がこれでは効果が打ち消される。

### 1-2. guide.html に専用OGPがない

`guide.html` は「中小企業のためのAI導入、最初の5ステップ。」という独立した記事コンテンツで、`sitemap.xml` にも `priority 0.8` で登録されている。それがトップページと同じ画像を使っているため、共有されても記事の内容が伝わらない。

### 1-3. guide.html に図が1枚もない

`guide.html` の本文（`.article-content`、44〜50行）は STEP 01〜05 が**全文テキストのみ**。5つのステップの関係が視覚的に示されておらず、読み飛ばされやすい。

**このフェーズが完了すると:**

- OGP画像が現ブランド「デジタル担当室」になり、SNS共有時の表示とサイトが一致する
- `guide.html` に記事専用のOGPが付く
- `og:image:alt` と画像の中身が一致する
- ガイド記事に5ステップの流れを示す図が入る

---

## 2. 対象ファイルと現状

### 2-1. OGP の参照箇所

```bash
grep -rn "ogp.jpg" --include="*.html" . | grep -v "^./docs"
```

| ファイル | 箇所 |
|---|---|
| `index.html:16` | `og:image` |
| `index.html:23` | `twitter:image` |
| `guide.html:14` | `og:image` |
| `guide.html:21` | `twitter:image` |
| `privacy.html:13` | `og:image` |
| `privacy.html:19` | `twitter:image` |

`404.html` にはOGPタグ自体がない（`noindex` なので Phase 7 で扱う）。

### 2-2. 現在の `assets/ogp.jpg`

- 1200×630 / 122,044 bytes
- 左半分: 濃紺（`#11153f` 系）のパネルにテキスト
- 右半分: 写真（**`assets/hero-consultation-*.webp` と同一の写真の別トリミング**）

→ **写真素材はヒーロー画像を再利用できる。** 新しい素材を探す必要はない。

### 2-3. `guide.html` の構造

```html
<main id="main-content" class="article-page section-wrap">
  <p class="eyebrow">...</p>
  <h1>中小企業のための<br /><em>AI導入、最初の5ステップ。</em></h1>
  <p class="article-lead">...</p>
  <div class="article-toc">
    <strong>このガイドでわかること</strong>
    <ol><li>目的を「時間」や「品質」で言語化する</li>...</ol>
  </div>
  <article class="article-content">
    <section><p class="step-label">STEP 01</p><h2>解決したい仕事を、先に決める。</h2><p>...</p><aside>...</aside></section>
    <section><p class="step-label">STEP 02</p><h2>小さく、安全に試す。</h2><p>...</p></section>
    <section><p class="step-label">STEP 03</p><h2>入力してよい情報を決める。</h2><p>...</p></section>
    <section><p class="step-label">STEP 04</p><h2>できた手順を、チームの形にする。</h2><p>...</p></section>
    <section><p class="step-label">STEP 05</p><h2>月に一度、使い方を見直す。</h2><p>...</p></section>
  </article>
  <section class="article-cta">...</section>
</main>
```

関連CSS: `.article-page`（1804行）/ `.article-toc`（1830）/ `.article-content`（1848）/ `.step-label`（1861）

> **注意**: 本文が1行に詰めて書かれている（ミニファイ気味）。既存の書式に合わせること。

---

## 3. 作業手順

### Step 1: 掲載する文言をユーザーに確認する【必須】

OGPに載せる文言は**サイトの顔**なので、勝手に決めない。以下を確認する。

| 項目 | 確認内容 | 参考（サイト本文の既存表現） |
|---|---|---|
| 大見出し | 「デジタル担当室」でよいか | ヘッダーのブランド名 |
| 英字ラベル | 何にするか | `index.html` の `WEB / EC / AI SUPPORT` が使える |
| 説明文 | 何を載せるか | `og:description` の「WEB・EC・AIの『これ誰に聞けばいい？』を、ひとつの窓口で。」が既に用意されている |
| バッジ | **「初回相談無料」を残すか、削除するか、別の表現にするか** | サイト本文には「相談しただけで費用が発生することはありません」（`#pricing`）とある |
| 運営表記 | 「運営：誠幸（セイコウ）」を残すか | 現行と一致しているので通常は残す |

**「初回相談無料」について**: この表現はサイト本文のどこにも存在しない。画像だけがこの訴求をしている状態は、**書かれていない条件を約束していることになりかねない**。残す場合は本文にも同じ表現を入れるべきで、それは今回のスコープ外。**削除するか、本文と同じ「相談は無料」系の表現に合わせることを推奨し、ユーザーに判断を仰ぐ。**

### Step 2: OGP画像を生成する

`00-common-constraints.md`「6. ラスター画像の生成方法」に従い、スクラッチパッドで作業する。

日本語テキストを含む画像なので、**SVGをsharpでラスタライズする方法は使わない**（日本語フォントが解決できず豆腐になる）。**ヘッドレスブラウザでHTMLをレンダリングする。**

```bash
npm i puppeteer sharp
```

> `puppeteer` は初回に Chromium（約150MB）をダウンロードする。ネットワーク制限で失敗する場合は、代替として本セッションのブラウザツール（`preview_start` → `resize_window` 1200×630 → `screenshot`）を使う。その場合は寸法が正確に1200×630になっているかを必ず検証すること。

#### 生成パイプライン

1. スクラッチパッドに **1200×630 ちょうど**のHTMLを作る
   - 左パネル: `#11153f`、右: `assets/hero-consultation-pc-1303.webp` を `object-fit: cover` で配置
   - フォントはシステムフォント（`00-brand-tokens.md` の `--display` と同じスタック）
   - Phase 1 で確定したブランドマーク（円形＋担）を左上か左下に置く
   - 色は `00-brand-tokens.md` の採用色のみ。**廃止色（黄緑・深緑）を使わない**
2. puppeteer で **`deviceScaleFactor: 2`** でレンダリングし、2400×1260 のPNGを撮る（2倍で撮って縮小するとテキストが鮮明になる）
3. sharp で 1200×630 に縮小し、JPEG化

   ```js
   sharp(png).resize(1200, 630).jpeg({ quality: 82, mozjpeg: true }).toFile('ogp.jpg')
   ```

4. **目標: 300KB以下**

#### 2枚作る

| 出力 | 大見出し | 用途 |
|---|---|---|
| `assets/ogp.jpg`（**上書き**） | デジタル担当室 | トップ・プライバシー用 |
| `assets/ogp-guide.jpg`（新規） | 中小企業のためのAI導入、最初の5ステップ。 | ガイド記事用 |

同じテンプレートで文言だけ差し替える。**2枚のデザインが揃っていること。**

### Step 3: 生成した画像を必ず目視確認する【重要】

**生成したJPEGを `Read` ツールで開いて、自分の目で中身を確認すること。**

- 文字が切れていない・はみ出していない
- 日本語が豆腐（□）になっていない
- 旧ブランド名「リモートお助け隊」「さいたま」が**残っていない**
- 廃止色（黄緑・深緑）が**使われていない**

そのうえで**ユーザーにも画像を送って確認を取る**（`SendUserFile`）。「生成した」だけで完了にしない。

### Step 4: HTML の OGP タグを更新する

#### `guide.html`（14行・21行）

```html
<meta property="og:image" content="https://www.sei-ko.org/assets/ogp-guide.jpg?v=V" />
<meta name="twitter:image" content="https://www.sei-ko.org/assets/ogp-guide.jpg?v=V" />
```

`og:image:alt`（17行）も記事の内容に合わせて書き換える。

```html
<meta property="og:image:alt" content="中小企業のためのAI導入、最初の5ステップ。デジタル担当室" />
```

#### `index.html`（16行・23行）/ `privacy.html`（13行・19行）

ファイル名は `ogp.jpg` のままなので、**キャッシュバスターだけ更新する**（中身が変わったため）。

```html
<meta property="og:image" content="https://www.sei-ko.org/assets/ogp.jpg?v=V" />
```

`og:image:alt` は**実際に作った画像の内容と一致しているか確認し、ずれていれば直す**。

> `og:image:width` / `og:image:height`（1200 / 630）は寸法を変えないのでそのまま。

### Step 5: guide.html に5ステップのフロー図を追加する

#### 配置

`.article-toc` の**直後**、`.article-content` の**直前**。目次で全体像を示した直後に図で流れを見せ、そのまま本文に入る流れ。

#### 図の仕様

Phase 5 で決めた「大きめ図解」の作図ルールを踏襲するが、**縦方向のフロー**にする。

| 項目 | 規定 |
|---|---|
| `viewBox` | `0 0 320 560`（縦長） |
| レイアウト | **縦に5ノードを並べ、矢印でつなぐ** |
| `fill` | `none`（ノード内の塗りは `#fff` 可） |
| `stroke` | `currentColor` |
| `stroke-width` | `2` |
| `aria-hidden` | **`false`。このフローは情報なので `role="img"` + `aria-label` を付ける** |
| `width`/`height` | 付けない。CSSで制御 |

**縦方向にする理由**: 横並びの5ステップは375px幅で必ず潰れる。縦なら1つのSVGがすべての画面幅で同じように読め、メディアクエリもスクロールコンテナも不要になる。

#### アクセシビリティ（Phase 2/5 とは扱いが違う）

Phase 2 / 5 のアイコン・図解は**装飾**だったので `aria-hidden="true"` にした。しかしこのフロー図は**記事の情報そのもの**なので隠してはいけない。

```html
<div class="article-flow">
  <svg viewBox="0 0 320 560" fill="none" stroke="currentColor" stroke-width="2"
       stroke-linecap="round" stroke-linejoin="round"
       role="img" aria-label="AI導入の5ステップ。STEP01 解決したい仕事を決める、STEP02 小さく安全に試す、STEP03 入力してよい情報を決める、STEP04 手順をチームの形にする、STEP05 月に一度見直す。この流れを繰り返す。">
    ...
  </svg>
</div>
```

`aria-label` には**5ステップの内容をテキストで入れる**（既存の `.article-toc` の `<ol>` と同じ情報）。

#### 図の内容

- ノード5個。各ノードに **STEP番号のみ**（見出しの日本語は図に入れない。`aria-label` と本文が担う）
- 下向き矢印4本でつなぐ
- STEP 05 から STEP 02 あたりへ戻る**ループの矢印**を入れる（本文が「月に一度見直し、次に試す業務を決める」と繰り返しを明示しているため）

#### CSS

`styles.css` の記事関連スタイル（1804行以降）に追加する。

```css
.article-flow {
  max-width: 320px;
  margin: 32px auto;
  color: #1fa6ba;
}

.article-flow svg {
  width: 100%;
  height: auto;
  display: block;
}
```

### Step 6: キャッシュバスターを更新する

- `assets/ogp.jpg` — 中身を変えたので**必ず**クエリを上げる（`index.html` / `privacy.html`）
- `assets/ogp-guide.jpg` — 新規なのでクエリを付ける（`guide.html`）
- `styles.css` — 変更したので**4ページ分**上げる

---

## 4. やってはいけないこと

- **画像の中身を確認せずに完了とする** — 必ず `Read` で開いて目視し、ユーザーにも送る
- **日本語テキストをSVG＋sharpでラスタライズする** — フォントが解決できず豆腐になる
- **「初回相談無料」をユーザー確認なしにそのまま残す** — サイト本文にない訴求
- **廃止色（`#0e3d38` / `#d7ee87` / `#fff4d6`）をOGPに使う**
- **`og:image:width` / `og:image:height` を変更する** — 1200×630 のまま
- **OGP画像のファイル名 `ogp.jpg` を変える** — 既に外部にキャッシュ・共有されている可能性がある。中身の差し替え＋クエリ更新で対応する
- **フロー図に `aria-hidden="true"` を付ける** — 情報なので隠さない
- **フロー図を横並びにする** — 375px で潰れる
- **`guide.html` の本文コピーを書き換える** — 図の追加だけが担当範囲
- **新しい写真素材を外部から持ってくる** — 既存のヒーロー写真を再利用する（ライセンス上も安全）
- **リポジトリに `package.json` / `node_modules` を作る**
- **`git commit` しない**

---

## 5. 完了条件

- [ ] `assets/ogp.jpg` が新ブランド「デジタル担当室」の内容に差し替わっている
- [ ] `assets/ogp-guide.jpg` が新規作成され、ガイド記事の見出しが入っている
- [ ] 両方とも 1200×630 / 300KB以下
- [ ] 画像に旧ブランド名（リモートお助け隊 / さいたま）と廃止色が**含まれていない**
- [ ] 生成画像を `Read` で目視確認し、ユーザーにも送って承認を得た
- [ ] 「初回相談無料」の扱いをユーザーに確認した
- [ ] `guide.html` の `og:image` / `twitter:image` が `ogp-guide.jpg` を指している
- [ ] 全ページの `og:image:alt` が実際の画像内容と一致している
- [ ] `guide.html` に5ステップのフロー図が入り、`role="img"` + `aria-label` が付いている
- [ ] フロー図が 375px で潰れていない
- [ ] `ogp.jpg` / `styles.css` のキャッシュバスターを更新した
- [ ] `PROGRESS.md` に記録を追記した

---

## 6. 検証手順

### 6-1. 画像の寸法とサイズ

```bash
ls -la assets/
python -c "
import struct
for p in ['assets/ogp.jpg','assets/ogp-guide.jpg']:
    d=open(p,'rb').read(); i=2
    while i<len(d):
        if d[i]!=0xFF: i+=1; continue
        if d[i+1] in (0xC0,0xC1,0xC2):
            h,w=struct.unpack('>HH',d[i+5:i+9]); print(p,f'{w}x{h}',len(d),'bytes'); break
        i+=2+struct.unpack('>H',d[i+2:i+4])[0]
"
```

**期待結果**: 両方 `1200x630`、300,000 bytes 以下

### 6-2. 画像の中身の確認【必須】

`Read` ツールで `assets/ogp.jpg` と `assets/ogp-guide.jpg` を開き、**自分の目で**確認する。

- [ ] 「デジタル担当室」と表示されている
- [ ] 「リモートお助け隊」「SAITAMA」が**ない**
- [ ] 日本語が正しく描画されている（豆腐がない）
- [ ] 黄緑・深緑が使われていない
- [ ] 文字が画像の端で切れていない

### 6-3. OGPタグの整合

```bash
grep -rn "og:image\|twitter:image" --include="*.html" . | grep -v "^./docs"
```

**期待結果**:
- `index.html` / `privacy.html` → `ogp.jpg?v=新しい値`
- `guide.html` → `ogp-guide.jpg?v=新しい値`
- `og:image:width` / `height` が 1200 / 630 のまま

### 6-4. フロー図のアクセシビリティ

```bash
grep -n "article-flow" guide.html styles.css
grep -n "role=\"img\"" guide.html
```

**期待結果**: `article-flow` が両ファイルにあり、`role="img"` が1件

DevTools の Accessibility ツリーで、フロー図が `img` ロールとして認識され、`aria-label` が読めることを確認する。

### 6-5. 目視確認

```bash
python -m http.server 8777
```

- `http://localhost:8777/guide.html` で、目次の直後にフロー図が表示される
- **375px 幅でフロー図が潰れていない**（縦フローなので潰れないはずだが必ず確認）
- 768px / 1280px でも中央に収まっている
- コンソールにエラー・404が出ていない

### 6-6. OGPの実表示確認（本番反映後）

ローカルでは検証できない。**本番反映後**に以下で確認する。

- X (Twitter) Card Validator
- Facebook Sharing Debugger（**キャッシュの再取得を明示的に実行すること**。旧画像がキャッシュされている）

**本番で確認するまで「検証済み」と報告しないこと。** `PROGRESS.md` に「未検証（本番反映後に要確認）」と正直に書く。

---

## 7. PROGRESS.md への追記内容

```markdown
### Phase 6: OGP画像の刷新と guide.html の図解追加

- **状態**: 完了
- **日付**: YYYY-MM-DD
- **キャッシュバスター**: ogp.jpg `?v=...` / ogp-guide.jpg `?v=...` / styles.css `?v=...`
- **変更ファイル**:
  - `assets/ogp.jpg` — 旧ブランド（リモートお助け隊）から現ブランドへ差し替え
  - `index.html` / `privacy.html` — og:image のキャッシュバスター更新、og:image:alt 整合
  - `guide.html` — og:image / twitter:image を ogp-guide.jpg へ、フロー図を追加
  - `styles.css` — .article-flow を追加
- **新規ファイル**:
  - `assets/ogp-guide.jpg` — 1200×630 / XXX,XXX bytes
- **ユーザー確認の結果**:
  - 「初回相談無料」バッジ: 残した / 削除した / 表現を変更した ← **必ず記録**
  - OGP画像の承認: 得た / 修正指示あり
- **検証結果**: 上記6-1〜6-5の結果。6-6（SNS実表示）は**未検証**（本番反映後に要確認）と明記
- **次フェーズへの申し送り**:
  - `404.html` にはOGPタグ自体がない。Phase 7 で追加するか判断すること
  - 本番反映後、Facebook Sharing Debugger でキャッシュの再取得が必要
- **スコープ外で気づいた点**: （あれば）
```
