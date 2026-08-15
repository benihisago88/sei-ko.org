# ブランド仕様書

**すべてのフェーズのAIは、作図・実装の前にこのファイルを読むこと。**

このサイトは旧ブランド「さいたま 社長のパソコン・AIお助け隊」から「デジタル担当室」へ改称した経緯があり、旧ブランドの色が各所に残っている。**このファイルが色とアイコン形状の唯一の正**とする。

---

## 1. カラー

### 採用色（`styles.css` の `:root` が実体）

| 変数 | 値 | 用途 |
|---|---|---|
| `--ink` | `#11153f` | 主色。濃紺。テキスト、ブランドマーク背景、暗い面 |
| `--pine` | `#11153f` | `--ink` と同値（歴史的経緯。新規では `--ink` を使う） |
| `--lime` | `#35bfd2` | アクセント。シアン。**名前は "lime" だが実際はシアン**（旧ブランドの名残） |
| `--mint` | `#e7f8fa` | 淡いシアン背景 |
| `--cream` | `#fff` | ページ背景 |
| `--sand` | `#f4f8f9` | 薄いグレー背景 |
| `--line` | `#dce6e8` | 罫線・ボーダー |
| `--muted` | `#536173` | 補助テキスト |

### 変数化されていないが現役の色

既存コードにハードコードされている。**新規作図ではこの範囲のシアンに揃える。**

| 値 | 使用箇所 |
|---|---|
| `#33b8cf` | `.button.primary` の背景（`styles.css:319`） |
| `#1faec3` | ヘッダーの `.brand-dot`（「デジタル」の文字色） |
| `#1fa6ba` | `.service-icon` の文字色 |
| `#20a1b3` | `.card-number` |
| `#9edbe2` | `.service-icon` のボーダー |
| `#cfeef1` | `.case-visual` の背景 |
| `#55bfc9` | `.blueprint` の罫線 |
| `#18889b` / `#107a91` | ケースビジュアルのテキスト |

### 廃止色（旧ブランド）— 見つけたら置換する

| 廃止色 | 説明 | 置換先 |
|---|---|---|
| `#0e3d38` | 深緑。旧ブランドの主色 | `#11153f`（`--ink`） |
| `#d7ee87` | 黄緑。旧ブランドのアクセント | `#35bfd2`（`--lime`） |
| `#fff4d6` | クリーム。旧ブランドの装飾色 | 用途に応じて `#fff` または `--mint` |

現在の残存箇所（Phase 0 で全消しする）:

```
404.html:7          theme-color #0e3d38
guide.html:22       theme-color #0e3d38
index.html:24       theme-color #0e3d38
privacy.html:20     theme-color #0e3d38
site.webmanifest:10 theme_color  #0e3d38
favicon.svg:2       rect fill    #0e3d38
favicon.svg:3,4     stroke       #d7ee87
favicon.svg:5       path fill    #fff4d6
styles.css:315      box-shadow   #0e3d3824
styles.css:1304     フッター .brand-dot #d7ee87
```

---

## 2. ブランドマーク

**円形 + 「担」の1文字。** これが実装上の正であり、ファビコンもこれに合わせる。

現在の実装（`styles.css:134`）:

```css
.brand-mark {
  width: 30px;
  height: 30px;
  display: grid;
  place-items: center;
  color: #fff;
  background: var(--ink);   /* 濃紺 */
  font-family: var(--display);
  font-size: 14px;
  font-weight: 800;
  border-radius: 50%;       /* 真円 */
}
```

HTML側（4ページのヘッダー・フッター共通）:

```html
<span class="brand-mark">担</span>
```

### ファビコン作図の指針（Phase 1）

- **円形 + 「担」** をベースにする。現行 `favicon.svg` のモニター＋星の図柄は破棄する
- 背景 `#11153f`（濃紺）、文字 `#fff`（白）
- アクセントに `#35bfd2`（シアン）を使ってよいが、16pxでの視認性を最優先する
- 「担」の字はSVGの `<text>` ではなく **`<path>` でアウトライン化する**。`<text>` は環境のフォントに依存し、Windows / macOS / Android で字形が変わるうえ、ラスタライズ時にフォントが解決されない可能性がある
- 16px 表示で潰れないこと。細部を削り、線を太く保つ

