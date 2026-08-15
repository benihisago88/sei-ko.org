# 全フェーズ共通の制約

**すべてのフェーズのAIは、作業前にこのファイルを読むこと。**

---

## 1. サイトの性質

- **ビルド工程なしの素の静的サイト**。HTML / CSS / 素のJavaScript のみ（＋ `contact.php` 1本）
- **フレームワーク・バンドラ・トランスパイラを導入しない**。React / Vue / Tailwind / Sass / Vite などを持ち込まないこと
- **外部CDNを参照しない**。Google Fonts、Font Awesome、jsDelivr などへのリンクを追加しない
- 現在Webフォントは1つも読み込んでいない（`styles.css` に `url()` が存在しない）。これは意図的な設計なので維持する

## 2. CSP（Content Security Policy）の制約

`.htaccess` で厳格なCSPが設定されている。

```
default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'self';
object-src 'none'; script-src 'self'; style-src 'self'; img-src 'self' data:;
connect-src 'self'; font-src 'self'
```

これにより：

| やること | 可否 | 補足 |
|---|---|---|
| 外部ドメインの画像・CSS・JS・フォント | **不可** | すべて自ドメイン内に置く |
| インライン `<style>` タグの追加 | **不可** | `style-src 'self'` に `'unsafe-inline'` がない |
| `style="..."` 属性の追加 | **不可** | 同上 |
| インライン `<script>` の追加 | **不可** | `script-src 'self'` に `'unsafe-inline'` がない |
| **インラインSVG**（`<svg>` を直接HTMLに書く） | **可** | `img-src` の対象外。今回のアイコン実装はこれを使う |
| `data:` URI の画像 | 可 | ただし多用しない |
| 既存の `<script type="application/ld+json">` | 現状動作中 | 構造化データ。触らない |

**新しくCSSを足すときは必ず `styles.css` に書く。HTMLに `style` 属性を書かない。**

## 3. キャッシュバスター規約

`.htaccess` で CSS/JS/画像に `max-age=31536000, immutable`（1年）が設定されている。**ファイルを差し替えただけでは反映されない。**

- 規約：`?v=YYYYMMDD.N` （例：`?v=20260809.1`）
- 同じ日に複数回更新するときは `.1` → `.2` と連番を上げる
- **アセットを変更したら、それを参照している全ページのクエリを同時に更新する**

### 参照ページ一覧（重要）

アイコン・manifest・CSS・JS は以下の**4ページすべて**が参照している。1つでも漏らすと不整合になる。

```
index.html
guide.html
privacy.html
404.html
```

`<head>` 内の以下は4ページで**常に一致させること**：

- `<link rel="icon">`（SVG / PNG）
- `<link rel="apple-touch-icon">`
- `<link rel="manifest">`
- `<meta name="theme-color">`
- `<link rel="stylesheet" href="styles.css?v=...">`

> 注: `index.html` はルート相対パスを使わない箇所がある（`href="styles.css"`）が、他3ページは `/styles.css` とルート相対。既存の書き方を変えずにクエリだけ更新すること。

## 4. コードスタイル

- **コメントは日本語**。既存コードがすべて日本語コメントなので合わせる
- `styles.css` は16セクションに区切られ、冒頭に目次コメントがある。**新しいスタイルは該当セクション内に追記し、目次も必要なら更新する**
- スクロール連動アニメーションは `reveal` / `delay-1` / `delay-2` / `delay-3` クラスで実装済み。新セクションでもこのパターンを踏襲する
- 改行制御に `.nobr`（`white-space: nowrap`）と `.pc-only`（PCのみ改行）を使うパターンが全体にある。見出しを追加するときは踏襲する

## 5. 色の扱い

- **色は `styles.css` の `:root` 変数を使う**。詳細は [00-brand-tokens.md](00-brand-tokens.md)
- 新規ハードコード色を増やさない
- 既存コードには変数化されていないハードコード色が多数残っているが、**今回のフェーズで触る箇所以外は変更しない**（差分を大きくしないため）

## 6. ラスター画像の生成方法

現環境には画像処理ツールが入っていない（ImageMagick / cwebp / Pillow いずれもなし）。Node.js v24 と npm 11 は利用可能。

### 手順

1. **スクラッチパッド**で作業する。リポジトリ内に `package.json` / `node_modules` を作らない

   ```
   C:\Users\murph\AppData\Local\Temp\claude\C--Users-murph-Desktop-sei-ko-org\<session>\scratchpad
   ```

2. そこで必要なパッケージを入れる

   ```bash
   npm init -y && npm i sharp png-to-ico
   ```

   - `sharp` … SVG→PNG のラスタライズ、リサイズ、JPEG変換に使う
   - `png-to-ico` … マルチサイズ `.ico` の生成に使う

3. 生成スクリプトもスクラッチパッドに置く
4. **完成した画像ファイルだけ**をリポジトリへコピーする

### ネットワークが使えない場合のフォールバック

`npm i` が失敗したら、ブラウザの canvas でラスタライズする方法に切り替える。SVGを `<img>` に読ませて canvas に描画し、`toDataURL('image/png')` で取り出す。この場合は必ずユーザーに状況を報告してから進めること。

## 7. やってはいけないこと（全フェーズ共通）

- **事実の捏造** — 経歴、実績、顧客数、対応件数、資格などの事実や数値を勝手に書かない。運営者本人しか知り得ない情報は `<!-- TODO(user): ... -->` で空欄として明示し、ユーザーに埋めてもらう
- **既存の文言の書き換え** — 指示にない本文コピーを「良くしよう」として書き換えない。デザイン・画像の作業であって、コピーライティングの作業ではない
- **`contact.php` / `tools/` への変更** — メール送信とGoogle Sheets連携の本番系。今回のスコープ外
- **`.htaccess` のCSP緩和** — アイコンやスタイルを動かすためにCSPを緩めない。CSPに適合する実装方法を選ぶ
- **スコープ外のリファクタリング** — 気になる箇所を見つけても、そのフェーズの担当範囲外なら `PROGRESS.md` の「申し送り」に書くだけにする
- **git commit / push** — ユーザーが明示的に指示しない限り、コミットしない

## 8. 検証の基本

各フェーズの検証手順に加えて、共通で以下を行う。

```bash
# ローカルサーバーを立てて目視確認
python -m http.server 8777
```

- `http://localhost:8777/` を開き、**ブラウザのコンソールにCSP違反やエラーが出ていないか**確認する
- 4ページすべて（`/`, `/guide.html`, `/privacy.html`, `/404.html`）を開いて崩れがないか確認する
- 幅 375px（スマホ）/ 768px（タブレット）/ 1280px（PC）で確認する。`styles.css` にはこの3段階のブレークポイントがある

## 9. フェーズ完了時にやること

1. そのフェーズの「完了条件」チェックリストをすべて満たしていることを確認する
2. 「検証手順」を実際に実行する
3. **[PROGRESS.md](PROGRESS.md) に追記する**（フォーマットは各プロンプトの最終節に記載）
4. ユーザーに、やったこと・確認したこと・積み残しを報告する

検証が通らなかった項目は、**通ったふりをせず正直に報告する**こと。
