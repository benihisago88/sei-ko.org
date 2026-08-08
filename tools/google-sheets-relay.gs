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
const SECRET = 'cb69e004d5117de842fb0926967cb18f5de744d0f56412ec';

// 記録先シート名。無ければ自動で作成し、見出し行を付けます。
const SHEET_NAME = 'お問い合わせ';

const HEADERS = ['受信日時', 'お名前', 'メールアドレス', 'ご相談内容', 'メール送信'];

/**
 * contact.php からの POST を受け取る
 */
function doPost(e) {
  try {
    const params = (e && e.parameter) || {};

    // 合言葉が一致しない書き込みは受け付けない
    if (params.secret !== SECRET) {
      return jsonResponse({ ok: false, message: 'forbidden' });
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
    console.error(error);
    return jsonResponse({ ok: false, message: String(error) });
  }
}

/**
 * 記録先シートを取得する (無ければ見出し付きで作成)
 */
function getSheet() {
  const book = SpreadsheetApp.getActiveSpreadsheet();
  let sheet = book.getSheetByName(SHEET_NAME);

  if (!sheet) {
    sheet = book.insertSheet(SHEET_NAME);
  }

  if (sheet.getLastRow() === 0) {
    sheet.appendRow(HEADERS);
    sheet.setFrozenRows(1);
  }

  return sheet;
}

/**
 * JSON を返す
 */
function jsonResponse(payload) {
  return ContentService
    .createTextOutput(JSON.stringify(payload))
    .setMimeType(ContentService.MimeType.JSON);
}
