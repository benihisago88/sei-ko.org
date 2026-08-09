# Phase 3: ヒーロー画像の軽量化

## 0. 前提（必ず最初に読む）

1. `docs/design-refresh/00-common-constraints.md` — 全フェーズ共通の制約（特に「6. ラスター画像の生成方法」）
2. `docs/design-refresh/PROGRESS.md` — これまでの記録

対象リポジトリ: `C:\Users\murph\Desktop\sei-ko.org`

**このフェーズは他のフェーズと独立している。** Phase 0 の完了を待たずに実行してよい（色に触らないため）。時間が取れないときの埋め合わせに回してもよい。

---

## 1. ゴール

ヒーローの `<picture>` のフォールバック画像 `assets/hero-consultation.png` が **1,740,979 bytes（1.74MB）** ある。同じ絵のwebp版が58〜92KBなので、**約20〜30倍**。

このファイルを読むのは webp 非対応のブラウザ（IE11、Safari 13以前など）と、`<picture>` を解釈しない一部のクローラ・SNSのプレビュー取得ボット。数は少ないが、当たった環境では読み込みが極端に遅くなる。

**このフェーズが完了すると:**

- フォールバック画像が 250KB 以下の JPEG になる（**85%以上の削減**）
- 見た目は一切変わらない（webp対応ブラウザは元々webpを読んでいるため）

**規模が小さく、副作用がほぼないフェーズ。**

---

## 2. 対象ファイルと現状

### 2-1. 現在のアセット

| ファイル | 実サイズ | 寸法 | 比率 | 用途 |
|---|---|---|---|---|
| `assets/hero-consultation-sp-1055.webp` | 88,930 B | 1055×941 | 1.121 | 〜480px |
| `assets/hero-consultation-1280.webp` | 58,656 B | 1280×720 | 1.778 | 481〜800px |
| `assets/hero-consultation-pc-1303.webp` | 92,472 B | 1303×941 | 1.385 | 801px〜 |
| `assets/hero-consultation.png` | **1,740,979 B** | 1672×941 | 1.777 | **フォールバック** |

### 2-2. `index.html:95-103` の現状

```html
<div class="hero-art hero-photo">
  <picture>
    <source media="(max-width: 480px)" srcset="assets/hero-consultation-sp-1055.webp" type="image/webp" />
    <source media="(max-width: 800px)" srcset="assets/hero-consultation-1280.webp" type="image/webp" />
    <source srcset="assets/hero-consultation-pc-1303.webp" type="image/webp" />
    <img src="assets/hero-consultation.png" alt="ノートパソコンを囲んでWEBやECの相談をする事業主とサポート担当者" width="1672" height="941"
      fetchpriority="high" decoding="async" />
  </picture>
</div>
```

### 2-3. アスペクト比が3種類バラバラな件について（これは正常）

上の表のとおり、3つの webp は比率が違う（1.121 / 1.778 / 1.385）。**これはバグではなく、ブレークポイントごとに構図を変える意図的なアートディレクション。**

レイアウトが崩れない理由は CSS 側にある。

```css
.hero-art.hero-photo {
  height: 462px;        /* コンテナ側で高さを固定 */
  overflow: hidden;
}
.hero-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;    /* はみ出しはトリミング */
  display: block;
}
```

コンテナが高さを持ち、`img` が `100% / 100% / object-fit: cover` なので、**`<img>` の `width` / `height` 属性はレイアウトを決めていない**。比率が変わってもレイアウトシフトは起きない。

→ **CSSには一切触らないこと。** ここを「直そう」としないこと。

---

## 3. 作業手順

### Step 1: 元PNGがgitにコミット済みであることを確認する

削除する前に、元ファイルが履歴から復元できることを確認する。

```bash
git log --oneline -- assets/hero-consultation.png
```

**コミットが1件以上表示されること。** 表示されない（未追跡）の場合は、削除せずユーザーに報告する。

### Step 2: スクラッチパッドで JPEG に変換する

`00-common-constraints.md`「6. ラスター画像の生成方法」に従い、スクラッチパッドで作業する。

```bash
npm i sharp
```

```js
const sharp = require('sharp');
sharp('<リポジトリ>/assets/hero-consultation.png')
  .jpeg({ quality: 80, mozjpeg: true, chromaSubsampling: '4:2:0' })
  .toFile('<スクラッチパッド>/hero-consultation.jpg')
  .then(info => console.log(info));
```

**設定の意図:**

- **寸法は 1672×941 のまま**（リサイズしない）。構図が変わると、webp非対応環境だけ見え方が違うことになる
- `quality: 80` — 写真素材で視覚的な劣化がほぼ出ない実用的な下限
- `mozjpeg: true` — 同品質でファイルサイズが1〜2割小さくなる
- **目標: 250KB 以下**。超える場合は quality を 75 まで下げてよい。**70を下回らないこと**（圧縮ノイズが出る）

### Step 3: リポジトリへ配置する

生成した `hero-consultation.jpg` を `assets/` にコピーする。

### Step 4: `index.html` の `<img>` を更新する

```html
<img src="assets/hero-consultation.jpg" alt="ノートパソコンを囲んでWEBやECの相談をする事業主とサポート担当者" width="1672" height="941"
  fetchpriority="high" decoding="async" />
```

