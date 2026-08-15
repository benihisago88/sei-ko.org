/**
 * デジタル担当室 - メインスクリプト
 *
 * 構成:
 * - 動作設定 (APP_CONFIG)
 * - 分野別ケースデータ (CASE_DATA)
 * - DOM要素の集約参照 (uiElements)
 * - 各機能の初期化ルーチン
 */

// =============================================================================
// 動作設定
// =============================================================================

const APP_CONFIG = {
  // 視覚的なフィードバックの速度
  FADE_DURATION_MS: 160,
  
  // スクロール時に要素を表示する閾値（14%見えたら表示）
  INTERSECTION_THRESHOLD: 0.14,

  // 準備度診断のスコア判定基準
  // 3つの質問（各0〜2点）の合計点に基づき、事業主のデジタル準備状態を分類する
  READINESS_LEVELS: {
    START_ORGANIZING: 2, // 困りごとの整理から始める段階
    SMALL_START: 4       // スポット対応から着手できる段階
  },

  // フォームの選択肢とスコアの紐付け
  // a:前向き・具体性あり(2), b:検討中(1), c:不明・未着手(0)
  ANSWER_SCORE_WEIGHTS: {
    'a': 2,
    'b': 1,
    'c': 0
  }
};

// =============================================================================
// 分野別ケースデータ
// =============================================================================

/**
 * WEB / EC / AI の各サポート事例データ
 * index.html の活用事例セクションで動的に切り替えて表示する。
 * 図解 (figure) はインラインSVGを直接保持し、CSP違反を避けつつ動的更新を可能にしている。
 */
const CASE_DATA = {
  web: {
    no: '01',
    type: 'WEB制作・改善 / 活用イメージ',
    title: '<span class="nobr">フォームやページを</span><br class="pc-only"><span class="nobr">まず<span>部分修正</span>。</span>',
    desc: '「問い合わせフォームが動かない」「この一文だけ直したい」といった小さな依頼から着手。WordPressやドメインまわりも含めて、触れる状態に整えます。',
    badge: 'WEB',
    metric: 'SPOT',
    metricLabel: '<span class="nobr">必要な箇所だけ</span><br class="pc-only"><span class="nobr">小さく直す</span>',
    quote: '全面リニューアルありきではなく、いま困っている箇所から手をつけます。',
    visual: '<span class="nobr">直したい場所だけ、</span><br class="pc-only"><span class="nobr">すぐ直せる状態に。</span>',
    figure: '<svg viewBox="0 0 320 240" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="43" y="47" width="234" height="146" rx="8" /><path d="M43 79h234M62 63h.5M75 63h.5M88 63h.5" /><path d="M69 105h69M69 122h48M69 139h55" /><rect x="154" y="101" width="82" height="55" rx="5" /><path d="M178 169l14-17 11 10 10-27 14 22" /><path d="M197 109l18 18-9 2-4 10-5-5-8 8z" /></svg>'
  },
  ec: {
    no: '02',
    type: 'Shopify・EC / 活用イメージ',
    title: '<span class="nobr">売る準備と運用を</span><br class="pc-only"><span class="nobr">まとめて<span>整える</span>。</span>',
    desc: '初期設定、商品登録、決済・配送、テーマ設定まで。「送料設定だけ分からない」といったピンポイントの相談にも対応し、日々の運用が回る形にします。',
    badge: 'EC',
    metric: 'SETUP',
    metricLabel: '<span class="nobr">開店準備から</span><br class="pc-only"><span class="nobr">日々の運用まで</span>',
    quote: '止まっている設定を先に片づけてから、売り方の改善に進みます。',
    visual: '<span class="nobr">開店も運用も、</span><br class="pc-only"><span class="nobr">つまずかずに進む。</span>',
    figure: '<svg viewBox="0 0 320 240" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M48 123h48M116 123h38M202 123h38" /><path d="m96 115 8 8-8 8M192 115l8 8-8 8M278 115l8 8-8 8" /><rect x="28" y="91" width="40" height="40" rx="5" /><path d="m37 91 11-14 11 14M38 110h20" /><path d="M124 103h42l8 28h-58zM135 103l5-12h10l5 12" /><rect x="210" y="98" width="42" height="30" rx="4" /><path d="M218 109h26M218 119h12" /><path d="m271 112 10 10 19-22" /></svg>'
  },
  ai: {
    no: '03',
    type: 'AI・業務効率化 / 活用イメージ',
    title: '<span class="nobr">毎日の手作業を</span><br class="pc-only"><span class="nobr">AIで<span>減らす</span>。</span>',
    desc: 'ChatGPTでの文章づくり、スプレッドシートの整理、定型業務の見直し、簡易的な自動化まで。問い合わせ対応など、時間を取られている作業から見直します。',
    badge: 'AI',
    metric: 'REVIEW',
    metricLabel: '<span class="nobr">最終確認は</span><br class="pc-only"><span class="nobr">必ず人が行う</span>',
    quote: 'AIの出力をそのまま使わず、判断が必要な部分は人が確認します。',
    visual: '<span class="nobr">定型作業を、</span><br class="pc-only"><span class="nobr">もっと軽くする。</span>',
    figure: '<svg viewBox="0 0 320 240" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="40" y="73" width="58" height="24" rx="4" /><rect x="40" y="108" width="58" height="24" rx="4" /><rect x="40" y="143" width="58" height="24" rx="4" /><path d="M112 85h34M112 120h34M112 155h34M146 85l25 35-25 35zM171 120h36" /><circle cx="234" cy="91" r="20" /><path d="M202 166c5-25 19-37 32-37s27 12 32 37M224 91l7 7 12-14" /><path d="m267 161 8 8 15-18" /></svg>'
  }
};

