<?php
/**
 * お問い合わせフォームの送信先 (ConoHa WING / PHP)
 *
 * 同一オリジンのエンドポイントなので、.htaccess の CSP
 * (connect-src 'self' / form-action 'self') を緩めずに動作します。
 *
 * 差出人・宛先ともに info@sei-ko.org を使用します。
 * 差出人を他ドメイン (gmail.com など) にすると SPF/DKIM が一致せず、
 * 迷惑メール扱いや配送拒否の原因になるため変更しないでください。
 */

declare(strict_types=1);

// =============================================================================
// 設定
// =============================================================================

const MAIL_TO      = 'info@sei-ko.org'; // サイト管理者の連絡先
const MAIL_FROM    = 'info@sei-ko.org'; // 差出人 (SPF/DKIM を通すため必ず自ドメイン)
const MAIL_SUBJECT = '【デジタル担当室】お問い合わせがありました';
const ALLOWED_HOST = 'www.sei-ko.org';

const MAX_NAME    = 100;
const MAX_EMAIL   = 254;
const MAX_MESSAGE = 5000;

// UTF-8 のまま送る (Japanese を指定すると ISO-2022-JP に変換される)
mb_internal_encoding('UTF-8');
mb_language('uni');
date_default_timezone_set('Asia/Tokyo');

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

// =============================================================================
// ヘルパー
// =============================================================================

/**
 * JSONを返して処理を終了する
 */
function respond(int $status, string $message)
{
    http_response_code($status);
    echo json_encode(['ok' => $status < 400, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * メールヘッダーインジェクション対策として改行を除去する
 */
function singleLine(string $value): string
{
    return trim(str_replace(["\r", "\n"], '', $value));
}

/**
 * リクエストが自サイトから送られたものか確認する
 */
function isSameOrigin(): bool
{
    $source = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? '';
    if ($source === '') {
        return false;
    }

    $host = parse_url($source, PHP_URL_HOST);
    if ($host === null || $host === false) {
        return false;
    }

    // HTTP_HOST にはポートが含まれることがあるので取り除いて比較する
    $selfHost = strtok((string)($_SERVER['HTTP_HOST'] ?? ''), ':');

    return $host === ALLOWED_HOST || ($selfHost !== false && $host === $selfHost);
}

// =============================================================================
// 検証
// =============================================================================

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(405, '不正なリクエストです。');
}

if (!isSameOrigin()) {
    respond(403, '不正なリクエストです。');
}

// ハニーポット: 人間には見えない項目。埋まっていれば自動投稿とみなす。
// 送信元にはエラーを返さず、成功したように見せて破棄する。
if (trim((string)($_POST['website'] ?? '')) !== '') {
    respond(200, '送信しました。内容を確認のうえ、折り返しご連絡します。');
}

$name    = singleLine((string)($_POST['name'] ?? ''));
$email   = singleLine((string)($_POST['email'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $message === '') {
    respond(422, '未入力の項目があります。');
}

if (mb_strlen($name) > MAX_NAME || mb_strlen($email) > MAX_EMAIL || mb_strlen($message) > MAX_MESSAGE) {
    respond(422, '入力内容が長すぎます。');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(422, 'メールアドレスの形式が正しくありません。');
}

// =============================================================================
// 送信
// =============================================================================

$sentAt = date('Y-m-d H:i:s');

$body = <<<TEXT
デジタル担当室のお問い合わせフォームから送信がありました。

■ お名前
{$name}

■ メールアドレス
{$email}

■ ご相談内容
{$message}

------------------------------
送信日時: {$sentAt}
TEXT;

$headers = [
    'From'     => sprintf('%s <%s>', mb_encode_mimeheader('デジタル担当室'), MAIL_FROM),
    'Reply-To' => $email,
];

$sent = mb_send_mail(MAIL_TO, MAIL_SUBJECT, $body, $headers, '-f' . MAIL_FROM);

if (!$sent) {
    error_log('contact.php: mb_send_mail failed');
    respond(500, '送信できませんでした。お手数ですが、時間をおいて再度お試しください。');
}

respond(200, '送信しました。内容を確認のうえ、折り返しご連絡します。');
