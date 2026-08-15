<?php
if (!defined('APP')) { http_response_code(404); exit; }

// トップページだけページ内アンカー、下層ページはトップへのリンクにする
$isHome    = !empty($page['is_home']);
$homeHref  = $isHome ? '#top' : 'index.php';
$contactHref = $isHome ? '#contact' : 'index.php#contact';
?>
  <body<?= !empty($page['body_id']) ? ' id="' . e($page['body_id']) . '"' : '' ?>>
    <a class="skip-link" href="#main-content">本文へ移動</a>
<?php if ($isHome): ?>
    <div class="page-glow glow-one"></div><div class="page-glow glow-two"></div>
<?php endif; ?>
    <header class="site-header">
<?php
$brandHref = $homeHref;
$brandScreenReader = true;
require __DIR__ . '/brand.php';
?>
<?php if ($isHome): ?>
      <nav aria-label="メインナビゲーション"><a href="#service">できること</a><a href="#cases">活用イメージ</a><a href="#pricing">料金</a><a href="guide.php">AI活用ガイド</a><a href="#faq">よくある質問</a></nav>
<?php endif; ?>
      <a class="header-cta" href="<?= e($contactHref) ?>">無料相談を予約 <span>→</span></a>
<?php if ($isHome): ?>
      <details class="mobile-nav"><summary>メニュー</summary><nav aria-label="モバイルナビゲーション"><a href="#service">できること</a><a href="#cases">活用イメージ</a><a href="#pricing">料金</a><a href="guide.php">AI活用ガイド</a><a href="#faq">よくある質問</a><a class="mobile-nav-cta" href="#contact">無料相談を予約 →</a></nav></details>
<?php endif; ?>
    </header>