// =============================================================================
// DOM要素の集約参照
// =============================================================================

/**
 * 頻繁にアクセスするDOM要素をキャッシュし、パフォーマンスを向上させる。
 * また、要素の取得を1箇所にまとめることでメンテナンス性を高めている。
 */
const uiElements = {
  // 事例切り替え用パネル
  casePanel: document.querySelector('#casePanel'),
  caseTabs: [...document.querySelectorAll('[data-case]')],
  caseNo: document.querySelector('#caseNo'),
  caseType: document.querySelector('#caseType'),
  caseTitle: document.querySelector('#caseTitle'),
  caseDesc: document.querySelector('#caseDesc'),
  caseMetric: document.querySelector('#caseMetric'),
  caseMetricLabel: document.querySelector('#caseMetricLabel'),
  caseQuote: document.querySelector('#caseQuote'),
  caseVisualText: document.querySelector('#caseVisualText'),
  caseFigure: document.querySelector('#caseFigure'),
  caseBadge: document.querySelector('#caseBadge'),
  
  // 準備度診断フォーム
  readinessForm: document.querySelector('#readinessForm'),
  readinessResultArea: document.querySelector('#result-area'),
  readinessResultText: document.querySelector('#result-text'),
  
  // お問い合わせフォーム
  contactForm: document.querySelector('.contact-form'),
  
  // モバイルナビゲーション
  mobileNavLinks: document.querySelectorAll('.mobile-nav a')
};

// =============================================================================
// 分野別ケース切り替え機能
// =============================================================================

/**
 * 事例パネルの遷移アニメーション設定
 * 視覚的負荷を抑えるため、フェード効果の時間を設定から取得して適用する。
 */
function setupCasePanelTransitions() {
  if (!uiElements.casePanel) return;
  uiElements.casePanel.style.transition = `opacity ${APP_CONFIG.FADE_DURATION_MS}ms ease`;
}

/**
 * 表示されている事例内容を更新する
 * テンプレートエンジンの代わりに innerHTML/textContent を使い分け、
 * 静的サイトとしてのシンプルさを保ちつつ動的な切り替えを実現している。
 */
