/**
 * リモートお助け隊 - メインスクリプト
 *
 * 構成:
 * - 定数定義 (CONFIG, CASE_DATA)
 * - DOM要素の参照
 * - 業種別ケース切り替え機能
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
  READINESS_FIELDS: ['task', 'owner', 'rule']
};

/**
 * 業種別ケースデータ
 * 各ケースには番号、タイプ、タイトル、説明、メトリクス、引用、ビジュアルテキストを含む
 */
const CASE_DATA = {
  builder: {
    no: '01',
    type: '工務店 / 活用イメージ',
    title: '見積書のたたき台を<br>AIで<span>効率化</span>。',
    desc: '過去の見積データをもとに、提案文と概算見積の下書きを生成。職人さんは判断とお客様対応に集中しやすくなります。',
    metric: 'STEP',
    metricLabel: 'まずは1業務から<br>小さく試す',
    quote: '期待値と安全な使い方を共有してから、実際の業務で試します。',
    visual: '見積作成を、<br>もっと早く、正確に。'
  },
  accounting: {
    no: '02',
    type: '会計事務所 / 活用イメージ',
    title: '面談メモからの<br>提案準備を<span>整理</span>。',
    desc: 'ヒアリング内容の整理と提案書の構成づくりをAIが補助。担当者ごとの経験値に左右されにくい、お客様対応の土台を整えます。',
    metric: 'CHECK',
    metricLabel: '専門家の確認を<br>必ず組み込む',
    quote: 'AIの出力をそのまま使わず、専門性が必要な判断は必ず人が行います。',
    visual: '数字の整理を、<br>もっとお客様のために。'
  },
  retail: {
    no: '03',
    type: '小売店 / 活用イメージ',
    title: 'SNS投稿づくりを<br>もっと<span>続けやすく</span>。',
    desc: '商品写真と季節の話題から、店らしい発信文の下書きをAIと一緒に作成。投稿のハードルを下げ、来店前の接点づくりを支えます。',
    metric: 'REVIEW',
    metricLabel: '公開前に内容を<br>店主が確認',
    quote: 'お店の言葉らしさと、正しい商品情報は人が最後に確認します。',
    visual: 'お店の魅力を、<br>もっと自然に届ける。'
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
  contactForm: document.querySelector('#contactForm'),
  formSuccess: document.querySelector('#formSuccess'),
  readinessForm: document.querySelector('#readinessForm'),
  readinessResult: document.querySelector('#readinessResult')
};

// =============================================================================
// 業種別ケース切り替え機能
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
    console.warn(`Unknown case: ${button.dataset.case}`);
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
// お問い合わせフォーム送信
// =============================================================================

/**
 * お問い合わせフォームの送信処理
 * 非同期で送信し、結果をユーザーにフィードバック
 */
function initContactForm() {
  if (!elements.contactForm) return;

  elements.contactForm.addEventListener('submit', async event => {
    event.preventDefault();

    const form = event.currentTarget;
    const submitButton = form.querySelector('button');
    const originalButtonText = submitButton.innerHTML;

    // UIを送信状態に
    elements.formSuccess.classList.remove('show');
    submitButton.disabled = true;
    submitButton.textContent = '送信しています…';

    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { Accept: 'application/json' }
      });

      const result = await response.json();

      if (!response.ok || !result.ok) {
        throw new Error(result.message || '送信に失敗しました。');
      }

      // 成功時
      elements.formSuccess.textContent = result.message;
      elements.formSuccess.classList.add('show');
      form.reset();

    } catch (error) {
      // エラー時 - ユーザーに分かりやすいメッセージを
      elements.formSuccess.textContent = error.message || '送信できませんでした。時間をおいて再度お試しください。';
      elements.formSuccess.classList.add('show');

    } finally {
      // UIを元に戻す
      submitButton.disabled = false;
      submitButton.innerHTML = originalButtonText;
    }
  });
}

// =============================================================================
// 準備度診断フォーム
// =============================================================================

/**
 * 準備度スコアに基づいてメッセージを生成
 * @param {number} score - 合計スコア (0-6)
 * @returns {string} ユーザーに表示するメッセージHTML
 */
function getReadinessMessage(score) {
  const { LOW, MEDIUM } = CONFIG.READINESS_SCORE;

  if (score <= LOW) {
    return '<strong>いまは「困りごとの整理」から始める段階です。</strong> まずは時間がかかる仕事を一つ選び、誰に相談できると安心かを考えてみましょう。';
  }

  if (score <= MEDIUM) {
    return '<strong>小さな改善を始めやすい状態です。</strong> 一業務・少人数で試し、便利さと確認の手間を一緒に記録するのがおすすめです。';
  }

  return '<strong>仕事をラクにする準備ができています。</strong> パソコン・AIの使い方を整え、チームで同じように使える形にしていきましょう。';
}

/**
 * 準備度診断フォームの送信処理
 * 各質問の回答を合計し、段階に応じたメッセージを表示
 */
function initReadinessForm() {
  if (!elements.readinessForm) return;

  elements.readinessForm.addEventListener('submit', event => {
    event.preventDefault();

    const form = event.currentTarget;

    // 各フィールドの選択値を合計
    const score = CONFIG.READINESS_FIELDS.reduce((total, fieldName) => {
      const checkedInput = form.querySelector(`input[name="${fieldName}"]:checked`);
      return total + Number(checkedInput.value);
    }, 0);

    elements.readinessResult.innerHTML = getReadinessMessage(score);
    elements.readinessResult.classList.add('show');
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
      link.closest('details').removeAttribute('open');
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
  initContactForm();
  initReadinessForm();
  initMobileNav();
});