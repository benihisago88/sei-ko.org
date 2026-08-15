<?php
/**
 * お問い合わせフォーム処理
 *
 * セキュリティ対策:
 * - CSRF: Origin ヘッダーの検証
 * - スパム: ハニーポットフィールド
 * - レート制限: IPアドレスベース (60秒間隔)
 * - メールヘッダーインジェクション: 改行文字の除去
 */

declare(strict_types=1);

// =============================================================================
// 定数定義 - マジックナンバーを排除
// =============================================================================

/** レート制限の間隔 (秒) */
const RATE_LIMIT_SECONDS = 60;

/** 入力値の最大長 */
const MAX_LENGTH = [
    'company' => 120,
    'name'    => 80,
    'concern' => 120,
];

/** メール送信先 */
const MAIL_TO = 'info@sei-ko.org';
const MAIL_FROM = 'Webフォーム <noreply@sei-ko.org>';
const MAIL_SUBJECT = '【さいたま 社長のパソコン・AIお助け隊】無料相談のお申し込み';

// =============================================================================
// レスポンスヘッダー
// =============================================================================

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

// =============================================================================
// ヘルパー関数
// =============================================================================

/**
 * JSONレスポンスを出力して終了
 *
 * @param int    $status  HTTPステータスコード
 * @param bool   $ok      成功フラグ
 * @param string $message ユーザー向けメッセージ
 */
function respond(int $status, bool $ok, string $message): never
{
    http_response_code($status);
    echo json_encode(['ok' => $ok, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Origin ヘッダーを検証してCSRFを防止
 *
 * @return bool 有効なOriginの場合true
 */
function isValidOrigin(): bool
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $host = $_SERVER['HTTP_HOST'] ?? '';

    // Originヘッダーがない場合はスキップ (同一オリジンからのリクエスト)
    if ($origin === '') {
        return true;
    }

    return parse_url($origin, PHP_URL_HOST) === $host;
}

/**
 * レート制限をチェック
 *
 * @param string $clientAddress クライアントIPアドレス
 * @return bool 制限内の場合true
 */
function isWithinRateLimit(string $clientAddress): bool
{
    $rateLimitFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'saitama-ai-contact-' . hash('sha256', $clientAddress);

    if (!is_file($rateLimitFile)) {
        return true;
    }

    $lastSubmission = (int) @file_get_contents($rateLimitFile);
    return (time() - $lastSubmission) >= RATE_LIMIT_SECONDS;
}

/**
 * レート制限のタイムスタンプを更新
 *
 * @param string $clientAddress クライアントIPアドレス
 */
function updateRateLimitTimestamp(string $clientAddress): void
{
    $rateLimitFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'saitama-ai-contact-' . hash('sha256', $clientAddress);
    @file_put_contents($rateLimitFile, (string) time(), LOCK_EX);
}

/**
 * 入力値を検証
 *
 * @param array $data POSTデータ
 * @return string|null エラーメッセージ (問題なければnull)
 */
function validateInput(array $data): ?string
{
    $company = trim((string) ($data['company'] ?? ''));
    $name = trim((string) ($data['name'] ?? ''));
    $email = trim((string) ($data['email'] ?? ''));
    $concern = trim((string) ($data['concern'] ?? ''));

    // 必須フィールドのチェック
    if ($company === '' || $name === '' || $concern === '') {
        return '入力内容をご確認ください。';
    }

    // メールアドレスの形式チェック
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return '入力内容をご確認ください。';
    }

    // 最大長のチェック
    if (mb_strlen($company) > MAX_LENGTH['company']) {
        return '入力内容が長すぎます。';
    }
    if (mb_strlen($name) > MAX_LENGTH['name']) {
        return '入力内容が長すぎます。';
    }
    if (mb_strlen($concern) > MAX_LENGTH['concern']) {
        return '入力内容が長すぎます。';
    }

    return null;
}

/**
 * メール本文を作成
 *
 * @param array $data 検証済みのPOSTデータ
 * @return string メール本文
 */
function createMailBody(array $data): string
{
    $company = trim((string) $data['company']);
    $name = trim((string) $data['name']);
    $email = trim((string) $data['email']);
    $concern = trim((string) $data['concern']);

    return "会社名: {$company}\n"
         . "お名前: {$name}\n"
         . "メールアドレス: {$email}\n"
         . "お悩み: {$concern}\n\n"
         . "送信日時: " . gmdate('c');
}

/**
 * メールヘッダーを作成
 *
 * @param string $replyTo 返信先メールアドレス
 * @return array ヘッダー配列
 */
function createMailHeaders(string $replyTo): array
{
    // メールヘッダーインジェクション対策: 改行文字を除去
    $safeReplyTo = str_replace(["\r", "\n"], '', $replyTo);

    return [
        'From: ' . MAIL_FROM,
        'Reply-To: ' . $safeReplyTo,
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
    ];
}

// =============================================================================
// メイン処理
// =============================================================================

// HTTPメソッドの検証
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, false, 'この操作は許可されていません。');
}

// CSRF対策: Origin検証
if (!isValidOrigin()) {
    respond(403, false, '送信元を確認できません。');
}

// スパム対策: ハニーポットフィールド
// ボットは隠しフィールドに入力する傾向があるため、入力があれば正常完了として返す
if (!empty($_POST['website'] ?? '')) {
    respond(200, true, '送信を受け付けました。担当者よりご連絡します。');
}

// レート制限
$clientAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!isWithinRateLimit($clientAddress)) {
    respond(429, false, '続けて送信する場合は、1分ほどお待ちください。');
}

// 入力値の検証
$validationError = validateInput($_POST);
if ($validationError !== null) {
    respond(422, false, $validationError);
}

// メールの送信
$mailBody = createMailBody($_POST);
$mailHeaders = createMailHeaders(trim((string) $_POST['email']));
$encodedSubject = '=?UTF-8?B?' . base64_encode(MAIL_SUBJECT) . '?=';

if (!mail(MAIL_TO, $encodedSubject, $mailBody, implode("\r\n", $mailHeaders))) {
    respond(503, false, 'ただいま送信できません。時間をおいて再度お試しください。');
}

// レート制限のタイムスタンプを更新
updateRateLimitTimestamp($clientAddress);

// 成功レスポンス
respond(200, true, '送信を受け付けました。担当者よりご連絡します。');