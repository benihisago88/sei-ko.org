<?php
if (!defined('APP')) { http_response_code(404); exit; }

/**
 * ロゴ（ヘッダー・フッター共通）
 *
 * @var string $brandHref     リンク先
 * @var bool   $brandScreenReader スクリーンリーダー向けの補足を出すか（ヘッダーのみ true）
 */
$brandScreenReader = $brandScreenReader ?? false;
?>
<a class="brand" href="<?= e($brandHref) ?>"><span class="brand-mark">助</span><span class="brand-copy"><span class="brand-service"><span class="brand-dot">リモート</span>お助け隊</span><span class="brand-owner">運営：誠幸（セイコウ）</span></span><?= $brandScreenReader ? '<span class="sr-only">トップへ</span>' : '' ?></a>
