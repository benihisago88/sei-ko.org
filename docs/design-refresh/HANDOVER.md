# デザイン刷新の引き継ぎ記録（履歴）

> **注意**: この文書は2026-08-09時点の作業記録です。記載されたブランチ名・
> コミット・キャッシュバスターは当時の値であり、現在の配信状態を示しません。
> 現在の状態は[PROGRESS.md](PROGRESS.md)、通常運用は
> [リポジトリREADME](../../README.md)と
> [運用手順](../operations/deployment-and-recovery.md)を参照してください。

**作成日／最終更新日**: 2026-08-09
**ブランチ**: `design-refresh/phase-0`
**作業開始時の最新コミット**: `ff37601` — Document production verification and note obsolete hero asset

---

## 完了フェーズ

| Phase | 内容 | コミット |
|---|---|---|
| 0 | ブランド色の一元化 | `af70c39` に含まれる |
| 1 | ファビコン一式の刷新 | `af70c39` に含まれる |
| 6 | OGP画像の刷新と guide.html の図解追加 | `24cbc26` |
| 2 | サービスカードアイコンのSVG化 | `3a32798` |
| 3 | ヒーロー画像の軽量化 | `cdd6a0e` |
| 5 | USE CASES タブ別ビジュアル | この変更を含むコミット |
| 7 | 仕上げと全体検証 | この変更を含むコミット |

## 見送りフェーズ

| Phase | 内容 | 理由 |
|---|---|---|
| 4 | 運営者セクション新設 | ユーザー判断。確認済みの運営者情報がそろったときだけ再開する |

## 当時のキャッシュバスター

| アセット | 値 |
|---|---|
| `styles.css` | `?v=20260809.5` |
| アイコン系（favicon, manifest 等） | `?v=20260809.2` |
| `ogp.jpg` / `ogp-guide.jpg` | `?v=20260809.3` |
| `script.js` | `?v=20260809.1` |

## 重要な注意点

1. **CSP を緩めない** — インライン style / script は一切不可。CSS は必ず `styles.css` に書く
2. **キャッシュバスター** — アセット変更時は `?v=YYYYMMDD.N` を上げ、**4ページすべて**（`index.html` / `guide.html` / `privacy.html` / `404.html`）を同時更新
3. **`contact.php` / `tools/` に触らない**
4. **`package.json` / `node_modules` を作らない** — 画像生成はスクラッチパッドで
5. **`git commit` はユーザーの明示指示があるまでしない**
6. **事実を捏造しない** — 不明な数値・実績は `<!-- TODO(user): ... -->` で空欄に
7. **スコープ外を「ついでに」直さない** — 気づいた点は `PROGRESS.md` の申し送りに書く
8. **本番に旧PNGが残存** — `assets/hero-consultation.png` はHTMLから参照されないが、公開サーバーではまだ200。容量も削減するなら、公開先から別途削除する
9. **SNSのOGP再取得は本番反映後に行う** — X Card Validator と Facebook Sharing Debugger でキャッシュを更新して確認する

## 作業開始時の手順

1. `docs/design-refresh/00-common-constraints.md` を読む
2. `docs/design-refresh/00-brand-tokens.md` を読む
3. `docs/design-refresh/PROGRESS.md` を読む（特に Phase 3 の申し送り）
4. Phase 4を再開する場合だけ `phase-4-profile-section.md` を読み、ユーザー確認済みの事実だけで実装する

## 検証

```bash
python -m http.server 8777
```

- 4ページすべて（`/`, `/guide.html`, `/privacy.html`, `/404.html`）を開く
- 375px / 768px / 1280px で確認
- コンソールに CSP 違反・エラーがないことを確認
