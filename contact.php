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
 *
 * メール送信のあと、控えとして Google スプレッドシートにも記録します
 * (SHEET_ENDPOINT を設定した場合のみ)。ブラウザからではなくこの
 * サーバーから送るので、.htaccess の CSP には影響しません。
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

// 控えの記録先 (Google Apps Script のウェブアプリ URL)。
// 空にすると記録処理を丸ごとスキップします。
// SHEET_SECRET は Apps Script 側の SECRET と同じ値にしてください。
const SHEET_ENDPOINT       = 'https://script.google.com/macros/s/AKfycbwQPv12e_f7JDm7srAWjv5i8s1gnZVsRbuLkSxTMdXOcBJ6quEZR3XqBI6oMd795TXE/exec';
const SHEET_SECRET         = 'cb69e004d5117de842fb0926967cb18f5de744d0f56412ec';
const SHEET_TIMEOUT_SECONDS = 5;

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

/**
 * 控えを Google スプレッドシートへ記録する
 *
 * メールが届かなかった場合の保険なので、失敗しても利用者への応答は
 * 変えません。原因を追えるようにログだけ残します。
 *
 * cURL を優先し、失敗した場合はストリームで再試行します。CA 証明書の
 * 設定など環境差でどちらか一方が使えないことがあるためです。
 *
 * @param array<string, string> $fields 送信する項目
 */
function forwardToSheet(array $fields): void
{
    if (SHEET_ENDPOINT === '') {
        return;
    }

    $body = http_build_query($fields + ['secret' => SHEET_SECRET]);

    $curlError = postWithCurl($body);
    if ($curlError === null) {
        return;
    }

    $streamError = postWithStream($body);
    if ($streamError === null) {
        error_log('contact.php: sheet forward recovered by stream (curl: ' . $curlError . ')');
        return;
    }

    error_log(sprintf('contact.php: sheet forward failed (curl: %s / stream: %s)', $curlError, $streamError));
}

/**
 * cURL で POST する
 *
 * @return string|null 成功なら null、失敗なら理由
 */
function postWithCurl(string $body): ?string
{
    if (!function_exists('curl_init')) {
        return 'not available';
    }

    $curl = curl_init(SHEET_ENDPOINT);
    curl_setopt_array($curl, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_RETURNTRANSFER => true,
        // Apps Script は googleusercontent.com へ 302 で転送する
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => SHEET_TIMEOUT_SECONDS,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
    ]);

    $response = curl_exec($curl);
    $status   = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $error    = curl_error($curl);
    curl_close($curl);

    if ($response === false) {
        return $error !== '' ? $error : 'request failed';
    }

    if ($status >= 400) {
        return 'status ' . $status;
    }

    return null;
}

/**
 * ストリーム (file_get_contents) で POST する
 *
 * cURL が使えない環境向けのフォールバック。
 * ignore_errors を有効にし、戻り値と error_get_last() の両方で成否を判定する。
 *
 * @return string|null 成功なら null、失敗なら理由
 */
function postWithStream(string $body): ?string
{
    if (!ini_get('allow_url_fopen')) {
        return 'allow_url_fopen disabled';
    }

    $context = stream_context_create([
        'http' => [
            'method'        => 'POST',
            'header'        => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content'       => $body,
            'timeout'       => SHEET_TIMEOUT_SECONDS,
            'ignore_errors' => true,
        ],
    ]);

    // 警告を抑制しつつ、失敗時は error_get_last() で詳細を取得する
    $result = @file_get_contents(SHEET_ENDPOINT, false, $context);

    if ($result === false) {
        $lastError = error_get_last();
        $detail = $lastError['message'] ?? 'unknown stream error';
        return 'request failed: ' . $detail;
    }

    return null;
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

// 警告が出力に混ざると JSON が壊れるため抑止する (失敗は下でログに残す)
$sent = @mb_send_mail(MAIL_TO, MAIL_SUBJECT, $body, $headers, '-f' . MAIL_FROM);

// メールが失敗したときこそ控えが要るので、結果によらず記録する
forwardToSheet([
    'sent_at' => $sentAt,
    'name'    => $name,
    'email'   => $email,
    'message' => $message,
    'mailed'  => $sent ? 'OK' : 'FAILED',
]);

if (!$sent) {
    error_log('contact.php: mb_send_mail failed');
    respond(500, '送信できませんでした。お手数ですが、時間をおいて再度お試しください。');
}

respond(200, '送信しました。内容を確認のうえ、折り返しご連絡します。');
