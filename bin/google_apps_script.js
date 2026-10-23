/**
 * Realty Smartz Pathshala - Google Apps Script Webhook
 * 
 * Synchronizes Candidate and Interview Candidate submissions directly to Google Sheets in real-time.
 * 
 * Supports 2 Tabs:
 *   1. "candidates" (for standard onboarding & aptitude test candidates - 17 columns)
 *   2. "Interview Candidate" (for interview form candidates & admin feedback evaluations - 31 columns)
 * 
 * SETUP INSTRUCTIONS:
 * 1. Open your Google Spreadsheet (e.g., "candidates").
 * 2. In Google Sheets, click "Extensions" > "Apps Script".
 * 3. Delete any code in the editor, and paste this entire script.
 * 4. Click the "Save" icon (Ctrl+S or Cmd+S).
 * 5. Click "Deploy" > "New deployment".
 * 6. Under "Select type" (gear icon), select "Web app".
 * 7. In the configuration dialog:
 *    - Description: "Realty Smartz Sync Webhook"
 *    - Execute as: "Me" (your Google account)
 *    - Who has access: "Anyone" (CRITICAL: must be Anyone so your website can send data)
 * 8. Click "Deploy", and authorize permissions if prompted.
 * 9. Copy the "Web app URL" (starts with https://script.google.com/macros/s/...)
 *    and ensure it matches your configured webhook URL in the application.
 */

function doGet(e) {
  return ContentService.createTextOutput(JSON.stringify({
    status: "active",
    message: "Realty Smartz Pathshala Google Sheets Webhook is running!",
    timestamp: new Date().toISOString()
  })).setMimeType(ContentService.MimeType.JSON);
}

function doPost(e) {
  var lock = LockService.getScriptLock();
  // Wait up to 15 seconds for concurrent executions
  try {
    lock.waitLock(15000);
  } catch (err) {
    return ContentService.createTextOutput(JSON.stringify({
      status: "error",
      message: "Server busy, could not acquire lock"
    })).setMimeType(ContentService.MimeType.JSON);
  }

  try {
    var raw = e.postData ? e.postData.contents : "";
    if (!raw) {
      return ContentService.createTextOutput(JSON.stringify({
        status: "error",
        message: "No POST data received"
      })).setMimeType(ContentService.MimeType.JSON);
    }

    var data = JSON.parse(raw);
    var candidateId = data.candidate_id || (data.row && data.row[0]) || "";
    if (!candidateId) {
      return ContentService.createTextOutput(JSON.stringify({
        status: "error",
        message: "Missing candidate_id in payload"
      })).setMimeType(ContentService.MimeType.JSON);
    }

    // Determine target sheet tab name (default to 'candidates' or 'Interview Candidate')
    var targetTabName = data.tab || data.sheet_name || data.sheet || "candidates";
    var headers = data.headers || [];
    var rowValues = data.row || [];

    var ss = SpreadsheetApp.getActiveSpreadsheet();
    var sheet = getOrCreateSheet(ss, targetTabName, headers);

    // Ensure header row exists in Row 1
    if (sheet.getLastRow() === 0 && headers.length > 0) {
      sheet.appendRow(headers);
      styleHeaderRow(sheet, headers.length);
    }

    // Search Column A for existing candidate ID to prevent duplicate rows
    var existingRowIndex = -1;
    var lastRow = sheet.getLastRow();
    if (lastRow > 1) {
      var idValues = sheet.getRange(2, 1, lastRow - 1, 1).getValues();
      for (var i = 0; i < idValues.length; i++) {
        if (String(idValues[i][0]).trim().toUpperCase() === String(candidateId).trim().toUpperCase()) {
          existingRowIndex = i + 2; // Row index is 1-based, +2 because of header row and 0-based array
          break;
        }
      }
    }

    var action = "appended";
    if (existingRowIndex > 1) {
      // Update existing row in-place (real-time admin autosave / feedback updates)
      var targetRange = sheet.getRange(existingRowIndex, 1, 1, rowValues.length);
      targetRange.setNumberFormat("@"); // treat as text to keep numbers/dates clean
      targetRange.setValues([rowValues]);
      action = "updated";
    } else {
      // Append new row
      sheet.appendRow(rowValues);
      var newRowIndex = sheet.getLastRow();
      sheet.getRange(newRowIndex, 1, 1, rowValues.length).setNumberFormat("@");
      action = "appended";
    }

    return ContentService.createTextOutput(JSON.stringify({
      status: "success",
      action: action,
      tab: sheet.getName(),
      candidate_id: candidateId,
      timestamp: new Date().toISOString()
    })).setMimeType(ContentService.MimeType.JSON);

  } catch (error) {
    return ContentService.createTextOutput(JSON.stringify({
      status: "error",
      message: error.toString()
    })).setMimeType(ContentService.MimeType.JSON);
  } finally {
    lock.releaseLock();
  }
}

/**
 * Find sheet tab case-insensitively or create it if missing
 */
function getOrCreateSheet(ss, name, headers) {
  var sheets = ss.getSheets();
  var normalizedTarget = name.toLowerCase().replace(/[^a-z0-9]/g, '');

  for (var i = 0; i < sheets.length; i++) {
    var curName = sheets[i].getName();
    var normalizedCur = curName.toLowerCase().replace(/[^a-z0-9]/g, '');
    if (normalizedCur === normalizedTarget) {
      return sheets[i];
    }
  }

  // Handle singular vs plural variations (e.g., 'candidate' vs 'candidates', 'interview candidate' vs 'interview candidates')
  for (var j = 0; j < sheets.length; j++) {
    var sName = sheets[j].getName().toLowerCase();
    if (normalizedTarget.indexOf('interview') !== -1 && sName.indexOf('interview') !== -1) {
      return sheets[j];
    }
    if (normalizedTarget.indexOf('interview') === -1 && (sName === 'candidates' || sName === 'candidate')) {
      return sheets[j];
    }
  }

  // Not found -> create new sheet tab with exact target name
  var newSheet = ss.insertSheet(name);
  if (headers && headers.length > 0) {
    newSheet.appendRow(headers);
    styleHeaderRow(newSheet, headers.length);
  }
  return newSheet;
}

/**
 * Apply clean professional styling to header row (Navy Blue, White Text, Bold, Frozen top row)
 */
function styleHeaderRow(sheet, numCols) {
  try {
    sheet.setFrozenRows(1);
    var headerRange = sheet.getRange(1, 1, 1, numCols);
    headerRange.setBackground("#1B365D"); // Realty Smartz Navy
    headerRange.setFontColor("#FFFFFF");
    headerRange.setFontWeight("bold");
    headerRange.setFontFamily("Inter");
    headerRange.setWrap(true);
    sheet.setRowHeight(1, 40);
  } catch (e) {
    // Non-critical visual formatting fallback
  }
}
