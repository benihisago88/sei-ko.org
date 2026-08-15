<?php
if (!defined('APP')) { http_response_code(404); exit; }

$isHome = !empty($page['is_home']);
$links  = $page['footer_links'] ?? [];
?>
    <footer>
      <div class="footer-inner">
<?php
$brandHref = $isHome ? '#top' : 'index.php';
$brandScreenReader = false;
require __DIR__ . '/brand.php';
?>
        <p>© 2026 Saitama PC &amp; AI Support. All rights reserved.</p>
<?php if ($links): ?>
        <div><?php foreach ($links as $href => $label): ?><a href="<?= e($href) ?>"><?= e($label) ?></a><?php endforeach; ?></div>
<?php endif; ?>
      </div>
    </footer>
<?php if (!empty($page['script'])): ?>
    <script src="script.js?v=<?= V_JS ?>" defer></script>
<?php endif; ?>
  </body>
</html>