---

## 3. アイコン作図ルール

Phase 2 / 5 / 6 / 7 で作るインラインSVGアイコンの共通仕様。**必ずこれに従うこと。バラバラの見た目になるのを防ぐための規約。**

### 基本形

```html
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
  <!-- パス -->
</svg>
```

### ルール

| 項目 | 規定 |
|---|---|
| `viewBox` | `0 0 24 24` に統一 |
| 描画方式 | **線画（stroke）**。塗り（fill）ベースのアイコンと混ぜない |
| `fill` | `none` |
| `stroke` | `currentColor`（親要素の `color` を継承させ、CSSで色を制御する） |
| `stroke-width` | `1.75` |
| `stroke-linecap` / `stroke-linejoin` | `round` |
| `width` / `height` 属性 | **付けない**。CSSでサイズを制御する |
| アクセシビリティ | `aria-hidden="true"`。装飾目的なので `<title>` は付けない。意味は隣接する見出しテキストが担う |
| 座標 | 整数または `.5` 刻み。半端な小数を避ける |
| 余白 | 24×24 の中で 2px のパディングを取り、実描画は 2〜22 の範囲に収める |

### なぜ `currentColor` なのか

`.service-icon` などの既存CSSは `color` プロパティで色を指定している。`currentColor` にしておけば、ホバー・ダークカード・ミントカードなどの文脈で親のCSSだけで色が変わり、SVG側を触らずに済む。

### なぜ線画に統一するのか

現在のカード群は細い罫線（`--line`）と 1px ボーダーの円（`.service-icon`）で構成された軽いトーン。塗り潰しアイコンを入れると視覚的な重さが浮く。

---

## 4. タイポグラフィ

```css
--display: -apple-system, BlinkMacSystemFont, "Hiragino Kaku Gothic ProN",
           "Yu Gothic", Meiryo, sans-serif;
--mono:    ui-monospace, SFMono-Regular, Consolas, monospace;
```

- **Webフォントは使わない。** 現状 `styles.css` に `url()` は1つも存在せず、CSPも `font-src 'self'` で外部フォントを禁じている
- ラベル類（`.eyebrow` / `.card-number` / `.plan-label` / `.visual-tag`）は `--mono` + 大文字 + `letter-spacing` が共通パターン
- 見出しは `letter-spacing: -0.055em 〜 -0.06em` の詰め組みが共通

---

## 5. 命名規約

### アセットファイル

| 種別 | パターン | 例 |
|---|---|---|
| ファビコン系 | ルート直下 | `favicon.svg` / `favicon.ico` / `favicon-32.png` / `favicon-48.png` |
| アプリアイコン | ルート直下 | `apple-touch-icon.png` / `icon-192.png` / `icon-512.png` |
| maskable | ルート直下 | `icon-maskable-192.png` / `icon-maskable-512.png` |
| OGP | `assets/` | `ogp.jpg`（トップ）/ `ogp-guide.jpg`（ガイド） |
| 写真 | `assets/` | `<用途>-<バリアント>-<幅>.webp` 例: `hero-consultation-pc-1303.webp` |

### CSSクラス

- ケバブケース（`service-card` / `case-visual` / `price-card`）
- 修飾は単語追加（`dark-card` / `mint-card` / `sand-card` / `featured`）
- アニメーション遅延は `delay-1` 〜 `delay-3`

---

## 6. 迷ったときの判断基準

1. **既存の実装が正** — このドキュメントと実コードが食い違ったら、まずユーザーに報告する。勝手にどちらかへ寄せない
2. **16px で読めるか** — ファビコンとアイコンは最小サイズでの視認性を最優先する
3. **差分を小さく** — 「ついでに直す」をしない。スコープ外の改善案は `PROGRESS.md` の申し送りに書く
