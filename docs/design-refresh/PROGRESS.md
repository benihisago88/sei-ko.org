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
| 1 | ファビコン一式の刷新 | 完了 |
| 2 | サービスアイコンSVG化 | 完了 |
| 3 | ヒーロー画像の軽量化 | 完了 |
| 4 | 運営者セクション新設 | 見送り（ユーザー判断） |
| 5 | USE CASES タブ別ビジュアル | 完了 |
| 6 | guide.html 強化 | 完了 |
| 7 | 仕上げと全体検証 | 完了 |

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

### Phase 1: ファビコン／アプリアイコン一式の刷新

- **状態**: 完了
- **日付**: 2026-08-09
- **キャッシュバスター**: アイコン系 `?v=20260809.2`（4ページ + manifest で統一）
- **採用した図柄**: 案A（「担」そのまま）。16px / 32px / 48pxの実寸比較を提示し、ユーザーが案Aを選択。
- **変更ファイル**:
  - `favicon.svg` — 真円の濃紺背景に白い「担」のアウトラインを配置し、aria-labelを「デジタル担当室」に更新
  - `apple-touch-icon.png` / `icon-192.png` / `icon-512.png` — 新図柄で再生成
  - `site.webmanifest` — iconsを4エントリ（any×2 / maskable×2）にし、全参照を新キャッシュバスターへ更新
  - `index.html` / `guide.html` / `privacy.html` / `404.html` — ico → SVG → apple-touch-icon → manifestの4行に統一
- **新規ファイル**:
  - `favicon.ico` — 16px / 32px / 48pxを内包
  - `icon-maskable-192.png` / `icon-maskable-512.png` — フルブリード濃紺背景のmaskableアイコン
- **削除ファイル**:
  - `favicon-32.png` — favicon.icoへ統合。削除前後とも参照0件を確認
- **検証結果**:
  - sharpでapple-touch-icon 180×180・透過なし、通常/ maskable各192×192・512×512を確認
  - favicon.icoは3画像（16×16 / 32×32 / 48×48）を内包していることを確認
  - favicon.svgに`<text>`がなく、aria-labelが現ブランド名であることを確認
  - maskable画像の四隅が`#11153f`のフルブリード背景であることをピクセル値で確認。中央の「担」は幅約41%で、60%以内
  - 4ページのアイコン宣言は各4行・同一順序・同一値であること、manifestのpurposeはany×2 / maskable×2、theme_colorは`#11153f`であることを確認
  - ローカルサーバーでアイコン/manifestの8ファイルがすべて200。実ブラウザで4ページを開き、コンソールのerror/warning 0件、375px / 768px / 1280pxで横スクロールなしを確認
  - Chrome DevToolsのApplication画面での「minimum safe area」手動表示は、この環境では操作できないため未実施。上記の生成時セーフゾーン寸法・ピクセル値で代替確認
  - リポジトリ直下の`package.json` / `node_modules`が存在しないこと、`git diff --check`が通ることを確認
- **次フェーズへの申し送り**:
  - 「担」はSIL Open Font LicenseのNoto Sans JP 700（`@fontsource/noto-sans-jp`のWOFFサブセット）から取得し、SVGのpathデータとしてのみ配置。フォントファイルはリポジトリに含めていない
  - 512px用の別SVGは使っていない。通常アイコンは64pxの元SVGを高密度でラスタライズしたもの
  - アイコンを再変更する場合は`?v=20260809.2`から連番を上げ、4ページとmanifestを同時更新すること
- **スコープ外で気づいた点**: なし

### Phase 6: OGP画像の刷新と guide.html の図解追加

- **状態**: 完了
- **日付**: 2026-08-09
- **キャッシュバスター**: ogp.jpg / ogp-guide.jpg / styles.css `?v=20260809.3`
- **変更ファイル**:
  - `assets/ogp.jpg` — 現行サイトのヒーローと揃う白地・濃紺文字・シアン強調・右側の丸み写真のOGPへ差し替え
  - `index.html` / `privacy.html` — og:imageとtwitter:imageのキャッシュバスター、画像内容に合うog:image:altを更新
  - `guide.html` — 専用OGP参照とaltへ更新し、目次直後にアクセシブルな縦型5ステップフロー図を追加
  - `404.html` — styles.cssのキャッシュバスターを4ページと同じ値へ更新
  - `styles.css` — `.article-flow`の表示スタイルを追加
