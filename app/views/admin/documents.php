<?php
ob_start();
$pageTitle = "Document Repository | Admin Portal";
$viewTitle = "Documents";

function getDocTypeLabel(string $type): string {
    return match ($type) {
        'aadhaar_front' => 'Aadhaar Card (Front)',
        'aadhaar_back'  => 'Aadhaar Card (Back)',
        'aadhaar'       => 'Aadhaar Card',
        'pan_card'      => 'PAN Card',
        'profile_photo' => 'Photograph',
        'marksheet'     => 'Education Marksheet',
        'offer_letter'  => 'Offer Letter',
        'cancelled_cheque' => 'Bank Proof / Cheque',
        default         => ucwords(str_replace('_', ' ', $type))
    };
}
?>

<style>
    .docs-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }

    .docs-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.02em;
        margin: 0 0 4px 0;
    }

    .docs-subtitle {
        color: var(--muted);
        font-size: 14px;
        margin: 0;
    }

    .docs-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        padding: 20px 24px;
        margin-bottom: 24px;
    }

    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }

    .form-group-item {
        flex: 1;
        min-width: 200px;
    }

    .form-label-small {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 6px;
    }

    .input-control {
        width: 100%;
        height: 42px;
        padding: 0 14px;
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        background: var(--surface);
        color: var(--text);
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
    }

    .input-control:focus {
        border-color: var(--brand);
    }

    .btn-filter {
        height: 42px;
        padding: 0 18px;
        background: var(--brand);
        color: var(--surface);
        border: none;
        border-radius: var(--r-md);
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }

    .btn-reset {
        height: 42px;
        padding: 0 14px;
        background: var(--surface);
        color: var(--text);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .bulk-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
    }

    .docs-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }

    .docs-table th {
        background: var(--surface-2);
        color: var(--muted);
        font-weight: 600;
        font-size: 12.5px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 12px 18px;
        border-bottom: 1px solid var(--line);
        white-space: nowrap;
    }

    .docs-table td {
        padding: 14px 18px;
        border-bottom: 1px solid var(--line);
        color: var(--text);
        vertical-align: middle;
    }

    .docs-table tr:last-child td {
        border-bottom: none;
    }

    .docs-table tr:hover td {
        background: rgba(31, 92, 74, 0.02);
    }

    .btn-action-sm {
        height: 32px;
        padding: 0 12px;
        background: var(--surface);
        color: var(--text);
        border: 1px solid var(--line);
        border-radius: var(--r-sm);
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-action-sm:hover {
        background: var(--surface-2);
    }

    /* Drawer Preview */
    .drawer-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(38, 51, 45, 0.4);
        z-index: 200;
        backdrop-filter: blur(2px);
    }

    .drawer-overlay.active {
        display: block;
    }

    .drawer-panel {
        position: fixed;
        top: 0;
        right: -600px;
        width: 100%;
        max-width: 600px;
        height: 100vh;
        background: var(--surface);
        box-shadow: -8px 0 32px rgba(38, 51, 45, 0.15);
        z-index: 210;
        display: flex;
        flex-direction: column;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .drawer-panel.open {
        transform: translateX(-600px);
    }

    .drawer-header {
        height: 64px;
        padding: 0 24px;
        border-bottom: 1px solid var(--line);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .drawer-body {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
    }
</style>

<div class="docs-header">
    <div>
        <h1 class="docs-title">Documents Repository</h1>
        <p class="docs-subtitle">All candidate document uploads and compressed archives</p>
    </div>
</div>

<!-- Search & Filtering Card -->
<div class="docs-card">
    <form method="GET" action="<?= url('/admin/documents') ?>" class="filter-form">
        <div class="form-group-item" style="flex: 2;">
            <label class="form-label-small" for="searchQ">Candidate / File Search</label>
            <input type="text" name="q" id="searchQ" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Search candidate name, email, or filename..." class="input-control">
        </div>

        <div class="form-group-item">
            <label class="form-label-small" for="filterType">Document Type</label>
            <select name="type" id="filterType" class="input-control">
                <option value="">All Document Types</option>
                <?php foreach (($availableTypes ?? []) as $t): ?>
                    <option value="<?= e($t) ?>" <?= (($_GET['type'] ?? '') === $t) ? 'selected' : '' ?>>
                        <?= getDocTypeLabel($t) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn-filter" aria-label="Apply filter">
                <svg width="15" height="15" aria-hidden="true"><use href="#icon-search"></use></svg>
                <span>Filter</span>
            </button>
            <?php if (!empty($_GET['q']) || !empty($_GET['type'])): ?>
                <a href="<?= url('/admin/documents') ?>" class="btn-reset" aria-label="Reset filters">
                    <svg width="14" height="14" aria-hidden="true"><use href="#icon-x"></use></svg>
                    <span>Reset</span>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Bulk Actions & Table -->
<form id="bulkDocsForm" method="POST" action="<?= url('/admin/documents/bulk-action') ?>">
    <?= csrf_field() ?>

    <div class="bulk-bar">
        <div style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: var(--muted);">
            <input type="checkbox" id="selectAllDocs" style="cursor: pointer; width: 16px; height: 16px;" onchange="toggleSelectAllDocs(this)">
            <label for="selectAllDocs" style="cursor: pointer; font-weight: 500;">Select All on Page</label>
            <span id="selectedCountBadge" style="display: none; background: var(--brand-soft); color: var(--brand); font-weight: 600; padding: 2px 8px; border-radius: var(--r-full); font-size: 12px;">
                0 selected
            </span>
        </div>

        <div>
            <button type="submit" name="bulk_action" value="zip" class="btn-action-sm" onclick="return confirmBulkZip()" aria-label="Download Selected documents as ZIP">
                <svg width="15" height="15" aria-hidden="true"><use href="#icon-download"></use></svg>
                <span>Download Selected (ZIP)</span>
            </button>
        </div>
    </div>

    <div class="table-container">
        <table class="docs-table" aria-label="Candidate Documents Table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">#</th>
                    <th>Candidate</th>
                    <th>Document Type</th>
                    <th>File Details</th>
                    <th>Uploaded Date</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($documents)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 48px 20px; color: var(--muted);">
                            No documents uploaded yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($documents as $d): ?>
                        <?php
                        $fileSizeBefore = (int)($d['file_size_before'] ?? $d['file_size']);
                        $fileSizeAfter = (int)$d['file_size'];
                        $savingsPercent = ($fileSizeBefore > $fileSizeAfter && $fileSizeBefore > 0)
                            ? round((($fileSizeBefore - $fileSizeAfter) / $fileSizeBefore) * 100)
                            : 0;
                        $mime = $d['mime_type'] ?? '';
                        $isPdf = str_contains($mime, 'pdf');
                        $isImg = str_starts_with($mime, 'image/');
                        ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="selected_docs[]" value="<?= $d['id'] ?>" class="doc-chk" style="cursor: pointer; width: 16px; height: 16px;" onchange="updateSelectedCount()">
                            </td>
                            <td>
                                <a href="<?= url('/admin/candidates/' . $d['user_id']) ?>" style="font-weight: 600; color: var(--text); text-decoration: none;">
                                    <?= e($d['candidate_name']) ?>
                                </a>
                                <div style="font-size: 12.5px; color: var(--muted);"><?= e($d['candidate_email']) ?></div>
                            </td>
                            <td>
                                <span style="font-weight: 500; font-size: 13.5px;">
                                    <?= getDocTypeLabel($d['document_type']) ?>
                                </span>
                            </td>
                            <td>
                                <div style="font-size: 13px; color: var(--text); max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= e($d['original_name']) ?>">
                                    <?= e($d['original_name']) ?>
                                </div>
                                <div style="font-size: 12px; color: var(--muted); margin-top: 2px;">
                                    <?= round($fileSizeAfter / 1024, 1) ?> KB
                                    <?php if ($savingsPercent > 0): ?>
                                        <span style="font-size: 11px; font-weight: 600; color: var(--brand); background: var(--brand-soft); padding: 1px 6px; border-radius: var(--r-full);">
                                            -<?= $savingsPercent ?>%
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="color: var(--muted); font-size: 13px; white-space: nowrap;">
                                <?= date('d M Y, h:i A', strtotime($d['uploaded_at'])) ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; align-items: center; gap: 6px;">
                                    <button type="button" class="btn-action-sm" onclick="openDocDrawer('<?= e($d['original_name']) ?>', '<?= url('/document/download/' . $d['id']) ?>', '<?= $mime ?>')" aria-label="View document <?= e($d['original_name']) ?>">
                                        <svg width="14" height="14" aria-hidden="true"><use href="#icon-file-text"></use></svg>
                                        <span>View</span>
                                    </button>
                                    <a href="<?= url('/document/download/' . $d['id'] . '?download=1') ?>" class="btn-action-sm" aria-label="Download document <?= e($d['original_name']) ?>">
                                        <svg width="14" height="14" aria-hidden="true"><use href="#icon-download"></use></svg>
                                        <span>Download</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</form>

<!-- Document Preview Drawer -->
<div class="drawer-overlay" id="docDrawerOverlay" onclick="closeDocDrawer()"></div>
<div class="drawer-panel" id="docDrawerPanel">
    <div class="drawer-header">
        <h3 id="docDrawerTitle" style="font-size: 16px; font-weight: 600; margin: 0; color: var(--text);">Document Preview</h3>
        <button type="button" onclick="closeDocDrawer()" style="background: none; border: none; cursor: pointer; color: var(--muted);" aria-label="Close preview drawer">
            <svg width="20" height="20" aria-hidden="true"><use href="#icon-x"></use></svg>
        </button>
    </div>
    <div class="drawer-body" id="docDrawerBody"></div>
</div>

<script>
function toggleSelectAllDocs(master) {
    var chks = document.querySelectorAll('.doc-chk');
    chks.forEach(c => c.checked = master.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    var checked = document.querySelectorAll('.doc-chk:checked').length;
    var badge = document.getElementById('selectedCountBadge');
    if (checked > 0) {
        badge.style.display = 'inline-block';
        badge.textContent = checked + ' selected';
    } else {
        badge.style.display = 'none';
    }
}

function confirmBulkZip() {
    var checked = document.querySelectorAll('.doc-chk:checked').length;
    if (checked === 0) {
        alert('Please select at least one document to download.');
        return false;
    }
    return true;
}

function openDocDrawer(title, url, mime) {
    document.getElementById('docDrawerTitle').textContent = title;
    var body = document.getElementById('docDrawerBody');
    body.innerHTML = '';
    if (mime.indexOf('image/') === 0) {
        body.innerHTML = '<img src="' + url + '" style="max-width:100%; height:auto; border-radius: var(--r-sm);" alt="' + title + '">';
    } else {
        body.innerHTML = '<iframe src="' + url + '#toolbar=0" style="width:100%; height:80vh; border:none; border-radius: var(--r-sm);"></iframe>';
    }
    document.getElementById('docDrawerOverlay').classList.add('active');
    document.getElementById('docDrawerPanel').classList.add('open');
}

function closeDocDrawer() {
    document.getElementById('docDrawerOverlay').classList.remove('active');
    document.getElementById('docDrawerPanel').classList.remove('open');
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/admin.php';
?>
