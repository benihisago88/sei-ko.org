/**
 * お問い合わせの控えをスプレッドシートに記録する Apps Script
 *
 * contact.php から POST を受け取り、シートに1行追加します。
 * このファイルはサーバーに置くためのものではなく、Apps Script
 * エディタに貼り付けて使います (tools/ は .htaccess で公開停止)。
 *
 * デプロイ手順は tools/README.md を参照してください。
 */

// contact.php の SHEET_SECRET と同じ値にする
// 変更する場合は必ず両方を書き換えること
const SECRET = 'cb69e004d5117de842fb0926967cb18f5de744d0f56412ec';

// 記録先シート名。無ければ自動で作成し、見出し行を付けます。
const SHEET_NAME = 'お問い合わせ';

const HEADERS = ['受信日時', 'お名前', 'メールアドレス', 'ご相談内容', 'メール送信'];

/**
 * contact.php からの POST を受け取る
 *
 * @param {Object} e - Apps Script のイベントオブジェクト
 * @return {ContentService.TextOutput} JSON レスポンス
 */
function doPost(e) {
  try {
    const params = (e && e.parameter) || {};

    // 合言葉が一致しない書き込みは受け付けない
    // 第三者が URL を知っても書き込めないようにするため
    if (params.secret !== SECRET) {
      return jsonResponse({ ok: false, message: 'forbidden' });
    }

    // 必須フィールドの簡易チェック
    if (!params.name && !params.email && !params.message) {
      return jsonResponse({ ok: false, message: 'empty payload' });
    }

    getSheet().appendRow([
      params.sent_at || new Date(),
      params.name || '',
      params.email || '',
      params.message || '',
      params.mailed || ''
    ]);

    return jsonResponse({ ok: true });
  } catch (error) {
    // 想定外のエラーはログに残し、呼び出し元には安全なメッセージだけ返す
    console.error('doPost failed: %s', String(error));
    return jsonResponse({ ok: false, message: 'internal error' });
  }
}

/**
 * 記録先シートを取得する (無ければ見出し付きで作成)
 *
 * 初回書き込み時にシートが存在しなければ自動生成する。
 * 見出し行は固定行として凍結し、スクロールしても常に表示されるようにする。
 *
 * @return {GoogleAppsScript.Spreadsheet.Sheet}
 */
function getSheet() {
  const book = SpreadsheetApp.getActiveSpreadsheet();
  let sheet = book.getSheetByName(SHEET_NAME);

  if (!sheet) {
    sheet = book.insertSheet(SHEET_NAME);
  }

  // 見出し行がなければ追加（シート新規作成時または全行削除時に備える）
  if (sheet.getLastRow() === 0) {
    sheet.appendRow(HEADERS);
    sheet.setFrozenRows(1);
  }

  return sheet;
}

/**
 * JSON レスポンスを生成する
 *
 * @param {Object} payload - レスポンスに含めるオブジェクト
 * @return {ContentService.TextOutput}
 */
function jsonResponse(payload) {
  return ContentService
    .createTextOutput(JSON.stringify(payload))
    .setMimeType(ContentService.MimeType.JSON);
}
