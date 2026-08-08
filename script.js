/**
 * デジタル担当室 - メインスクリプト
 *
 * 構成:
 * - 定数定義 (CONFIG, CASE_DATA)
 * - DOM要素の参照
 * - 分野別ケース切り替え機能
 * - スクロールアニメーション
 * - お問い合わせフォーム送信
 * - 準備度診断フォーム
 * - モバイルナビゲーション
 */

// =============================================================================
// 定数定義 - マジックナンバーを排除し、意図を明確にする
// =============================================================================

const CONFIG = {
  // アニメーション
  FADE_DURATION_MS: 160,
  INTERSECTION_THRESHOLD: 0.14,

  // 準備度診断のスコア閾値
  READINESS_SCORE: {
    LOW: 2,    // 困りごとの整理から始める段階
    MEDIUM: 4  // 小さな改善を始めやすい状態
  },

  // フォームフィールド名
  READINESS_FIELDS: ['q1', 'q2', 'q3'],

  // スコア変換マップ (a:2点, b:1点, c:0点)
  SCORE_MAP: {
    'a': 2,
    'b': 1,
    'c': 0
  }
};

/**
 * 分野別ケースデータ
 * 各ケースには番号、タイプ、タイトル、説明、メトリクス、引用、ビジュアルテキストを含む
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
    visual: '<span class="nobr">直したい場所だけ、</span><br class="pc-only"><span class="nobr">すぐ直せる状態に。</span>'
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
    visual: '<span class="nobr">開店も運用も、</span><br class="pc-only"><span class="nobr">つまずかずに進む。</span>'
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
    visual: '<span class="nobr">定型作業を、</span><br class="pc-only"><span class="nobr">もっと軽くする。</span>'
  }
};

// =============================================================================
// DOM要素の参照 - 一度だけ取得し、再利用する
// =============================================================================

const elements = {
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
  caseBadge: document.querySelector('#caseBadge'),
  readinessForm: document.querySelector('#readinessForm'),
  readinessResult: document.querySelector('#result-area'),
  readinessText: document.querySelector('#result-text'),
  contactForm: document.querySelector('.contact-form')
};

// =============================================================================
// 分野別ケース切り替え機能
// =============================================================================

/**
 * ケースパネルの初期化
 * アクセシビリティ属性を設定
 */
function initCasePanel() {
  elements.casePanel.setAttribute('role', 'tabpanel');
  elements.casePanel.tabIndex = 0;
  elements.casePanel.style.transition = `opacity ${CONFIG.FADE_DURATION_MS}ms ease`;
}

/**
 * ケースデータをDOMに反映
 * @param {Object} data - CASE_DATAのいずれかのオブジェクト
 */
function updateCaseContent(data) {
  elements.caseNo.textContent = data.no;
  elements.caseType.textContent = data.type;
  elements.caseTitle.innerHTML = data.title;
  elements.caseDesc.textContent = data.desc;
  elements.caseMetric.textContent = data.metric;
  elements.caseMetricLabel.innerHTML = data.metricLabel;
  elements.caseQuote.textContent = data.quote;
  elements.caseVisualText.innerHTML = data.visual;
  elements.caseBadge.textContent = data.badge;
}

/**
 * タブの選択状態を更新
 * @param {HTMLElement} activeButton - 選択されたタブボタン
 */
function updateTabSelection(activeButton) {
  elements.caseTabs.forEach(tab => {
    const isSelected = tab === activeButton;
    tab.classList.toggle('active', isSelected);
    tab.setAttribute('aria-selected', String(isSelected));
    tab.tabIndex = isSelected ? 0 : -1;
  });
}

/**
 * ケースをアクティブにする
 * フェードアウト → 内容更新 → フェードイン
 * @param {HTMLElement} button - クリックされたタブボタン
 */
function activateCase(button) {
  const data = CASE_DATA[button.dataset.case];

  if (!data) {
    console.error(`Missing case data for: ${button.dataset.case}`);
    return;
  }

  updateTabSelection(button);
  elements.casePanel.setAttribute('aria-labelledby', button.id);

  // フェードアウト → 内容更新 → フェードイン
  elements.casePanel.style.opacity = '0';

  setTimeout(() => {
    updateCaseContent(data);
    elements.casePanel.style.opacity = '1';
  }, CONFIG.FADE_DURATION_MS);
}

/**
 * キーボードナビゲーション処理
 * 矢印キー、Home、Endキーでタブ間を移動
 * @param {KeyboardEvent} event
 * @param {number} currentIndex - 現在のタブインデックス
 */
function handleTabKeyNavigation(event, currentIndex) {
  const tabCount = elements.caseTabs.length;
  let nextIndex;

  switch (event.key) {
    case 'ArrowRight':
      nextIndex = (currentIndex + 1) % tabCount;
      break;
    case 'ArrowLeft':
      nextIndex = (currentIndex - 1 + tabCount) % tabCount;
      break;
    case 'Home':
      nextIndex = 0;
      break;
    case 'End':
      nextIndex = tabCount - 1;
      break;
    default:
      return; // 対象外のキーは無視
  }

  event.preventDefault();
  elements.caseTabs[nextIndex].focus();
  activateCase(elements.caseTabs[nextIndex]);
}

