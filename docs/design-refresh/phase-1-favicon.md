# Phase 1: ファビコン／アプリアイコン一式の刷新

## 0. 前提（必ず最初に読む）

1. `docs/design-refresh/00-common-constraints.md` — 全フェーズ共通の制約
2. `docs/design-refresh/00-brand-tokens.md` — ブランド仕様（特に「2. ブランドマーク」）
3. `docs/design-refresh/PROGRESS.md` — **Phase 0 の記録を必ず読む**（設定済みキャッシュバスターの値が必要）

対象リポジトリ: `C:\Users\murph\Desktop\sei-ko.org`

**Phase 0 が未完了ならこのフェーズを始めないこと。** Phase 0 で色を確定していないと、ここで作るアイコンが全部作り直しになる。

---

## 1. ゴール

現在のファビコンには3つの問題がある。

1. **図柄がブランドと不一致** — `favicon.svg` はモニター＋星の図柄。しかしサイトのブランドマークは「円形＋担の1文字」（`styles.css:134` の `.brand-mark`、4ページのヘッダー・フッターで使用）。ファビコンとロゴが別物になっている
2. **`aria-label` が旧ブランド名** — 「さいたま 社長のパソコン・AIお助け隊」のまま残っている
3. **必要なファイル形式が欠けている** — `favicon.ico` なし（`/favicon.ico` への暗黙リクエストが404 → 404.htmlが返る）、48px以上のラスターなし（Google検索結果のファビコンは48pxの倍数が推奨）、maskableアイコンなし（Androidのアダプティブアイコンで四隅が切り落とされる）

**このフェーズが完了すると:**

- ファビコンがヘッダーロゴと同じ「円形＋担」になる
- `favicon.ico` が存在し、16/32/48px を内包する
- Android のホーム画面追加でアイコンの角が欠けない
- 4ページの `<head>` のアイコン宣言が統一され、宣言と実ファイルが一致する

---

## 2. 対象ファイルと現状

### 2-1. 現在のアイコンファイル

| ファイル | サイズ | 状態 |
|---|---|---|
| `favicon.svg` | viewBox 0 0 64 64 | 図柄が旧ブランド。Phase 0 で色だけ濃紺＋シアンに置換済み |
| `favicon-32.png` | 32×32 | 旧図柄 |
| `apple-touch-icon.png` | 180×180 | 旧図柄 |
| `icon-192.png` | 192×192 | 旧図柄 |
| `icon-512.png` | 512×512 | 旧図柄 |
| `favicon.ico` | — | **存在しない** |
| 48px ラスター | — | **存在しない** |
| maskable | — | **存在しない** |

### 2-2. 現在の `<head>` 宣言（4ページ共通）

```html
<link rel="icon" href="/favicon.svg?v=..." type="image/svg+xml" />
<link rel="icon" href="/favicon-32.png?v=..." sizes="32x32" type="image/png" />
<link rel="apple-touch-icon" href="/apple-touch-icon.png?v=..." />
<link rel="manifest" href="/site.webmanifest?v=..." />
```

### 2-3. 現在の `site.webmanifest`

```json
"icons": [
  { "src": "/icon-192.png?v=20260801", "sizes": "192x192", "type": "image/png" },
  { "src": "/icon-512.png?v=20260801", "sizes": "512x512", "type": "image/png" }
]
```

`"purpose"` の指定がないため、すべて `any` 扱い。maskable がない。

---

## 3. 作業手順

### Step 1: 図柄の設計と、16px での可読性の確認【ユーザー判断あり】

**ここは必ずユーザーに確認を取ること。AIだけで決めない。**

理由: 「担」は12画の漢字で、**16px（ブラウザのタブに表示される実サイズ）ではほぼ確実に潰れて読めない**。これは技術的に解決できない制約で、デザイン上のトレードオフをユーザーが選ぶ必要がある。

以下の2案を実際に作り、**16px / 32px / 48px にラスタライズした画像を並べてユーザーに提示し、選んでもらう**。

#### 案A: 「担」をそのまま使う

- 円形（または角丸正方形）の濃紺 `#11153f` 地に、白の「担」
- **利点**: ヘッダーロゴと完全に一致する
- **欠点**: 16pxでは黒い塊にしか見えない可能性が高い

