# Phase 0: ブランド色の一元化

## 0. 前提（必ず最初に読む）

作業開始前に、以下を順に読むこと。

1. `docs/design-refresh/00-common-constraints.md` — 全フェーズ共通の制約
2. `docs/design-refresh/00-brand-tokens.md` — ブランド仕様（色の正）
3. `docs/design-refresh/PROGRESS.md` — 進捗ログ（Phase 0 なので記録は空のはず）

対象リポジトリ: `C:\Users\murph\Desktop\sei-ko.org`

---

## 1. ゴール

このサイトは旧ブランド「さいたま 社長のパソコン・AIお助け隊」から「デジタル担当室」へ改称した経緯があり、**旧ブランドの深緑・黄緑がコード内に10箇所残っている**。実際のサイトの見た目は濃紺＋シアンなので、ファビコン・ブラウザのテーマカラー・フッターだけが緑という不整合が起きている。

**このフェーズが完了すると:**

- リポジトリ内から旧ブランド色 `#0e3d38` / `#d7ee87` / `#fff4d6` が消える
- ブラウザのアドレスバー・タスクスイッチャーの色（`theme-color`）が濃紺になり、サイト本体と一致する
- ヘッダーとフッターで「デジタル」の文字色が揃う
- `docs/` と `tools/` が外部から閲覧できなくなる

**これは後続フェーズすべての前提。** Phase 1 以降で作るアイコンの色はここで確定した値を使うため、必ず最初に実行すること。

---

## 2. 対象ファイルと現状

### 2-1. 旧ブランド色の残存箇所（grep で11行 / 対応する修正は10項目）

内訳: `#0e3d38` × 7行 / `#d7ee87` × 3行 / `#fff4d6` × 1行 = **11行**。
下の表が10行なのは、**項目7 が `favicon.svg` の3行目と4行目の2行をまとめている**ため。表の項目数と grep の行数が食い違って見えるのは正常。

以下のコマンドで現状を確認できる。**着手前に必ず実行し、下の表と一致することを確認すること。**行番号がずれていたら、表ではなく実際の grep 結果を信じる。

```bash
grep -rn "0e3d38\|d7ee87\|fff4d6" --include="*.html" --include="*.css" --include="*.svg" --include="*.webmanifest" . | grep -v "^./docs"
```

| # | ファイル:行 | 現在の値 | 置換後 | 理由 |
|---|---|---|---|---|
| 1 | `index.html:24` | `theme-color` `#0e3d38` | `#11153f` | `--ink` に一致させる |
| 2 | `guide.html:22` | `theme-color` `#0e3d38` | `#11153f` | 同上 |
| 3 | `privacy.html:20` | `theme-color` `#0e3d38` | `#11153f` | 同上 |
| 4 | `404.html:7` | `theme-color` `#0e3d38` | `#11153f` | 同上 |
| 5 | `site.webmanifest:10` | `theme_color` `#0e3d38` | `#11153f` | 同上 |
| 6 | `favicon.svg:2` | `rect fill="#0e3d38"` | `#11153f` | ※下の注記を読むこと |
| 7 | `favicon.svg:3,4` | `stroke="#d7ee87"` | `#35bfd2` | ※下の注記を読むこと |
| 8 | `favicon.svg:5` | `path fill="#fff4d6"` | `#ffffff` | ※下の注記を読むこと |
| 9 | `styles.css:315` | `box-shadow: 0 10px 20px #0e3d3824` | `#11153f24` | 8桁hex。末尾 `24` はアルファ値なので保持する |
| 10 | `styles.css:1304` | フッター `.brand-dot` `#d7ee87` | `#47c4d0` | ← **理由は下記** |

#### #10 の置換先が `#35bfd2` ではなく `#47c4d0` である理由

フッターは暗い背景（`footer { background: #0d1232 }`、`styles.css:1272`）。すぐ隣の `.footer-inner .brand-mark` は既に `#47c4d0`（`styles.css:1293`）という**明るめのシアン**を使っている。暗い背景上ではこちらの方が可読性が高く、隣接要素と色が揃う。

ヘッダー側の `.brand-dot` は白背景なので `#1faec3`（濃いめ）のまま変更しない。**明るい背景では濃いシアン、暗い背景では明るいシアン**という既存の使い分けを踏襲する。

#### #6〜#8（favicon.svg）についての重要な注記

`favicon.svg` は **Phase 1 で図柄ごと作り直す**（モニター＋星 → 円形＋「担」）。