/**
 * ケースタブのイベントリスナーを設定
 */
function initCaseTabs() {
  elements.caseTabs.forEach((button, index) => {
    button.addEventListener('click', () => activateCase(button));
    button.addEventListener('keydown', event => handleTabKeyNavigation(event, index));
  });
}

// =============================================================================
// スクロールアニメーション (Intersection Observer)
// =============================================================================

/**
 * スクロール時のフェードインアニメーションを初期化
 * .revealクラスを持つ要素がビューポートに入ったら表示
 */
function initScrollAnimations() {
  const observer = new IntersectionObserver(
    entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target); // 一度表示したら監視解除
        }
      });
    },
    { threshold: CONFIG.INTERSECTION_THRESHOLD }
  );

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
}


// =============================================================================
// 準備度診断フォーム
// =============================================================================


/**
 * 各質問の回答を合計し、段階に応じたメッセージを表示
 */
function initReadinessForm() {
  const form = elements.readinessForm;
  if (!form) return;

  form.addEventListener('submit', (event) => {
    event.preventDefault();

    // q1, q2, q3 それぞれのチェックされている入力を取得
    const q1 = form.querySelector('input[name="q1"]:checked');
    const q2 = form.querySelector('input[name="q2"]:checked');
    const q3 = form.querySelector('input[name="q3"]:checked');

    // すべて回答されているか確認（HTMLのrequired属性でも担保されるが念のため）
    if (!q1 || !q2 || !q3) return;

    // 合計スコアの計算 (a=2, b=1, c=0)
    const score = CONFIG.SCORE_MAP[q1.value] + 
                  CONFIG.SCORE_MAP[q2.value] + 
                  CONFIG.SCORE_MAP[q3.value];

    // 結果メッセージの決定 (if...else if を使用)
    let message = '';
    const { LOW, MEDIUM } = CONFIG.READINESS_SCORE;

    if (score <= LOW) {
      message = '<strong>いまは「何に困っているか」の整理から始める段階です。</strong> まとまっていなくて構いません。止まっていることをそのまま送っていただければ、一緒に切り分けます。';
    } else if (score <= MEDIUM) {
      message = '<strong>スポット対応から始めやすい状態です。</strong> 直したい箇所や設定を一つ選んで小さく片づけると、次にやることが見えやすくなります。';
    } else {
      message = '<strong>改善・導入を進めやすい状態です。</strong> WEB・EC・AIのどこから着手すると効果が出やすいか、進め方と費用を整理してご提案します。';
    }

    // 結果の表示とアニメーション
    elements.readinessText.innerHTML = message;
    
    // 一度非表示にしてからクラスを付与することで、アニメーションを再トリガーしやすくする
    elements.readinessResult.classList.remove('is-active');
    
    // ブラウザのリフローを強制してアニメーションを適用
    void elements.readinessResult.offsetWidth;
    
    elements.readinessResult.classList.add('is-active');
    
    // スムーズに結果エリアへスクロール
    elements.readinessResult.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  });
}

// =============================================================================
// お問い合わせフォーム送信
// =============================================================================

/**
 * 相談内容を同一サーバーの contact.php へ非同期でPOSTする
 * ページ遷移を伴わず、同じ画面で結果を返す
 */
function initContactForm() {
  const form = elements.contactForm;
  if (!form) return;

  const button = form.querySelector('button[type="submit"]');
  const status = form.querySelector('.form-success');

  const FALLBACK_ERROR = '送信できませんでした。お手数ですが、時間をおいて再度お試しください。';

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    button.disabled = true;
    status.classList.remove('show', 'is-error');

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'Accept': 'application/json' }
      });

      // 入力不備などはサーバー側のメッセージをそのまま利用者に見せる
      const result = await response.json().catch(() => ({}));

      status.textContent = result.message || (response.ok ? '送信しました。' : FALLBACK_ERROR);

      if (response.ok) {
        form.reset();
      } else {
        status.classList.add('is-error');
      }
    } catch (error) {
      // 通信自体に失敗した場合 (オフライン等)
      console.error(error);
      status.textContent = FALLBACK_ERROR;
      status.classList.add('is-error');
    } finally {
      status.classList.add('show');
      button.disabled = false;
    }
  });
}

// =============================================================================
// モバイルナビゲーション
// =============================================================================

/**
 * モバイルナビゲーションのリンククリック時にメニューを閉じる
 */
function initMobileNav() {
  document.querySelectorAll('.mobile-nav a').forEach(link => {
    link.addEventListener('click', () => {
      const details = link.closest('details');
      if (details) {
        details.removeAttribute('open');
      }
    });
  });
}

// =============================================================================
// 初期化
// =============================================================================

document.addEventListener('DOMContentLoaded', () => {
  initCasePanel();
  initCaseTabs();
  initScrollAnimations();
  initReadinessForm();
  initContactForm();
  initMobileNav();
});