function applyCaseDataToView(caseData) {
  uiElements.caseNo.textContent = caseData.no;
  uiElements.caseType.textContent = caseData.type;
  uiElements.caseTitle.innerHTML = caseData.title;
  uiElements.caseDesc.textContent = caseData.desc;
  uiElements.caseMetric.textContent = caseData.metric;
  uiElements.caseMetricLabel.innerHTML = caseData.metricLabel;
  uiElements.caseQuote.textContent = caseData.quote;
  uiElements.caseVisualText.innerHTML = caseData.visual;
  uiElements.caseFigure.innerHTML = caseData.figure;
  uiElements.caseBadge.textContent = caseData.badge;
}

/**
 * タブの選択状態（WAI-ARIA属性含む）を更新する
 */
function refreshTabStates(selectedButton) {
  uiElements.caseTabs.forEach(tab => {
    const isSelected = tab === selectedButton;
    tab.classList.toggle('active', isSelected);
    tab.setAttribute('aria-selected', String(isSelected));
    tab.tabIndex = isSelected ? 0 : -1;
  });
}

/**
 * 事例パネルの内容を切り替える
 * 急激な画面変化による不快感を防ぐため、一旦透明にしてから内容を書き換え、再度表示する。
 */
function switchActiveCase(targetButton) {
  const caseKey = targetButton.dataset.case;
  const targetData = CASE_DATA[caseKey];

  if (!targetData) {
    console.error(`Missing data for case: ${caseKey}`);
    return;
  }

  refreshTabStates(targetButton);
  uiElements.casePanel.setAttribute('aria-labelledby', targetButton.id);

  // フェードアウト
  uiElements.casePanel.style.opacity = '0';

  // アニメーション完了後に内容を差し替えてフェードイン
  setTimeout(() => {
    applyCaseDataToView(targetData);
    uiElements.casePanel.style.opacity = '1';
  }, APP_CONFIG.FADE_DURATION_MS);
}

/**
 * キーボード（矢印キーなど）でのタブ移動を処理する
 * W3Cの「Tabs Design Pattern」に準拠し、アクセシビリティを確保している。
 */
function handleTabKeyboardInput(event, currentIndex) {
  const totalTabs = uiElements.caseTabs.length;
  let nextIndex;

  const keyActions = {
    'ArrowRight': () => (currentIndex + 1) % totalTabs,
    'ArrowLeft': () => (currentIndex - 1 + totalTabs) % totalTabs,
    'Home': () => 0,
    'End': () => totalTabs - 1
  };

  const getNextIndex = keyActions[event.key];
  if (!getNextIndex) return;

  event.preventDefault();
  nextIndex = getNextIndex();
  
  const nextTab = uiElements.caseTabs[nextIndex];
  nextTab.focus();
  switchActiveCase(nextTab);
}

/**
 * 事例切り替え機能の初期化
 */
function initializeCaseSwitcher() {
  setupCasePanelTransitions();
  
  uiElements.caseTabs.forEach((button, index) => {
    button.addEventListener('click', () => switchActiveCase(button));
    button.addEventListener('keydown', event => handleTabKeyboardInput(event, index));
  });
}

// =============================================================================
// スクロールアニメーション
// =============================================================================

/**
 * 要素が視界に入ったタイミングで表示するアニメーションを初期化
 * JSが無効な環境でもコンテンツが見えるよう、初期状態はCSS側で制御している。
 */
function initializeRevealAnimations() {
  const revealObserver = new IntersectionObserver(
    entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          // 一度表示した要素は監視を停止し、スクロールの負荷を軽減する
          revealObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: APP_CONFIG.INTERSECTION_THRESHOLD }
  );

  document.querySelectorAll('.reveal').forEach(element => revealObserver.observe(element));
}

// =============================================================================
// 準備度診断フォーム
// =============================================================================

/**
 * 診断結果のメッセージを決定する
 */