- **新規ファイル**:
  - `assets/ogp-guide.jpg` — 1200×630 / 68,908 bytes
- **ユーザー確認の結果**:
  - 「初回相談無料」バッジ: 削除
  - OGP画像の承認: 得た（濃紺ベタ案を取り下げ、現行ヒーローと整合する白地案を採用）
- **検証結果**:
  - `assets/ogp.jpg` = 1200×630 / 62,941 bytes、`assets/ogp-guide.jpg` = 1200×630 / 68,908 bytes。両画像を目視し、日本語の欠落・旧ブランド表記・黄緑バッジ・廃止色がないことを確認
  - ローカル `python -m http.server 8777` とChromiumで `/` / `/guide.html` / `/privacy.html` / `/404.html` を375px / 768px / 1280pxで確認。全12画面が200、横スクロール・コンソールerror/warning・失敗リクエストなし
  - フロー図は全幅で320×560px、`role="img"`と5ステップを含むアクセシブル名を確認。Chrome Accessibilityツリーでもimageロールと名称を確認
  - OGP参照先・alt・1200/630メタデータ、4ページのstyles.cssクエリ同値、インラインstyleなし、リポジトリ内のpackage.json/node_modulesなし、`git diff --check`を確認
  - 公開中の `/` と `/guide.html` はCSP維持をHTTPヘッダで確認。SNS実表示は未検証（本番反映後に要確認）
- **次フェーズへの申し送り**:
  - `404.html` にはOGPタグ自体がない。Phase 7で追加するか判断すること
  - 本番反映後、X Card ValidatorおよびFacebook Sharing Debuggerでキャッシュを再取得してSNS表示を確認すること
- **スコープ外で気づいた点**: なし

### Phase 2: サービスカードアイコンのSVG化

- **状態**: 完了
- **日付**: 2026-08-09
- **キャッシュバスター**: styles.css `?v=20260809.4`
- **変更ファイル**:
  - `index.html` — service-icon 4箇所を記号文字（⌘ ▤ ✦ ↗）からインラインSVGへ置換。未定義クラス `dark-card` / `mint-card` / `sand-card` を削除（ユーザー確認済み）
  - `styles.css` — `.service-icon` の `font-size: 20px` を削除、`.service-icon svg` のサイズ指定（22px×22px）を追加
  - `index.html` / `guide.html` / `privacy.html` / `404.html` — styles.css のキャッシュバスターを `?v=20260809.4` に更新
- **採用した図像**:
  - 01 / WEB: ブラウザウィンドウ（上部バー付き矩形 + アドレスバー + テキスト行）
  - 02 / EC: ショッピングバッグ（本体 + 取っ手）
  - 03 / AI: 4点スパークル（きらめき星形）
  - 04 / SUPPORT: 吹き出し（チャットバブル）
- **検証結果**:
  - 旧記号文字（⌘ ▤ ✦ ↗）: index.html 内 **0件**（search_files で確認）
  - `viewBox="0 0 24 24"` の SVG: **4件**（全カード）
  - `dark-card` / `mint-card` / `sand-card`: index.html 内 **0件**（削除確認）
  - キャッシュバスター `?v=20260809.4`: **4ページすべて同値**（search_files で確認）
  - ローカルサーバー（`python -m http.server 8777`）で `/` / `/guide.html` / `/privacy.html` / `/404.html` すべて **200**（curl で確認）
  - ブラウザで `/` を開き、サービスカード4枚のアイコンがシアンの線画SVGで表示されることを目視確認。コンソールエラー・CSP違反なし
  - 375px / 768px / 1280px で横スクロール発生なし、アイコン崩れなし
  - ユーザーによる図像確認: **未実施**（ユーザーに確認を依頼すること）
- **次フェーズへの申し送り**:
  - Phase 5（ケース図解）は、ここで作ったアイコンと同じ作図ルール（`viewBox 0 0 24 24` / `stroke-width 1.75` / `currentColor` / 線画）で揃えること
  - アイコンの意味が伝わるか、ユーザーに確認を取ること（phase-2-service-icons.md 6-6）
- **スコープ外で気づいた点**: なし

### Phase 3: ヒーロー画像の軽量化

- **状態**: 完了
- **日付**: 2026-08-09
- **キャッシュバスター**: なし（拡張子変更でURLが変わるため不要）
- **変更ファイル**:
  - `index.html` — hero の `<img src>` を `.png` から `.jpg` へ変更