変更するのは **`src` の拡張子だけ**。

- `alt` を書き換えない（現状の記述は適切）
- `width` / `height` は寸法を変えていないので `1672` / `941` のまま
- `fetchpriority` / `decoding` はそのまま
- `<source>` 3行は**一切触らない**

### Step 5: 元PNGを削除する

参照が残っていないことを確認してから削除する。

```bash
grep -rn "hero-consultation.png" . | grep -v "^./.git" | grep -v "^./docs"
```

**0件を確認してから** `assets/hero-consultation.png` を削除する。

### Step 6: キャッシュバスターについて

ファイル名が `.png` → `.jpg` に変わるため、**キャッシュバスターは不要**（別URLになる）。

`index.html` のこの画像は元々クエリを付けていないので、付け足さないこと。

---

## 4. やってはいけないこと

- **CSSを触る** — `.hero-art.hero-photo` / `.hero-photo img` は現状で正しい
- **`<source>` の3行を変更する** — webp版は既に最適化済み
- **画像をリサイズ・トリミングする** — 構図を変えない
- **`alt` を書き換える** — 現状の記述は適切
- **webp版を再生成する** — スコープ外。既に十分小さい
- **quality を 70 未満にする** — 圧縮ノイズが出る
- **gitにコミット済みか確認せずにPNGを削除する**
- **リポジトリに `package.json` / `node_modules` を作る**
- **`git commit` しない**

---

## 5. 完了条件

- [ ] `assets/hero-consultation.jpg` が存在し、**250KB以下**
- [ ] JPEGの寸法が **1672×941**（元と同じ）
- [ ] `index.html` の `<img src>` が `.jpg` を指している
- [ ] `<source>` 3行と `alt` / `width` / `height` が変更されていない
- [ ] `assets/hero-consultation.png` が削除され、参照も0件
- [ ] `styles.css` を変更していない
- [ ] `PROGRESS.md` に記録を追記した

---

## 6. 検証手順

### 6-1. サイズと寸法の確認

```bash
ls -la assets/
```

**期待結果**: `hero-consultation.jpg` が 250,000 bytes 以下。`hero-consultation.png` が存在しない

```bash
python -c "
import struct
d=open('assets/hero-consultation.jpg','rb').read()
i=2
while i<len(d):
    if d[i]!=0xFF: i+=1; continue
    m=d[i+1]
    if m in (0xC0,0xC1,0xC2):
        h,w=struct.unpack('>HH',d[i+5:i+9]); print(f'{w}x{h}'); break
    i+=2+struct.unpack('>H',d[i+2:i+4])[0]
"
```

**期待結果**: `1672x941`

### 6-2. 削減率の計算

```
削減率 = (1740979 - 新サイズ) / 1740979 × 100
```

**期待結果: 85%以上**。`PROGRESS.md` に実測値を書く

### 6-3. 参照が残っていないことの確認

```bash
grep -rn "hero-consultation.png" . | grep -v "^./.git" | grep -v "^./docs"
```

**期待結果: 0件**

### 6-4. 目視確認

```bash
python -m http.server 8777
```

- `http://localhost:8777/` でヒーロー画像が表示される
- DevTools の Network タブで、**読み込まれているのが `.webp` であること**（通常のブラウザはwebpを選ぶ。`.jpg` が読まれていたら `<source>` の記述が壊れている）
- コンソールに404が出ていない
- 375px / 768px / 1280px の3幅で、ヒーロー画像の構図が切り替わる

### 6-5. フォールバック経路の確認

webp非対応環境をシミュレートして `.jpg` が読まれることを確認する。DevTools のコンソールで：

```js
document.querySelectorAll('.hero-photo source').forEach(s => s.remove());
location.reload();
```

…では確認できない（リロードで戻る）。代わりに **`<img>` の `currentSrc` を直接確認する**：

```js
document.querySelector('.hero-photo img').currentSrc
```

**期待結果**: 通常は `.webp` のURLが返る。これが `.jpg` を返す場合は `<source>` が効いていないので調査する。

JPEG自体の表示確認は、`http://localhost:8777/assets/hero-consultation.jpg` を直接開いて画像が正常に見えることで代替する。

---

## 7. PROGRESS.md への追記内容

```markdown
### Phase 3: ヒーロー画像の軽量化

- **状態**: 完了
- **日付**: YYYY-MM-DD
- **キャッシュバスター**: なし（拡張子変更でURLが変わるため不要）
- **変更ファイル**:
  - `index.html` — hero の <img src> を .png から .jpg へ
- **新規ファイル**:
  - `assets/hero-consultation.jpg` — 1672×941 / quality XX / XXX,XXX bytes
- **削除ファイル**:
  - `assets/hero-consultation.png` — 1,740,979 bytes（gitコミット済みを確認のうえ削除）
- **削減実績**: 1,740,979 B → XXX,XXX B（XX.X% 削減）
- **検証結果**: 上記6-1〜6-5の結果
- **次フェーズへの申し送り**:
  - webp 3種のアスペクト比が異なるのは意図的なアートディレクション。CSSの object-fit: cover が吸収しているので触らないこと
- **スコープ外で気づいた点**: （あれば）
```