function getReadinessMessage(score) {
  const { START_ORGANIZING, SMALL_START } = APP_CONFIG.READINESS_LEVELS;

  if (score <= START_ORGANIZING) {
    return '<strong>いまは「何に困っているか」の整理から始める段階です。</strong> まとまっていなくて構いません。止まっていることをそのまま送っていただければ、一緒に切り分けます。';
  } 
  
  if (score <= SMALL_START) {
    return '<strong>スポット対応から始めやすい状態です。</strong> 直したい箇所や設定を一つ選んで小さく片づけると、次にやることが見えやすくなります。';
  }

  return '<strong>改善・導入を進めやすい状態です。</strong> WEB・EC・AIのどこから着手すると効果が出やすいか、進め方と費用を整理してご提案します。';
}

/**
 * 準備度診断フォームの初期化
 */
function initializeReadinessForm() {
  const form = uiElements.readinessForm;
  if (!form) return;

  form.addEventListener('submit', (event) => {
    event.preventDefault();

    // 回答された選択肢を取得
    const answers = [
      form.querySelector('input[name="q1"]:checked'),
      form.querySelector('input[name="q2"]:checked'),
      form.querySelector('input[name="q3"]:checked')
    ];

    // 全ての質問への回答があるか確認（HTML5のrequired属性をJSでも補完）
    if (answers.some(ans => !ans)) return;

    // スコアの合計算出
    const totalScore = answers.reduce((sum, radio) => {
      return sum + APP_CONFIG.ANSWER_SCORE_WEIGHTS[radio.value];
    }, 0);

    // 結果表示の更新と視覚効果の適用
    uiElements.readinessResultText.innerHTML = getReadinessMessage(totalScore);
    
    // アニメーションを再発火させるため、一旦クラスを剥がしてリフローを強制する
    uiElements.readinessResultArea.classList.remove('is-active');
    void uiElements.readinessResultArea.offsetWidth;
    uiElements.readinessResultArea.classList.add('is-active');
    
    // 診断結果が視界に入るよう自動スクロール
    uiElements.readinessResultArea.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  });
}

// =============================================================================
// お問い合わせフォーム送信
// =============================================================================

/**
 * 問い合わせフォームの非同期送信（AJAX）を初期化
 * fetch API を使用し、ページ遷移なしでフィードバックを提供することでユーザー体験を向上させている。
 */
function initializeContactForm() {
  const form = uiElements.contactForm;
  if (!form) return;

  const submitButton = form.querySelector('button[type="submit"]');
  const statusDisplay = form.querySelector('.form-success');
  const GENERIC_ERROR_MSG = '送信できませんでした。お手数ですが、時間をおいて再度お試しください。';

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    // 多重送信防止
    submitButton.disabled = true;
    statusDisplay.classList.remove('show', 'is-error');

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'Accept': 'application/json' }
      });

      // サーバー側（contact.php）からのJSONメッセージを試行的に取得
      const result = await response.json().catch(() => ({}));

      statusDisplay.textContent = result.message || (response.ok ? '送信しました。' : GENERIC_ERROR_MSG);

      if (response.ok) {
        form.reset();
      } else {
        statusDisplay.classList.add('is-error');
      }
    } catch (error) {
      console.error('Contact form submission error:', error);
      statusDisplay.textContent = GENERIC_ERROR_MSG;
      statusDisplay.classList.add('is-error');
    } finally {
      statusDisplay.classList.add('show');
      submitButton.disabled = false;
    }
  });
}

// =============================================================================
// モバイルナビゲーション
// =============================================================================

/**
 * アンカーリンククリック時に自動でメニューを閉じる
 * モバイル環境で画面遷移感（ページ内移動）を出すための配慮。
 */
function initializeMobileNavBehavior() {
  uiElements.mobileNavLinks.forEach(link => {
    link.addEventListener('click', () => {
      const detailsElement = link.closest('details');
      if (detailsElement) {
        detailsElement.removeAttribute('open');
      }
    });
  });
}

// =============================================================================
// 全体の初期化実行
// =============================================================================

document.addEventListener('DOMContentLoaded', () => {
  initializeCaseSwitcher();
  initializeRevealAnimations();
  initializeReadinessForm();
  initializeContactForm();
  initializeMobileNavBehavior();
});