#### 案B: 「担」を簡略化した幾何学マーク

- 「担」の偏（てへん）と旁の構造を、2〜3本の線＋矩形に還元した記号
- アクセントに `#35bfd2` を1色だけ使う
- **利点**: 16pxでも形が判別でき、タブの中で見分けがつく
- **欠点**: ヘッダーの「担」とは別物になる（ただし色とトーンは共通）

**提示方法**: 両案を 16/32/48px でPNG化し、`SendUserFile` で送るか、比較用HTMLを作って `python -m http.server` で見せる。**実寸の16pxで見せること**（拡大表示だけで判断させない）。

### Step 2: 「担」のアウトライン化（案Aを選んだ場合、または案Bで漢字要素を使う場合）

**SVG内で `<text>` を使わないこと。** 理由:

- 環境のフォントに依存し、Windows / macOS / Android で字形が変わる
- ラスタライズツール（sharp / librsvg）が日本語フォントを解決できず、豆腐（□）になる可能性が高い

**アウトライン化の手順**（スクラッチパッドで実施）:

1. **ライセンスが明確なフォントを使う。Windows同梱の游ゴシック・メイリオを使わないこと。** それらのEULAはグリフのアウトライン抽出・再配布を想定していない。**SIL Open Font License の Noto Sans JP** を使う（OFLは派生物の作成・埋め込みを明示的に許可している）

   ```bash
   npm i @fontsource/noto-sans-jp fontkit
   ```

2. `fontkit` でグリフのパスを取り出す

   ```js
   const fontkit = require('fontkit');
   const font = fontkit.openSync('<Noto Sans JP Bold の .ttf パス>');
   const glyph = font.layout('担').glyphs[0];
   const d = glyph.path.toSVG();          // フォント座標系（Y軸が上向き）
   const scale = 1 / font.unitsPerEm;     // 正規化してから viewBox に合わせる
   ```

3. Y軸を反転し、viewBox の中央に配置する `transform` を計算して SVG に埋める
4. **得られた `d` 属性を `favicon.svg` に直接書き込む。フォントファイルはリポジトリにコピーしない**

### Step 3: `favicon.svg` の作り直し

- viewBox は `0 0 64 64` のまま（既存と揃える）
- 背景: `#11153f`、角丸 `rx="16"`（現行と同じ）または真円。ヘッダーの `.brand-mark` は `border-radius: 50%` の**真円**なので、真円に寄せることを推奨
- 文字／マーク: `#fff`。アクセントに `#35bfd2` を使ってよい
- **`aria-label` を現ブランドに更新する**: `role="img" aria-label="デジタル担当室"`
- `<text>` を使わない（Step 2 でアウトライン化済みのパスを使う）

### Step 4: ラスター画像の生成

スクラッチパッドで実施。手順は `00-common-constraints.md`「6. ラスター画像の生成方法」に従う。

```bash
npm i sharp png-to-ico
```

生成するもの:

| 出力 | サイズ | 元 | 備考 |
|---|---|---|---|
| `favicon.ico` | 16 + 32 + 48 内包 | `favicon.svg` | `png-to-ico` に3サイズのPNGを渡す |
| `apple-touch-icon.png` | 180×180 | `favicon.svg` | **透過なし**。背景を `#11153f` で塗りつぶす（iOSは透過を黒く塗る） |
| `icon-192.png` | 192×192 | `favicon.svg` | `purpose: any` 用 |
| `icon-512.png` | 512×512 | `favicon.svg` | `purpose: any` 用 |
| `icon-maskable-192.png` | 192×192 | 別途作成 | ↓セーフゾーン仕様 |
| `icon-maskable-512.png` | 512×512 | 別途作成 | ↓セーフゾーン仕様 |

#### maskable のセーフゾーン仕様（重要）

maskable アイコンは、OSが円形・角丸四角・しずく形など**任意の形で切り抜く**。そのため通常アイコンとは別に作る必要がある。