したがってこのフェーズでは:

- **色だけを置換して、図柄はそのままにしておく**
- ファイルを消したり作り直したりしない
- `aria-label` の旧ブランド名（「さいたま 社長のパソコン・AIお助け隊」）も**このフェーズでは触らない**（Phase 1 の担当範囲）

理由: Phase 0 は「色の一元化」だけを差分にして、レビューしやすくするため。図柄と色を同時に変えると、何が原因で見た目が変わったのか追えなくなる。

### 2-2. `.htaccess` の現状

```apache
ErrorDocument 404 /404.html

Options -Indexes

DirectoryIndex index.html

<IfModule mod_rewrite.c>
  RewriteEngine On
  # www なし（apex）へのアクセスを www ありに 301 で集約する。
  RewriteCond %{HTTP_HOST} ^sei-ko\.org$ [NC]
  RewriteRule ^(.*)$ https://www.sei-ko.org/$1 [R=301,L]
</IfModule>
...
```

**問題**: `docs/` と `tools/` はWebルート配下にあるため、そのまま公開されている。

- `https://www.sei-ko.org/docs/design-refresh/README.md` → 誰でも読める（このプラン文書一式）
- `https://www.sei-ko.org/tools/google-sheets-relay.gs` → 誰でも読める（Google Sheets連携スクリプト）

`Options -Indexes` はディレクトリ一覧を止めるだけで、**ファイル名が分かれば読めてしまう**。

---

## 3. 作業手順

### Step 1: 現状の確認

```bash
grep -rn "0e3d38\|d7ee87\|fff4d6" --include="*.html" --include="*.css" --include="*.svg" --include="*.webmanifest" . | grep -v "^./docs"
```

**11行**ヒットすることを確認する。件数が違う場合は、表と実際の差分をユーザーに報告してから進める。

### Step 2: `theme-color` の置換（4ページ + manifest）

`index.html` / `guide.html` / `privacy.html` / `404.html` の `<meta name="theme-color">` と、`site.webmanifest` の `"theme_color"` を `#11153f` にする。

**5箇所すべてが同じ値であること。** 1つでも漏れると、ページ間でアドレスバーの色が変わる。

### Step 3: `styles.css` の2箇所を置換

- `styles.css:315` — `#0e3d3824` → `#11153f24`（**末尾の `24` はアルファ値。消さない**）
- `styles.css:1304` — `#d7ee87` → `#47c4d0`

### Step 4: `favicon.svg` の色だけ置換

`fill="#0e3d38"` → `#11153f`、`stroke="#d7ee87"` → `#35bfd2`、`fill="#fff4d6"` → `#ffffff`。

**図柄と `aria-label` は変更しない**（Phase 1 の担当）。

### Step 5: `.htaccess` に `docs/` と `tools/` の配信拒否を追加

既存の `<IfModule mod_rewrite.c>` ブロック内、`RewriteEngine On` の**直後**に追加する。

```apache
<IfModule mod_rewrite.c>
  RewriteEngine On

  # 作業用ドキュメントと連携スクリプトは公開しない。
  RewriteRule ^(?:docs|tools)/ - [F,L]

  # www なし（apex）へのアクセスを www ありに 301 で集約する。
  RewriteCond %{HTTP_HOST} ^sei-ko\.org$ [NC]
  RewriteRule ^(.*)$ https://www.sei-ko.org/$1 [R=301,L]
</IfModule>
```

**この方法を選ぶ理由**: `Require all denied`（Apache 2.4）と `Deny from all`（2.2）はバージョンで書式が違い、間違えるとサイト全体が 500 になる。mod_rewrite の `[F]` はどちらでも動き、既に `<IfModule mod_rewrite.c>` で存在確認もされている。

**注意**: 既存の `<FilesMatch>` ブロック（`mod_headers` 内）とは干渉しない。そちらは触らないこと。

### Step 6: キャッシュバスターの更新

`favicon.svg` の中身を変えたので、参照している4ページのクエリを上げる。

現在: `href="/favicon.svg?v=20260716.5"`
更新後: `href="/favicon.svg?v=<今日の日付>.1"`（例: `?v=20260810.1`）

**4ページすべて**で同じ値にすること。

> `styles.css` も変更したので、そのキャッシュバスター（現在 `?v=20260809.1`）も4ページ分上げること。

---

## 4. やってはいけないこと

