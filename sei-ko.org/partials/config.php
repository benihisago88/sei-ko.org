<?php

declare(strict_types=1);

if (!defined('APP')) {
    http_response_code(404);
    exit;
}

// =============================================================================
// サイト全体の設定
// =============================================================================

/** 正規ホスト。http / apex は www へ301される（サーバー側設定） */
const SITE_URL = 'https://www.sei-ko.org';

/** キャッシュバスティング用のバージョン。該当ファイルを更新したら必ず上げる */
const V_ASSET = '20260801';   // 画像・アイコン・manifest
const V_CSS   = '20260731.2'; // styles.css
const V_JS    = '20260731.1'; // script.js
const V_ICON  = '20260716.5'; // favicon.svg

/** OGP画像（全ページ共通） */
const OGP_PATH = '/assets/ogp.jpg';
const OGP_ALT  = 'リモートお助け隊 パソコン・Excel・AIの困りごと相談。初回相談無料。';

/**
 * HTML属性・テキストのエスケープ
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * サイト内パスから絶対URLを組み立てる（canonical / OGP 用）
 */
function absUrl(string $path = '/'): string
{
    return SITE_URL . $path;
}

/**
 * 構造化データを JSON-LD として出力する
 *
 * 配列で書くことで、手書きJSONの構文崩れを防ぐ。
 */
function jsonLd(array $data): string
{
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
