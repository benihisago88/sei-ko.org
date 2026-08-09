# Phase 2: サービスカードアイコンのSVG化

## 0. 前提（必ず最初に読む）

1. `docs/design-refresh/00-common-constraints.md` — 全フェーズ共通の制約
2. `docs/design-refresh/00-brand-tokens.md` — **特に「3. アイコン作図ルール」を厳守すること**
3. `docs/design-refresh/PROGRESS.md` — Phase 0 / 1 の記録

対象リポジトリ: `C:\Users\murph\Desktop\sei-ko.org`

**Phase 0 完了が前提**（色が確定していること）。Phase 1 の完了は必須ではない。

---

## 1. ゴール

トップページの「できること」セクション（`#service`）の4枚のカードは、アイコンとして**記号文字**を使っている。

```html
<div class="service-icon">⌘</div>   <!-- WEB -->
<div class="service-icon">▤</div>   <!-- EC -->
<div class="service-icon">✦</div>   <!-- AI -->
<div class="service-icon">↗</div>   <!-- SUPPORT -->
```

問題:

- **フォント依存で字形が変わる。** `⌘`（U+2318, Place of Interest Sign）や `▤`（U+25A4）は、日本語フォントによっては全角幅で描画されたり、絵文字フォントに拾われて色付きになったり、環境によっては豆腐（□）になる
- **意味が伝わらない。** `⌘` は Mac のコマンドキーであってWeb制作の記号ではない。`▤` は網掛け矩形であってECとは無関係
- 4つの記号の間で線の太さ・大きさがバラバラで、円の中での重心も揃わない

**このフェーズが完了すると:**

- 4つのアイコンが、統一された線画SVG（`viewBox 0 0 24 24` / `stroke-width 1.75`）になる
- 環境によらず同じ字形で表示される
- 各アイコンがサービス内容を示す図像になる

---

## 2. 対象ファイルと現状

### 2-1. `index.html` の該当箇所（138〜179行付近）

```html
<div class="service-grid">
  <article class="service-card dark-card reveal">
    <p class="card-number">01 / WEB</p>
    <div class="service-icon">⌘</div>
    <h3>WEB制作・改善</h3>
    ...
  </article>
  <article class="service-card mint-card reveal delay-1">
    <p class="card-number">02 / EC</p>
    <div class="service-icon">▤</div>
    <h3>Shopify・EC</h3>
    ...
  </article>
  <article class="service-card sand-card reveal delay-2">
    <p class="card-number">03 / AI</p>
    <div class="service-icon">✦</div>
    <h3>AI・業務効率化</h3>
    ...
  </article>
  <article class="service-card reveal delay-3">
    <p class="card-number">04 / SUPPORT</p>
    <div class="service-icon">↗</div>
    <h3>相談・デジタル支援</h3>
    ...
  </article>
</div>
```

### 2-2. `styles.css:545` の `.service-icon`

```css
.service-icon {
  margin-top: 27px;
  width: 44px;
  height: 44px;
  display: grid;
  place-items: center;
  border: 1px solid #9edbe2;   /* 淡いシアンの円 */
  border-radius: 50%;
  font-size: 20px;             /* ← 記号文字用。SVG化後は不要になる */
  color: #1fa6ba;              /* ← currentColor の供給元になる */
}
```

### 2-3. 背景色について（重要）

`styles.css:509-510` で **全カードが白背景に固定**されている。

```css
.service-card {
  background: #fff !important;
  color: var(--ink) !important;
}
```

したがって**4枚とも同じ背景**であり、アイコンの色を出し分ける必要はない。`.service-icon` の `color: #1fa6ba` を `currentColor` で拾えば4枚とも同じシアンになる。これが正しい挙動。

### 2-4. 発見済みの問題（対応はユーザーに確認すること）

HTMLの `dark-card` / `mint-card` / `sand-card` の3クラスは、**`styles.css` に定義が存在しない**（`grep -n "dark-card\|mint-card\|sand-card" styles.css` が0件）。以前のデザインの名残で、現在は何の効果もない。

