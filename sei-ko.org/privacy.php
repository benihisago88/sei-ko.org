<?php
declare(strict_types=1);

define('APP', true);
require __DIR__ . '/partials/config.php';

$page = [
    'title'        => 'プライバシーポリシー | リモートお助け隊',
    'robots'       => 'noindex,follow',
    'is_home'      => false,
    'script'       => false,
    'footer_links' => [],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>

    <main id="main-content" class="legal-page section-wrap">
      <p class="eyebrow"><span></span> PRIVACY POLICY</p>
      <h1>プライバシーポリシー</h1>
      <p>「リモートお助け隊」を運営する誠幸（セイコウ）（以下「当方」）は、お問い合わせでお預かりする個人情報を適切に取り扱います。</p>

      <h2>取得する情報</h2>
      <p>会社名、お名前、メールアドレス、お問い合わせ内容を取得します。</p>

      <h2>利用目的</h2>
      <p>お問い合わせへの回答、サービスのご案内、必要な連絡のために利用します。</p>

      <h2>第三者提供</h2>
      <p>法令に基づく場合を除き、ご本人の同意なく第三者へ提供しません。</p>

      <h2>安全管理</h2>
      <p>個人情報への不正アクセス、漏えい、改ざん等を防ぐため、合理的な安全対策を講じます。</p>

      <h2>お問い合わせ</h2>
      <p>個人情報の開示・訂正・削除等に関するご連絡は、<a href="index.php#contact">お問い合わせフォーム</a>からお願いします。</p>

      <p class="legal-date">制定日：2026年7月16日</p>
    </main>
<?php require __DIR__ . '/partials/footer.php'; ?>
