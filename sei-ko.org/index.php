<?php
declare(strict_types=1);

define('APP', true);
require __DIR__ . '/partials/config.php';

$page = [
    'title'          => 'リモートお助け隊 | 誠幸（セイコウ）の中小企業IT相談',
    'description'    => 'さいたま市の中小企業向け、パソコン・Excel・生成AIのお助け隊。業務の困りごとを整理し、現場で使えるまで訪問・オンラインで支援します。初回相談無料。',
    'keywords'       => 'さいたま市 パソコン相談,さいたま市 AI導入,中小企業 AI活用,Excel 支援,生成AI 業務効率化,埼玉 ITコンサルタント',
    'path'           => '/',
    'og_type'        => 'website',
    'og_title'       => 'リモートお助け隊 | 誠幸（セイコウ）',
    'og_description' => 'パソコン・Excel・AIの困りごとを、社長のそばで解決します。',
    'is_home'        => true,
    'body_id'        => 'top',
    'script'         => true,
    'footer_links'   => [
        'privacy.php' => 'プライバシーポリシー',
        '#contact'    => 'お問い合わせ',
    ],
    // ヒーローはLCP要素。ビューポート帯ごとに実際に使われる1枚だけを先読みする
    'preload' => [
        ['href' => 'assets/hero-consultation-sp-1055.webp',  'type' => 'image/webp', 'media' => '(max-width: 480px)'],
        ['href' => 'assets/hero-consultation-1280.webp',     'type' => 'image/webp', 'media' => '(min-width: 481px) and (max-width: 800px)'],
        ['href' => 'assets/hero-consultation-pc-1303.webp',  'type' => 'image/webp', 'media' => '(min-width: 801px)'],
    ],
    'jsonld' => [
        [
            '@context'          => 'https://schema.org',
            '@type'             => 'ProfessionalService',
            'name'              => '誠幸（セイコウ）',
            'description'       => '中小企業向け、パソコン・Excel・生成AIの相談と業務改善支援サービス',
            'areaServed'        => [
                ['@type' => 'City', 'name' => 'さいたま市'],
                ['@type' => 'AdministrativeArea', 'name' => '埼玉県'],
            ],
            'serviceType'       => ['パソコン相談', 'Excel業務改善', 'AI導入支援', '生成AI研修', '業務改善コンサルティング'],
            'url'               => absUrl('/'),
            'availableLanguage' => 'ja',
        ],
        [
            '@context'   => 'https://schema.org',
            '@type'      => 'WebSite',
            'name'       => 'リモートお助け隊',
            'url'        => absUrl('/'),
            'inLanguage' => 'ja-JP',
            'publisher'  => ['@type' => 'Organization', 'name' => '誠幸（セイコウ）'],
        ],
        [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => [
                [
                    '@type'          => 'Question',
                    'name'           => 'パソコンが苦手でも相談できますか？',
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'はい。専門用語をできるだけ避け、実際の仕事を使って一緒に確認します。何を聞けばよいか分からない段階からご相談いただけます。'],
                ],
                [
                    '@type'          => 'Question',
                    'name'           => 'AIだけでなく、Excelやメールの相談もできますか？',
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'はい。Excel、メール、書類づくり、AIなど、毎日の業務を少しラクにするための困りごとに合わせて優先順位をご提案します。'],
                ],
                [
                    '@type'          => 'Question',
                    'name'           => 'オンラインだけで支援できますか？',
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'はい。画面共有を活用し、日々の実務に即した支援が可能です。必要に応じて訪問対応もご相談いただけます。'],
                ],
            ],
        ],
    ],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>

    <main id="main-content">
      <section class="hero section-wrap">
        <div class="hero-copy">
          <p class="eyebrow"><span></span> Saitama / PC &amp; AI Support</p>
          <h1>社長の<br /><em>「これ、どうにか<br />ならない？」</em><br />を、ラクに。</h1>
          <p class="hero-text">パソコン、Excel、メール、業務ソフト、生成AI。何から相談すればいいかわからない困りごとを、ひとつずつ整理します。さいたま市の中小企業に寄り添う、頼れる相談相手です。</p>
          <div class="hero-actions"><a class="button primary" href="#contact">無料で相談する <span>→</span></a><a class="text-link" href="#service">できることを見る <span>↓</span></a></div>
          <div class="trust-row"><div><strong>01</strong><span>困りごとを無料で整理</span></div><div><strong>02</strong><span>小さく試して解決</span></div><div><strong>03</strong><span>使えるまでサポート</span></div></div>
        </div>
        <div class="hero-art hero-photo">
          <picture>
            <source media="(max-width: 480px)" srcset="assets/hero-consultation-sp-1055.webp" type="image/webp" />
            <source media="(max-width: 800px)" srcset="assets/hero-consultation-1280.webp" type="image/webp" />
            <source srcset="assets/hero-consultation-pc-1303.webp" type="image/webp" />
            <img src="assets/hero-consultation.png" alt="ノートパソコンを囲んで相談する中小企業の経営者とサポート担当者" width="1672" height="941" fetchpriority="high" decoding="async" />
          </picture>
        </div>
      </section>

      <section class="problem-strip"><div class="section-wrap problem-grid"><p class="eyebrow light">DOES THIS SOUND FAMILIAR?</p><h2>パソコンやAIのこと、<br /><em>誰に聞けばいいか分からない。</em></h2><div class="problem-list"><p><span>01</span>Excelやメールに時間を取られている</p><p><span>02</span>AIが役立つと聞くが、何から試すか分からない</p><p><span>03</span>業者に頼むほどか、判断がつかない</p></div></div></section>

      <section id="service" class="services section-wrap section-pad">
        <div class="section-heading reveal"><p class="eyebrow"><span></span> WHAT WE DO</p><h2>難しいことは抜きにして、<br /><strong>困ったところから。</strong></h2><p>流行のAIや高価な仕組みを押しつけません。御社の現場で「本当に時間を奪っていること」から、一緒に解決します。</p></div>
        <div class="service-grid">
          <article class="service-card dark-card reveal"><p class="card-number">01 / SORT OUT</p><div class="service-icon">⌘</div><h3>まずは話を聞く</h3><p>「何に困っているか」を一緒に整理。パソコンや業務のつまずきを見える化して、先に片づけることを決めます。</p><ul><li>パソコン・業務の相談</li><li>改善の優先順位づけ</li></ul></article>
          <article class="service-card mint-card reveal delay-1"><p class="card-number">02 / FIX &amp; SET UP</p><div class="service-icon">✦</div><h3>仕事をラクにする</h3><p>Excelの整備、メールや書類づくりの効率化、AIの設定まで。御社で使いやすい形に、手順書と一緒に整えます。</p><ul><li>Excel・書類作業の改善</li><li>AI・業務ツールの設定</li></ul></article>
          <article class="service-card sand-card reveal delay-2"><p class="card-number">03 / STAY WITH YOU</p><div class="service-icon">↗</div><h3>使えるまで手伝う</h3><p>設定して終わりではありません。オンライン相談や必要に応じた訪問で、スタッフが安心して使えるまで支えます。</p><ul><li>少人数・実践型のレクチャー</li><li>定例の困りごと相談</li></ul></article>
        </div>
      </section>

      <section id="readiness" class="readiness section-wrap section-pad"><div class="readiness-box reveal"><div class="readiness-intro"><p class="eyebrow"><span></span> 3-MINUTE SELF CHECK</p><h2>いまの困りごとを、<br /><em>3分で整理。</em></h2><p>いきなりツールを選ぶ必要はありません。現場の状況を少し整理して、相談の出発点をつくりましょう。診断結果は送信されません。</p></div><form id="readinessForm" class="readiness-form"><fieldset><legend><b>01</b> 繰り返し発生する、時間のかかる業務がありますか？</legend><label><input type="radio" name="task" value="2" required /> はい、具体的に思い浮かぶ</label><label><input type="radio" name="task" value="1" /> なんとなくある</label><label><input type="radio" name="task" value="0" /> まだ分からない</label></fieldset><fieldset><legend><b>02</b> その仕事の最終確認をする担当者は決まっていますか？</legend><label><input type="radio" name="owner" value="2" required /> 決まっている</label><label><input type="radio" name="owner" value="1" /> 一部は決まっている</label><label><input type="radio" name="owner" value="0" /> まだ決まっていない</label></fieldset><fieldset><legend><b>03</b> パソコンやAIについて、気軽に相談できる相手はいますか？</legend><label><input type="radio" name="rule" value="2" required /> すでにいる</label><label><input type="radio" name="rule" value="1" /> 一部は相談できる</label><label><input type="radio" name="rule" value="0" /> 相談相手がほしい</label></fieldset><button class="button primary" type="submit">整理結果を見る <span>→</span></button><p id="readinessResult" class="readiness-result" role="status" aria-live="polite"></p></form></div></section>

      <section id="cases" class="cases"><div class="section-wrap section-pad"><div class="case-header"><div class="reveal"><p class="eyebrow"><span></span> USE CASES BY INDUSTRY</p><h2>現場に合わせた、<br /><em>無理のない使い方。</em></h2></div><p>派手なDXより、毎日の仕事が少しラクになること。その積み重ねが、事業を強くします。</p></div>
        <div class="case-tabs" role="tablist" aria-label="業種別のAI活用イメージ"><button id="tab-builder" type="button" class="active" data-case="builder" role="tab" aria-selected="true" aria-controls="casePanel" tabindex="0">工務店</button><button id="tab-accounting" type="button" data-case="accounting" role="tab" aria-selected="false" aria-controls="casePanel" tabindex="-1">会計事務所</button><button id="tab-retail" type="button" data-case="retail" role="tab" aria-selected="false" aria-controls="casePanel" tabindex="-1">小売店</button></div>
        <article class="case-panel" id="casePanel"><div class="case-visual"><p class="visual-tag">USE CASE / <span id="caseNo">01</span></p><div class="blueprint"><span class="bp-line l1"></span><span class="bp-line l2"></span><span class="bp-line l3"></span><span class="bp-square s1"></span><span class="bp-square s2"></span><b>AI</b></div><p class="visual-caption" id="caseVisualText">見積作成を、<br />もっと早く、正確に。</p></div><div class="case-content"><p class="case-type" id="caseType">工務店 / 活用イメージ</p><h3 id="caseTitle">見積書のたたき台を<br />AIで<span>効率化</span>。</h3><p id="caseDesc">過去の見積データをもとに、提案文と概算見積の下書きを生成。職人さんは判断とお客様対応に集中しやすくなります。</p><div class="case-result"><div><strong id="caseMetric">STEP</strong><span id="caseMetricLabel">まずは1業務から<br />小さく試す</span></div><blockquote id="caseQuote">期待値と安全な使い方を共有してから、実際の業務で試します。</blockquote></div></div></article>
      </div></section>

      <section class="process section-wrap section-pad"><div class="process-intro reveal"><p class="eyebrow"><span></span> HOW WE WORK</p><h2>相談から、<br />毎日の<strong>「ラクになった」</strong>まで。</h2></div><ol class="timeline"><li class="reveal"><span>01</span><div><h3>無料相談</h3><p>60分のオンライン対話で、いま困っていることを整理します。</p></div><b>WEEK 0</b></li><li class="reveal delay-1"><span>02</span><div><h3>やることを決める</h3><p>一番効果が出やすい業務を選び、無理のない進め方をご提案します。</p></div><b>WEEK 1</b></li><li class="reveal delay-2"><span>03</span><div><h3>一緒に整える</h3><p>パソコン・Excel・AIを、実際の仕事を題材に整えていきます。</p></div><b>WEEK 2–6</b></li><li class="reveal delay-3"><span>04</span><div><h3>困った時も相談</h3><p>使い始めてから出る疑問にも対応し、次の改善につなげます。</p></div><b>ONGOING</b></li></ol>
      </section>

      <section id="pricing" class="pricing section-pad"><div class="section-wrap"><div class="pricing-heading reveal"><p class="eyebrow light"><span></span> PLANS</p><h2>背伸びしない、<br />頼みやすいプラン。</h2><p>「何を頼めばいいか分からない」段階でも大丈夫です。まずはご相談ください。</p></div><div class="price-grid"><article class="price-card reveal"><p class="plan-label">FIRST STEP</p><h3>困りごと相談</h3><p class="price"><small>初回</small> 無料</p><p>パソコン・Excel・AIで困っていることを整理する、60分のオンライン相談です。</p><a href="#contact">無料で相談する <span>→</span></a></article><article class="price-card featured reveal delay-1"><p class="plan-label">RECOMMENDED</p><h3>業務ラクラク整備</h3><p class="price"><small>月額</small> 55,000<small>円〜</small></p><p>業務整理、パソコン・AIの設定、少人数レクチャー、月次相談をまとめて支援します。</p><a href="#contact">プランを相談する <span>→</span></a></article><article class="price-card reveal delay-2"><p class="plan-label">TEAM GROWTH</p><h3>いつでも相談サポート</h3><p class="price"><small>月額</small> 33,000<small>円〜</small></p><p>導入後の疑問を解消しながら、使える仕事の幅を少しずつ広げます。</p><a href="#contact">詳細を聞く <span>→</span></a></article></div></div></section>

      <section class="guide-promo section-wrap section-pad"><div class="guide-card reveal"><div><p class="eyebrow"><span></span> FREE GUIDE</p><h2>AIを仕事で使う前に<br />押さえたい5ステップ。</h2><p>ツール選びの前に決めること、情報管理の考え方、社内への伝え方を、読みやすくまとめました。</p></div><a class="button primary" href="guide.php">AI活用ガイドを読む <span>→</span></a></div></section>

      <section id="faq" class="faq section-wrap section-pad"><div class="faq-title"><p class="eyebrow"><span></span> FAQ</p><h2>よくある質問</h2></div><div class="faq-list"><details><summary>パソコンが苦手でも相談できますか？ <span>+</span></summary><p>もちろんです。専門用語をできるだけ避け、実際の仕事を使って一緒に確認します。「何を聞けばいいか分からない」段階からご相談いただけます。</p></details><details><summary>AIだけでなく、Excelやメールの相談もできますか？ <span>+</span></summary><p>はい。毎日の業務を少しラクにすることが出発点です。Excel、メール、書類づくり、AIなど、困りごとに合わせて優先順位をご提案します。</p></details><details><summary>オンラインだけで支援できますか？ <span>+</span></summary><p>はい。画面共有を活用し、日々の実務に即した支援が可能です。必要に応じて、さいたま市内での訪問対応もご相談いただけます。</p></details></div></section>

      <section id="contact" class="contact"><div class="section-wrap contact-inner"><div><p class="eyebrow light"><span></span> LET'S START SMALL</p><h2>まずは、御社の<br /><em>「困った」</em>を<br />聞かせてください。</h2><p>無理な営業はしません。パソコン・Excel・AIで何がラクになるかを一緒に考える、60分の無料相談です。</p></div><form class="contact-form" id="contactForm" action="contact.php" method="post"><label>会社名<input name="company" type="text" autocomplete="organization" placeholder="例）株式会社さいたま商店" required maxlength="120" /></label><label>お名前<input name="name" type="text" autocomplete="name" placeholder="例）山田 太郎" required maxlength="80" /></label><label>メールアドレス<input name="email" type="email" autocomplete="email" placeholder="example@company.jp" required maxlength="254" /></label><label>いちばん近いお悩み<select name="concern" required><option value="">選択してください</option><option>パソコン・Excelをラクにしたい</option><option>AIを仕事に使ってみたい</option><option>何から相談すればよいか知りたい</option><option>その他</option></select></label><label class="honeypot" aria-hidden="true">Webサイト<input name="website" type="text" tabindex="-1" autocomplete="off" /></label><button class="button primary" type="submit">無料相談を予約する <span>→</span></button><p class="form-note">送信により、<a href="privacy.php">プライバシーポリシー</a>に同意したものとみなします。</p><p class="form-success" id="formSuccess" role="status" aria-live="polite"></p></form></div></section>
    </main>
<?php require __DIR__ . '/partials/footer.php'; ?>