カード間の見た目の差は、実際には `styles.css:526-536` の `:nth-child()` によるアクセントライン色で付いている。

**このフェーズでこれらのクラスを消すかどうかは、ユーザーに確認してから決める。** 消すこと自体は安全だが、指示にない変更なので勝手にやらない。確認しない場合は**そのまま残し**、`PROGRESS.md` の申し送りに書く。

---

## 3. 作業手順

### Step 1: 4つのアイコンを設計する

`00-brand-tokens.md`「3. アイコン作図ルール」の基本形に従う。

```html
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  <!-- パス -->
</svg>
```

| カード | 現在 | 図像の方向性 | 避けるべき表現 |
|---|---|---|---|
| 01 / WEB制作・改善 | `⌘` | ブラウザウィンドウ（上部にバー付きの矩形）。「改善」を示すなら中にカーソルかスライダー | コードタグ `</>`（開発者向けに見え、非エンジニア層に響かない） |
| 02 / Shopify・EC | `▤` | ショッピングバッグ、またはカート | Shopifyの公式ロゴ（商標。使わない） |
| 03 / AI・業務効率化 | `✦` | 4点きらめき（スパークル）。AIを示す一般的な記号 | ロボット・脳（古い比喩で、業務効率化の文脈に合わない） |
| 04 / 相談・デジタル支援 | `↗` | 吹き出し、または対話を示す2つの吹き出し | 疑問符単体（「分からない人」を指す印象になる） |

**制約**:

- 実描画は 24×24 の中の **2〜22 の範囲**に収める（2px の余白）
- 座標は整数または `.5` 刻み
- 4つを**並べて見たときに線の密度が揃っている**こと。1つだけ描き込みが多いと浮く
- `width` / `height` 属性は付けない（CSSで制御する）

### Step 2: `index.html` の4箇所を差し替える

```html
<div class="service-icon">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
       stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <!-- パス -->
  </svg>
</div>
```

- **`.service-icon` の `div` 自体は残す**（円のボーダーとレイアウトを担っている）
- カードの他の要素（`card-number` / `h3` / `p` / `ul`）は**一切触らない**
- `index.html` のインデント（2スペース）に合わせる

### Step 3: `styles.css` の `.service-icon` を調整

```css
.service-icon {
  margin-top: 27px;
  width: 44px;
  height: 44px;
  display: grid;
  place-items: center;
  border: 1px solid #9edbe2;
  border-radius: 50%;
  color: #1fa6ba;
}

/* アイコンSVGのサイズはここで制御する（SVG側に width/height を書かない） */
.service-icon svg {
  width: 22px;
  height: 22px;
}
```

- `font-size: 20px` を**削除する**（記号文字用の指定で、SVG化後は無意味）
- `color: #1fa6ba` は**残す**。SVG の `currentColor` がこれを継承する
- 22px は 44px の円に対する目安。実際に見て 20〜24px の範囲で調整してよい

### Step 4: レスポンシブの確認

`styles.css:1510` 付近に `.service-card` のレスポンシブ指定がある。**アイコンサイズが小さい画面で不自然になっていないか確認**し、必要ならメディアクエリ内に `.service-icon svg` の指定を追加する。

---

## 4. やってはいけないこと

- **`00-brand-tokens.md` のアイコン作図ルールから外れる** — `viewBox` / `stroke-width` / `currentColor` は統一が目的。1つだけ違う仕様にしない
- **塗り（fill）ベースのアイコンを混ぜる** — 線画に統一する
- **SVGに `width` / `height` 属性を書く** — CSSで制御する
- **SVGに `stroke="#1fa6ba"` のように色を直書きする** — `currentColor` を使う
- **`<title>` を付ける / `aria-hidden` を外す** — 装飾目的。意味は隣の `<h3>` が担っている。スクリーンリーダーに二重に読ませない
- **外部アイコンライブラリを導入する** — CDN禁止。SVGを直接書く
- **`.service-card` の `!important` を外す / 背景色を変える** — スコープ外
- **カードの本文コピー（`h3` / `p` / `ul`）を書き換える** — 今回は画像の作業であってコピーの作業ではない
- **`dark-card` などのクラスをユーザー確認なしに消す**
- **`git commit` しない**