- **`favicon.svg` の図柄・`aria-label` を変えない** — Phase 1 の担当範囲
- **ヘッダーの `.brand-dot`（`#1faec3`）を変えない** — 白背景では現状が正しい
- **`styles.css:319` の `.button.primary` 背景 `#33b8cf` を変えない** — 旧ブランド色ではない
- **`--lime` という変数名を変えない** — 名前は "lime" だが値はシアン。リネームすると差分が全域に広がる
- **CSPを緩めない**
- **`contact.php` / `tools/` の中身を触らない**
- **`git commit` しない**

---

## 5. 完了条件

- [ ] `grep -rn "0e3d38\|d7ee87\|fff4d6" --include="*.html" --include="*.css" --include="*.svg" --include="*.webmanifest" . | grep -v "^./docs"` が **0件**
- [ ] `theme-color` が4ページ + manifest の**5箇所すべて** `#11153f`
- [ ] `styles.css:315` のアルファ値 `24` が保持されている（`#11153f24`）
- [ ] `favicon.svg` の図柄（モニター＋星）と `aria-label` が**変わっていない**
- [ ] `.htaccess` に `docs|tools` の `[F,L]` ルールが入っている
- [ ] `favicon.svg` と `styles.css` のキャッシュバスターが4ページで揃っている
- [ ] `PROGRESS.md` に記録を追記した

---

## 6. 検証手順

### 6-1. 旧色が消えたことの確認

```bash
grep -rn "0e3d38\|d7ee87\|fff4d6" --include="*.html" --include="*.css" --include="*.svg" --include="*.webmanifest" . | grep -v "^./docs"
```

**期待結果: 何も出力されない**（0件）

### 6-2. theme-color が揃っていることの確認

```bash
grep -rn "theme.color\|theme_color" --include="*.html" --include="*.webmanifest" . | grep -v "^./docs"
```

**期待結果: 5行すべて `#11153f`**

### 6-3. キャッシュバスターが揃っていることの確認

```bash
grep -rn "favicon.svg?v=\|styles.css?v=" --include="*.html" . | grep -v "^./docs"
```

**期待結果**: `favicon.svg?v=` が4行で同一値、`styles.css?v=` が4行で同一値

### 6-4. 目視確認

```bash
python -m http.server 8777
```

- `http://localhost:8777/` を開き、**ブラウザのコンソールにエラーが出ていない**こと
- フッターの「**デジタル**担当室」の「デジタル」がシアンになっている（緑でない）
- ヘッダーの「デジタル」の色は**変わっていない**
- ボタンにマウスを乗せたとき、影の色が不自然でない
- 4ページすべて開いて崩れがないこと

### 6-5. `.htaccess` の検証について（重要）

`python -m http.server` は `.htaccess` を解釈しないため、**ローカルでは配信拒否を検証できない**。

- ローカルでできるのは**構文の目視確認のみ**
- 実際の確認は本番反映後に `https://www.sei-ko.org/docs/design-refresh/README.md` が **403** を返すことで行う
- **本番で確認するまで「検証済み」と報告しないこと。** `PROGRESS.md` には「未検証（本番反映後に要確認）」と正直に書く

---

## 7. PROGRESS.md への追記内容

`docs/design-refresh/PROGRESS.md` の「フェーズ記録」セクションに追記し、冒頭の状態表も更新する。

```markdown
### Phase 0: ブランド色の一元化

- **状態**: 完了
- **日付**: YYYY-MM-DD
- **キャッシュバスター**: favicon.svg `?v=...` / styles.css `?v=...`
- **変更ファイル**:
  - `index.html` / `guide.html` / `privacy.html` / `404.html` — theme-color を #11153f に、キャッシュバスター更新
  - `site.webmanifest` — theme_color を #11153f に
  - `styles.css` — :315 box-shadow、:1304 フッター brand-dot
  - `favicon.svg` — 色のみ置換（図柄は Phase 1 で作り直す）
  - `.htaccess` — docs/ tools/ の配信拒否を追加
- **検証結果**:
  - 旧色 grep: 0件（確認済み）
  - theme-color 5箇所一致: 確認済み
  - .htaccess の配信拒否: **未検証**（ローカルサーバーは .htaccess を解釈しないため。本番反映後に 403 を確認すること）
- **次フェーズへの申し送り**:
  - `favicon.svg` は色だけ直した状態。図柄はモニター＋星のまま、`aria-label` も旧ブランド名のまま。Phase 1 で円形＋「担」に作り直すこと
  - このフェーズで設定したキャッシュバスターの値: （記入）
- **スコープ外で気づいた点**: （あれば）
```