- **新規ファイル**:
  - `assets/hero-consultation.jpg` — 1672×941 / quality 80 / 118,013 bytes
- **削除ファイル**:
  - `assets/hero-consultation.png` — 1,740,979 bytes（gitコミット済みを確認のうえ削除）
- **削減実績**: 1,740,979 B → 118,013 B（93.2% 削減）
- **検証結果**:
  - JPEGの寸法・サイズ、PNG参照の残存なし、`styles.css` が未変更であることを確認
  - ローカルサーバーで `/` / `/guide.html` / `/privacy.html` / `/404.html` / `assets/hero-consultation.jpg` がすべて200。JPEGの直接表示も正常であることを確認
  - Chromiumの375px / 768px / 1280pxスクリーンショットで、ヒーロー画像の構図切替・表示崩れ・横スクロールがないことを確認
  - 本番（2026-08-09）で4ページのCSPヘッダを実測し、`default-src 'self'` ほか既定ポリシーの維持を確認。375px / 768px / 1280px の実ブラウザ検証で、選択されたwebp・コンソールエラー・失敗リクエスト・横スクロールはすべて問題なし
- **次フェーズへの申し送り**:
  - webp 3種のアスペクト比が異なるのは意図的なアートディレクション。CSSの `object-fit: cover` が吸収しているので触らないこと
- **スコープ外で気づいた点**:
  - 本番の `assets/hero-consultation.png` は200で残存しているが、HTMLからの参照はない。サーバー容量も削減するには、公開先から旧PNGを別途削除すること

### Phase 4: 運営者セクションの新設

- **状態**: 見送り
- **日付**: 2026-08-09
- **理由**: ユーザー判断により、運営者自身の確認が必要な経歴・得意領域などを掲載せず、本フェーズは実装しない。
- **変更ファイル**: なし
- **次フェーズへの申し送り**: 後日追加する場合は `phase-4-profile-section.md` の聞き取り手順に従い、確認済みの事実だけを本文に記載する。

### Phase 5: USE CASES のタブ別ビジュアル

- **状態**: 完了
- **日付**: 2026-08-09
- **キャッシュバスター**: styles.css `?v=20260809.5`（4ページ）/ script.js `?v=20260809.1`（index.html）
- **変更ファイル**:
  - `index.html` — `.blueprint` を `#caseFigure` に置換し、WEB用の初期SVG、タイムラインの補助アイコン、料金カードの区別用クラスを追加
  - `script.js` — CASE_DATAにweb / ec / aiの静的な`figure`を追加し、タブ切替時に`#caseFigure`を更新
  - `styles.css` — 旧ブループリント規則を削除し、SVG図解・タイムラインアイコン・料金カード上部アクセントを追加
- **採用した図解**: web=ブラウザ内の部分修正 / ec=商品・カート・決済・配送の流れ / ai=定型作業を絞り込み、人が最終確認する流れ
- **副次的に解消**: `.bp-line` / `.bp-square` と緑 `#86b89e` を削除
- **検証結果**:
  - `node --check script.js`、初期SVGとCASE_DATA.web.figureの完全一致、3図解の属性、旧ブループリント参照0件を確認
  - ローカルブラウザでweb / ec / aiのクリック・矢印キー切替を確認。図解・バッジ・本文が同期し、console error / warningは0件
  - 375px / 768px / 1280pxで図解・バッジ・キャプションの表示、横スクロールなしを確認
- **次フェーズへの申し送り**: なし
- **スコープ外で気づいた点**: なし

### Phase 7: 仕上げと全体検証

- **状態**: 完了
- **日付**: 2026-08-09
- **最終キャッシュバスター**: styles.css `?v=20260809.5` / script.js `?v=20260809.1` / アイコン系 `?v=20260809.2` / OGP `?v=20260809.3`
- **ユーザー判断**: タイムラインは番号とアイコンを併記、料金カードは上部アクセントライン、404はOGP追加。Phase 4は見送り。
- **変更ファイル**:
  - `index.html` / `styles.css` — タイムライン4段階の装飾SVGと料金カード3種の上部アクセントを追加
  - `404.html` — canonicalを追加せず、既存OGP画像を参照するOGP / Twitterメタデータを追加
  - `index.html` — `#casePanel`を`div`へ変更し、`tabpanel`ロールとのHTML要素の不整合を解消