---

## 5. 完了条件

- [ ] 4枚のカードすべてでインラインSVGに置き換わり、記号文字 `⌘ ▤ ✦ ↗` が `index.html` から消えている
- [ ] 4つのSVGすべてが `viewBox="0 0 24 24"` / `fill="none"` / `stroke="currentColor"` / `stroke-width="1.75"` / `aria-hidden="true"`
- [ ] SVGに `width` / `height` 属性がない
- [ ] `styles.css` の `.service-icon` から `font-size` が消え、`.service-icon svg` のサイズ指定が入っている
- [ ] `color: #1fa6ba` が残っており、アイコンがシアンで表示される
- [ ] 375px / 768px / 1280px で崩れない
- [ ] `styles.css` のキャッシュバスターを更新し、4ページ分同期した
- [ ] `PROGRESS.md` に記録を追記した

---

## 6. 検証手順

### 6-1. 記号文字が残っていないことの確認

```bash
grep -n "⌘\|▤\|✦\|↗" index.html
```

**期待結果: 0件**

### 6-2. SVGの仕様が揃っていることの確認

```bash
grep -c "viewBox=\"0 0 24 24\"" index.html
grep -c "stroke-width=\"1.75\"" index.html
grep -c "aria-hidden=\"true\"" index.html
```

**期待結果**: いずれも4以上（`aria-hidden` は既存の他要素でも使われている可能性があるので4以上）

### 6-3. width/height 属性が付いていないことの確認

```bash
grep -n "<svg[^>]*width=" index.html
```

**期待結果: 0件**

### 6-4. キャッシュバスターの同期確認

```bash
grep -rn "styles.css?v=" --include="*.html" . | grep -v "^./docs"
```

**期待結果**: 4行すべて同じ値

### 6-5. 目視確認

```bash
python -m http.server 8777
```

- 「できること」セクションで4つのアイコンが**すべてシアンの線画**で表示される
- 4つ並べたときに**線の太さと密度が揃って見える**（1つだけ濃い／薄いがない）
- 円のボーダー内で上下左右が中央に来ている
- ブラウザのコンソールにエラーが出ていない
- 375px / 768px / 1280px の3幅で崩れない

### 6-6. アイコンの意味が伝わるかの確認

**ユーザーに実際に見てもらい、「どのアイコンがどのサービスか」が説明なしで分かるか確認する。** 分からないと言われたら図像を作り直す。ここは自己判断で「伝わるはず」と決めない。

---

## 7. PROGRESS.md への追記内容

```markdown
### Phase 2: サービスカードアイコンのSVG化

- **状態**: 完了
- **日付**: YYYY-MM-DD
- **キャッシュバスター**: styles.css `?v=...`
- **変更ファイル**:
  - `index.html` — service-icon 4箇所を記号文字からインラインSVGへ
  - `styles.css` — .service-icon の font-size 削除、.service-icon svg のサイズ指定追加
  - 4ページ — styles.css のキャッシュバスター更新
- **採用した図像**: WEB=... / EC=... / AI=... / SUPPORT=...（何を描いたか一言ずつ）
- **検証結果**: 上記6-1〜6-6の結果。ユーザーの図像確認の結果も記載
- **次フェーズへの申し送り**:
  - Phase 5（ケース図解）は、ここで作ったアイコンと同じ作図ルールで揃えること
  - `dark-card` / `mint-card` / `sand-card` の未定義クラスをどう扱ったか（残した / 消した / 未確認）
- **スコープ外で気づいた点**: （あれば）
```