- **背景は角丸にせず、キャンバス全面を `#11153f` で塗りつぶす**（フルブリード）
- **中央のマークは、キャンバス幅の 60% 以内**に収める（直径80%の内接円がセーフゾーン。マークは余裕を見て60%）
- つまり 512px なら、マークは中央 307px 四方に収める

#### 元SVGのサイズについて

`favicon.svg` は 16px 想定で細部を削った図柄。**512pxに引き伸ばすとスカスカに見える場合がある。** その場合は 512px 用に線を細めた別バージョンのSVGをスクラッチパッド内に作って使う（リポジトリには置かない）。

### Step 5: `favicon-32.png` の廃止

新しい `favicon.ico` が 16/32/48 を内包するため、`favicon-32.png` は不要になる。

1. 4ページの `<link rel="icon" ... favicon-32.png ...>` 行を削除する
2. `grep -rn "favicon-32" .` で参照が0件になったことを確認する
3. **確認後に**ファイルを削除する

### Step 6: 4ページの `<head>` を更新

`index.html` / `guide.html` / `privacy.html` / `404.html` の4ページすべてを、以下の形に揃える。

```html
<link rel="icon" href="/favicon.ico?v=V" sizes="16x16 32x32 48x48" />
<link rel="icon" href="/favicon.svg?v=V" type="image/svg+xml" />
<link rel="apple-touch-icon" href="/apple-touch-icon.png?v=V" />
<link rel="manifest" href="/site.webmanifest?v=V" />
```

- `V` は今日の日付ベースの新しい値（例 `20260810.1`）。**4ページとインデント以外すべて同一にすること**
- 順序も揃える（`.ico` → `.svg` → `apple-touch-icon` → `manifest`）
- 各ページの既存インデント（`index.html` は2スペース、他3ページは4スペース）は維持する

### Step 7: `site.webmanifest` の更新

```json
"icons": [
  { "src": "/icon-192.png?v=V", "sizes": "192x192", "type": "image/png", "purpose": "any" },
  { "src": "/icon-512.png?v=V", "sizes": "512x512", "type": "image/png", "purpose": "any" },
  { "src": "/icon-maskable-192.png?v=V", "sizes": "192x192", "type": "image/png", "purpose": "maskable" },
  { "src": "/icon-maskable-512.png?v=V", "sizes": "512x512", "type": "image/png", "purpose": "maskable" }
]
```

- `V` は Step 6 と同じ値
- `theme_color` は Phase 0 で `#11153f` になっているはず。**変更しない**
- `"name"` / `"short_name"` / `"description"` は触らない

---

## 4. やってはいけないこと

- **SVG内で `<text>` を使う** — フォント依存で豆腐化する
- **Windows同梱フォント（游ゴシック / メイリオ / MS ゴシック）のグリフを抽出する** — ライセンス上の問題。OFLフォントを使う
- **フォントファイルをリポジトリにコピーする** — アウトライン化したパスデータだけを持ち込む
- **`apple-touch-icon.png` を透過PNGにする** — iOSが透過部分を黒く塗る
- **maskable を通常アイコンと同じ画像にする** — 角が切り落とされる
- **16px での確認を飛ばす** — 拡大表示だけで「良さそう」と判断しない
- **4ページのうち一部だけ更新する** — 必ず4ページ同時
- **リポジトリに `package.json` / `node_modules` を作る**
- **`git commit` しない**

---

## 5. 完了条件

- [ ] `favicon.svg` が「円形（または角丸）＋担／簡略マーク」になっている
- [ ] `favicon.svg` の `aria-label` が「デジタル担当室」など現ブランド名になっている
- [ ] `favicon.svg` に `<text>` 要素が含まれていない
- [ ] `favicon.ico` が存在し、16/32/48px を内包している
- [ ] `apple-touch-icon.png`（180）/ `icon-192.png` / `icon-512.png` が新図柄で再生成されている
- [ ] `icon-maskable-192.png` / `icon-maskable-512.png` が存在し、フルブリード背景＋60%以内のマークになっている
- [ ] `favicon-32.png` がファイルごと削除され、参照も0件
- [ ] 4ページの `<head>` のアイコン宣言4行が完全一致している
- [ ] `site.webmanifest` に `purpose: any` ×2 と `purpose: maskable` ×2 が入っている
- [ ] 16px 表示をユーザーに確認してもらい、承認を得た
- [ ] リポジトリに `package.json` / `node_modules` が増えていない
- [ ] `PROGRESS.md` に記録を追記した

