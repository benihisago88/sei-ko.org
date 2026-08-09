# 引き継ぎ文書

**作成日**: 2026-08-09
**ブランチ**: `design-refresh/phase-0`
**最新コミット**: `3a32798` — Update service icons to SVG format and refresh stylesheet version

---

## 完了フェーズ

| Phase | 内容 | コミット |
|---|---|---|
| 0 | ブランド色の一元化 | `af70c39` に含まれる |
| 1 | ファビコン一式の刷新 | `af70c39` に含まれる |
| 6 | OGP画像の刷新と guide.html の図解追加 | `24cbc26` |
| 2 | サービスカードアイコンのSVG化 | `3a32798` |

## 未着手フェーズ（推奨順）

| 順序 | Phase | 内容 |
|---|---|---|
| 次 | 3 | ヒーロー画像の軽量化 |
| 次々 | 4 | 運営者セクション新設 |
| その後 | 5 | USE CASES タブ別ビジュアル |
| 最後 | 7 | 仕上げと全体検証 |

## 現在のキャッシュバスター

| アセット | 値 |
|---|---|
| `styles.css` | `?v=20260809.4` |
| アイコン系（favicon, manifest 等） | `?v=20260809.2` |
| `ogp.jpg` / `ogp-guide.jpg` | `?v=20260809.3` |
| `script.js` | `?v=20260808.2` |

## 重要な注意点

1. **CSP を緩めない** — インライン style / script は一切不可。CSS は必ず `styles.css` に書く
2. **キャッシュバスター** — アセット変更時は `?v=YYYYMMDD.N` を上げ、**4ページすべて**（`index.html` / `guide.html` / `privacy.html` / `404.html`）を同時更新
3. **`contact.php` / `tools/` に触らない**
4. **`package.json` / `node_modules` を作らない** — 画像生成はスクラッチパッドで
5. **`git commit` はユーザーの明示指示があるまでしない**
6. **事実を捏造しない** — 不明な数値・実績は `<!-- TODO(user): ... -->` で空欄に
7. **スコープ外を「ついでに」直さない** — 気づいた点は `PROGRESS.md` の申し送りに書く

## 作業開始時の手順

1. `docs/design-refresh/00-common-constraints.md` を読む
2. `docs/design-refresh/00-brand-tokens.md` を読む
3. `docs/design-refresh/PROGRESS.md` を読む（特に Phase 2 の申し送り）
4. 該当フェーズの `phase-N-*.md` を読む
5. 着手

## 検証

```bash
python -m http.server 8777
```

- 4ページすべて（`/`, `/guide.html`, `/privacy.html`, `/404.html`）を開く
- 375px / 768px / 1280px で確認
- コンソールに CSP 違反・エラーがないことを確認