- **検証で見つかった不整合と修正内容**:
  - Lighthouseが`article#casePanel`の`role=tabpanel`を不適切と検出したため、見た目を変えず`div`へ変更。再監査で当該指摘は解消した。
  - 4ページのstyles.cssクエリは`20260809.5`で統一。ローカルの参照切れは51件中0件、旧ブランド色・旧ブループリント参照は0件。
- **検証結果**:
  - ローカル4ページを375px / 768px / 1280pxで確認し、全12画面で200、横スクロールなし、console error / warningなし
  - ケースタブのクリック・矢印キー、準備度診断、skip-link、情報SVGの`role=img` / `aria-label`、装飾SVGの`aria-hidden=true`を確認
  - Lighthouse 12.8.2 Accessibility: index 0.97 / guide 0.95 / privacy 0.92 / 404 0.90。残る自動指摘は既存配色の`color-contrast`のみで、今回のSVG・ARIA変更には新たな指摘なし。
  - 本番でルートの厳格CSP、`/docs/design-refresh/README.md`と`/tools/google-sheets-relay.gs`の403を再確認
- **本番反映後に確認すること**:
  - X Card Validator と Facebook Sharing Debugger でOGPキャッシュを再取得し、新しい画像を確認する
  - スマホでホーム画面に追加し、maskableアイコンの角が欠けていないことを確認する
  - Google Search Consoleでファビコンの再クロールを待つ
- **スコープ外で気づいた点**: 既存配色のLighthouse `color-contrast` 指摘は、個別の配色レビューとして扱う。

### SEO: テクニカルSEO・構造化データ・パンくずリスト追加

- **状態**: 完了
- **日付**: 2026-08-10
- **キャッシュバスター**: styles.css `?v=20260810.1`（4ページ統一）
- **変更ファイル**:
  - `styles.css` — `.breadcrumb`（パンくずナビゲーション）のスタイル定義を追加
  - `guide.html` — `BreadcrumbList` 構造化データ、`Article` 画像・Publisherメタ拡張、HTMLパンくずナビゲーションを追加
  - `index.html` — `ProfessionalService` JSON-LDに `priceRange`, `knowsAbout` などを追加、個別の `Service` 構造化データを新設
  - `privacy.html` / `404.html` — `styles.css` キャッシュバスターを `?v=20260810.1` へ同時更新
  - `sitemap.xml` — `<lastmod>` を `2026-08-10` に更新
- **検証結果**:
  - `python -m http.server 8777` にて `/` / `guide.html` / `privacy.html` / `404.html` / `sitemap.xml` 全5レスポンスが 200 OK
### REFACTOR: コードの可読性向上とリファクタリング

- **状態**: 完了
- **日付**: 2026-08-10
- **キャッシュバスター**: script.js `?v=20260810.2` (index.html)
- **変更ファイル**:
  - `script.js` — 変数・関数名を具体的な名前に変更、早期リターン導入、Why（設計意図）重視のコメント拡充
  - `index.html` — script.js キャッシュバスター更新、casePanelへのrole/tabindex静的付与
- **検証結果**:
  - `node --check script.js` にて構文エラーなしを確認
  - ローカルサーバー `python -m http.server 8777` にて index.html / script.js が 200 OK であることを確認
- **次フェーズへの申し送り**: なし
- **スコープ外で気づいた点**: なし

## 総括

| Phase | 内容 | 状態 |
|---|---|---|
| 0 | ブランド色の一元化 | 完了 |
| 1 | ファビコン一式の刷新 | 完了 |
| 2 | サービスカードアイコンSVG化 | 完了 |
| 3 | ヒーロー画像の軽量化 | 完了 |
| 4 | 運営者セクション新設 | 見送り（ユーザー判断） |
| 5 | USE CASES タブ別ビジュアル | 完了 |
| 6 | guide.html 強化 | 完了 |
| 7 | 仕上げと全体検証 | 完了 |

---

## Phase 1 事前検証メモ（2026-08-09、Phase 1着手前に作成した履歴）

Phase 1着手前に、ツールチェーンをスクラッチパッドで**実際に通して確認した履歴**。Phase 1の完了記録は上記の「Phase 1: ファビコン／アプリアイコン一式の刷新」を正とする。アイコンを再生成する場合は、このメモを参照すること。
この事前検証の時点では、リポジトリには何も追加していなかった（`package.json` / `node_modules` なし、生成物も未コピー）。Phase 1完了後の変更内容は上記完了記録を参照すること。

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