---

## 6. 検証手順

### 6-1. 生成物のサイズ確認

```bash
node -e "const s=require('sharp');['favicon-48.png','apple-touch-icon.png','icon-192.png','icon-512.png','icon-maskable-192.png','icon-maskable-512.png'].forEach(async f=>{try{const m=await s(f).metadata();console.log(f,m.width+'x'+m.height,m.hasAlpha?'alpha':'no-alpha')}catch(e){console.log(f,'MISSING')}})"
```

※ スクラッチパッドから実行するか、`--prefix` でモジュールパスを解決すること。

**期待結果**: 180×180 / 192×192 / 512×512 が揃い、`apple-touch-icon.png` が `no-alpha`

### 6-2. `.ico` の中身確認

```bash
node -e "const b=require('fs').readFileSync('favicon.ico');const n=b.readUInt16LE(4);console.log('images:',n);for(let i=0;i<n;i++){const o=6+i*16;console.log((b[o]||256)+'x'+(b[o+1]||256))}"
```

**期待結果**: `images: 3` と `16x16` `32x32` `48x48`

### 6-3. 宣言と実ファイルの整合

```bash
# 4ページのアイコン宣言が一致しているか
grep -rn "rel=\"icon\"\|apple-touch-icon\|rel=\"manifest\"" --include="*.html" . | grep -v "^./docs"

# 削除したファイルへの参照が残っていないか
grep -rn "favicon-32" . | grep -v "^./docs" | grep -v "^./.git"
```

**期待結果**: 前者は4ページ×4行＝16行で、ページごとに同じ4行。後者は**0件**

### 6-4. manifest の検証

```bash
node -e "const m=require('./site.webmanifest');console.log(JSON.stringify(m.icons,null,2));console.log('theme:',m.theme_color)"
```

**期待結果**: 4エントリ（any×2, maskable×2）、`theme: #11153f`

### 6-5. 実ブラウザでの確認

```bash
python -m http.server 8777
```

- タブのファビコンが新図柄になっている（**キャッシュが強いので必ずハードリロード / シークレットウィンドウで確認**）
- `http://localhost:8777/favicon.ico` が 200 で画像を返す
- コンソールに 404 や CSP 違反が出ていない
- 4ページすべてで同じファビコンが出る

### 6-6. maskable の確認

- Chrome DevTools → Application → Manifest を開く
- Icons セクションに4エントリが出て、エラーが出ていないこと
- 「Show only the minimum safe area for maskable icons」にチェックを入れ、マークが切れないこと

---

## 7. PROGRESS.md への追記内容

```markdown
### Phase 1: ファビコン／アプリアイコン一式の刷新

- **状態**: 完了
- **日付**: YYYY-MM-DD
- **キャッシュバスター**: アイコン系 `?v=...`（4ページ + manifest で統一）
- **採用した図柄**: 案A（担そのまま）/ 案B（簡略マーク） ← どちらを選んだか、ユーザーの判断理由も記録
- **変更ファイル**:
  - `favicon.svg` — 図柄を作り直し、aria-label を現ブランド名に
  - `apple-touch-icon.png` / `icon-192.png` / `icon-512.png` — 新図柄で再生成
  - `site.webmanifest` — icons を4エントリに（any×2 / maskable×2）
  - `index.html` / `guide.html` / `privacy.html` / `404.html` — head のアイコン宣言を統一
- **新規ファイル**:
  - `favicon.ico` — 16/32/48 内包
  - `icon-maskable-192.png` / `icon-maskable-512.png`
- **削除ファイル**:
  - `favicon-32.png` — favicon.ico に統合
- **検証結果**: 上記6-1〜6-6の結果を記載。通らなかった項目は正直に書く
- **次フェーズへの申し送り**:
  - アウトライン化に使ったフォントとライセンス
  - 512px用に別バージョンのSVGを使った場合はその旨
  - このフェーズで設定したキャッシュバスターの値
- **スコープ外で気づいた点**: （あれば）
```
