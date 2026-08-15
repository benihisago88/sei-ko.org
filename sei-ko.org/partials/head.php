<?php if (!defined('APP')) { http_response_code(404); exit; } ?>
<!doctype html>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
<?php if (!empty($page['description'])): ?>
    <meta name="description" content="<?= e($page['description']) ?>" />
<?php endif; ?>
<?php if (!empty($page['keywords'])): ?>
    <meta name="keywords" content="<?= e($page['keywords']) ?>" />
<?php endif; ?>
    <meta name="robots" content="<?= e($page['robots'] ?? 'index,follow') ?>" />
<?php if (!empty($page['path'])): // 公開ページのみ canonical と OGP を出す ?>
    <meta property="og:type" content="<?= e($page['og_type'] ?? 'website') ?>" />
    <meta property="og:title" content="<?= e($page['og_title'] ?? $page['title']) ?>" />
    <meta property="og:description" content="<?= e($page['og_description'] ?? $page['description']) ?>" />
    <meta property="og:url" content="<?= e(absUrl($page['path'])) ?>" />
    <meta property="og:locale" content="ja_JP" />
    <meta property="og:site_name" content="リモートお助け隊" />
    <meta property="og:image" content="<?= e(absUrl(OGP_PATH) . '?v=' . V_ASSET) ?>" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:image:alt" content="<?= e(OGP_ALT) ?>" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?= e($page['og_title'] ?? $page['title']) ?>" />
    <meta name="twitter:description" content="<?= e($page['og_description'] ?? $page['description']) ?>" />
    <meta name="twitter:image" content="<?= e(absUrl(OGP_PATH) . '?v=' . V_ASSET) ?>" />
<?php endif; ?>
    <meta name="theme-color" content="#0e3d38" />
<?php if (!empty($page['path'])): ?>
    <link rel="canonical" href="<?= e(absUrl($page['path'])) ?>" />
    <link rel="alternate" hreflang="ja" href="<?= e(absUrl($page['path'])) ?>" />
<?php endif; ?>
    <link rel="icon" href="/favicon.svg?v=<?= V_ICON ?>" type="image/svg+xml" />
    <link rel="icon" href="/favicon-32.png?v=<?= V_ASSET ?>" sizes="32x32" type="image/png" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=<?= V_ASSET ?>" />
    <link rel="manifest" href="/site.webmanifest?v=<?= V_ASSET ?>" />
    <title><?= e($page['title']) ?></title>
<?php foreach ($page['preload'] ?? [] as $preload): ?>
    <link rel="preload" href="<?= e($preload['href']) ?>" as="image" type="<?= e($preload['type']) ?>" media="<?= e($preload['media']) ?>" />
<?php endforeach; ?>
    <link rel="stylesheet" href="styles.css?v=<?= V_CSS ?>" />
<?php foreach ($page['jsonld'] ?? [] as $schema): ?>
    <script type="application/ld+json"><?= jsonLd($schema) ?></script>
<?php endforeach; ?>
  </head>
