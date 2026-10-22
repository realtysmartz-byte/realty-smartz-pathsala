<?php
ob_start();
$pageTitle = "Candidates | Realty Smartz Pathshala";
$viewTitle = "Candidates";

$totalPages = max(1, ceil($total / $perPage));

function buildFilterUrl(array $overrides = []): string {
    $params = array_merge($_GET, $overrides);
    $filtered = array_filter($params, fn($v) => $v !== '' && $v !== null);
    return url('/admin/candidates' . (!empty($filtered) ? '?' . http_build_query($filtered) : ''));
}
?>

<style>
    /* -------------------------------------------------------------
       4. CANDIDATES LIST STYLES
       ------------------------------------------------------------- */
    
    .candidates-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .candidates-title-group h2 {
        font-size: 24px;
        font-weight: 600;
        color: var(--text);
        letter-spacing: -0.02em;
        margin-bottom: 4px;
    }

    .candidates-title-group p {
        font-size: 14px;
        color: var(--muted);
    }

    /* Filter Bar Card */
    .filter-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        padding: 18px 20px;
        margin-bottom: 24px;
    }

    .filter-form {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .filter-search-box {
        flex: 1;
        min-width: 240px;
        position: relative;
    }

    .filter-search-box input {
        width: 100%;
        height: 40px;
        border-radius: var(--r-md);
        border: 1px solid var(--line);
        background: var(--bg);
        padding: 0 14px 0 38px;
        font-size: 14px;
        color: var(--text);
        outline: none;
    }

    .filter-search-box input:focus {
        background: var(--surface);
        border-color: var(--brand);
    }

    .filter-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        pointer-events: none;
    }

    .filter-select {
        height: 40px;
        padding: 0 12px;
        border-radius: var(--r-md);
        border: 1px solid var(--line);
        background: var(--bg);
        font-size: 13.5px;
        color: var(--text);
        outline: none;
        cursor: pointer;
    }

    .filter-select:focus {
        border-color: var(--brand);
        background: var(--surface);
    }

    .filter-btn {
        height: 40px;
        padding: 0 18px;
        background: var(--brand);
        color: var(--surface);
        border: none;
        border-radius: var(--r-md);
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .filter-btn:hover {
        background: var(--brand-hover);
    }

    .filter-reset {
        height: 40px;
        padding: 0 14px;
        background: transparent;
        color: var(--muted);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .filter-reset:hover {
        background: var(--surface-2);
        color: var(--text);
    }

    /* Table Container & Full-Width Table */
    .table-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        overflow: hidden;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .candidates-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        text-align: left;
    }

    .candidates-table th {
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 12px 16px;
        border-bottom: 1px solid var(--line);
        background: var(--surface);
        white-space: nowrap;
    }

    .candidates-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--line);
        color: var(--text);
        vertical-align: middle;
        white-space: nowrap;
    }

    .candidates-table tr:last-child td {
        border-bottom: none;
    }

    .candidates-table tr:hover td {
        background: var(--bg);
    }

    .row-checkbox {
        width: 18px;
        height: 18px;
        accent-color: var(--brand);
        cursor: pointer;
    }

    .candidate-name-cell {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .guest-chip {
        display: inline-block;
        padding: 2px 6px;
        background: var(--brand-soft);
        color: var(--brand);
        font-size: 11px;
        font-weight: 600;
        border-radius: var(--r-sm);
        letter-spacing: 0.02em;
    }

    .action-group {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-action-view {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 14px;
        background: var(--brand);
        color: var(--surface);
        border: none;
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.15s ease;
    }

    .btn-action-view:hover {
        background: var(--brand-hover);
    }

    .btn-action-pdf {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: var(--surface);
        color: var(--brand);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    .btn-action-pdf:hover {
        background: var(--brand-soft);
        border-color: var(--brand);
    }

    .btn-action-edit {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        background: var(--surface);
        color: var(--text);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    .btn-action-edit:hover {
        background: var(--surface-2);
        border-color: var(--brand);
        color: var(--brand);
    }

    .btn-action-delete {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        background: var(--surface);
        color: var(--danger);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    .btn-action-delete:hover {
        background: var(--danger-soft);
        border-color: var(--danger);
    }

    .joining-date-val {
        font-size: 13.5px;
        color: var(--text);
        font-weight: 500;
    }

    .joining-date-empty {
        font-size: 13px;
        color: var(--muted);
        font-style: italic;
    }

    /* Modal Overlay & Dialog Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(38, 51, 45, 0.45);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-dialog {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        box-shadow: 0 12px 36px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 10;
        overflow: hidden;
        pointer-events: auto !important;
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid var(--line);
        flex-shrink: 0;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }

    .modal-close-btn {
        background: transparent;
        border: none;
        color: var(--muted);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        border-radius: var(--r-sm);
        transition: color 0.15s ease;
    }

    .modal-close-btn:hover {
        color: var(--text);
        background: var(--surface-2);
    }

    .modal-body {
        padding: 24px;
        overflow-y: auto;
        max-height: calc(90vh - 140px);
    }

    .modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 24px;
        border-top: 1px solid var(--line);
        background: var(--surface);
        flex-shrink: 0;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    @media (max-width: 600px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
        }
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 14px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text);
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        height: 40px;
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        background: var(--bg);
        padding: 0 12px;
        font-size: 14px;
        color: var(--text);
        outline: none;
        transition: border-color 0.15s ease, background 0.15s ease;
        box-sizing: border-box;
    }

    .form-textarea {
        height: 72px;
        padding: 10px 12px;
        resize: vertical;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: var(--brand);
        background: var(--surface);
    }

    .btn-modal-cancel {
        height: 40px;
        padding: 0 16px;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 14px;
        font-weight: 500;
        color: var(--text);
        cursor: pointer;
    }

    .btn-modal-cancel:hover {
        background: var(--surface-2);
    }

    .btn-modal-save {
        height: 40px;
        padding: 0 20px;
        background: var(--brand);
        border: none;
        border-radius: var(--r-md);
        font-size: 14px;
        font-weight: 600;
        color: var(--surface);
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-modal-save:hover {
        background: var(--brand-hover);
    }

    .btn-modal-delete {
        height: 40px;
        padding: 0 20px;
        background: var(--danger);
        border: none;
        border-radius: var(--r-md);
        font-size: 14px;
        font-weight: 600;
        color: var(--surface);
        cursor: pointer;
        transition: opacity 0.15s ease;
    }

    .btn-modal-delete:hover {
        opacity: 0.9;
    }


    /* Bulk Bar */
    .bulk-bar {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        background: var(--surface);
        border: 1px solid var(--line);
        box-shadow: 0 12px 32px rgba(38, 51, 45, 0.15);
        border-radius: var(--r-full);
        padding: 10px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        z-index: 100;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
        opacity: 0;
    }

    .bulk-bar.active {
        transform: translateX(-50%) translateY(0);
        pointer-events: auto;
        opacity: 1;
    }

    .bulk-count-text {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        white-space: nowrap;
    }

    .bulk-btn {
        height: 38px;
        padding: 0 16px;
        border-radius: var(--r-full);
        font-size: 13px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .bulk-btn-primary {
        background: var(--brand);
        color: var(--surface);
    }

    .bulk-btn-primary:hover {
        background: var(--brand-hover);
    }

    .bulk-btn-secondary {
        background: var(--surface-2);
        color: var(--text);
    }

    .bulk-btn-secondary:hover {
        background: var(--line);
    }

    /* Server-side Pagination Bar */
    .pagination-bar {
        padding: 16px 20px;
        border-top: 1px solid var(--line);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .pagination-info {
        font-size: 13.5px;
        color: var(--muted);
    }

    .pagination-pages {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 8px;
        border-radius: var(--r-md);
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--text);
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.15s ease;
    }

    .page-btn:hover:not(.disabled) {
        background: var(--surface-2);
    }

    .page-btn.active {
        background: var(--brand);
        color: var(--surface);
        border-color: var(--brand);
        font-weight: 600;
    }

    .page-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Mobile Stacked List (< 768px) */
    .candidates-mobile-list {
        display: none;
        flex-direction: column;
        gap: 12px;
        padding: 12px;
    }

    .candidate-mobile-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .card-mobile-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
    }

    .card-mobile-name {
        font-size: 15px;
        font-weight: 600;
        color: var(--text);
    }

    .card-mobile-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        font-size: 13px;
    }

    .card-mobile-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 4px;
    }

    .card-mobile-actions .btn-action-view,
    .card-mobile-actions .btn-action-pdf {
        flex: 1;
        text-align: center;
    }

    @media (max-width: 767.98px) {
        .table-responsive {
            display: none;
        }
        .candidates-mobile-list {
            display: flex;
        }
        .bulk-bar {
            width: 90%;
            padding: 10px 16px;
            justify-content: space-between;
        }
    }
</style>

<!-- Header & Counts -->
<div class="candidates-header">
    <div class="candidates-title-group">
        <h2>Candidates</h2>
        <p><?= number_format($total) ?> candidate<?= $total === 1 ? '' : 's' ?> in total</p>
    </div>
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <a href="<?= url('/admin/aptitude-candidates') ?>" class="btn-bulk-outline" style="height:38px; display:inline-flex; align-items:center; gap:6px; padding:0 14px; text-decoration:none; color:var(--text); border:1px solid var(--line); border-radius:var(--r-md); font-size:13.5px; font-weight:500;" aria-label="Aptitude Candidates">
            <svg width="15" height="15" aria-hidden="true"><use href="#icon-users"></use></svg>
            <span>Aptitude Candidates</span>
        </a>
        <a href="<?= url('/admin/qr-codes') ?>" class="btn-bulk-outline" style="height:38px; display:inline-flex; align-items:center; gap:6px; padding:0 14px; text-decoration:none; color:var(--text); border:1px solid var(--line); border-radius:var(--r-md); font-size:13.5px; font-weight:500;" aria-label="QR Codes">
            <svg width="15" height="15" aria-hidden="true"><use href="#icon-award"></use></svg>
            <span>QR Codes</span>
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-card">
    <form action="<?= url('/admin/candidates') ?>" method="GET" class="filter-form" id="candidatesFilterForm">
        <!-- Search Input -->
        <div class="filter-search-box">
            <span class="filter-search-icon" aria-hidden="true">
                <svg width="16" height="16"><use href="#icon-search"></use></svg>
            </span>
            <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search name, father's name, email, mobile..." aria-label="Search candidates">
        </div>

        <!-- Filter: Test Taken / Not Taken -->
        <select name="test_status" class="filter-select" aria-label="Filter by test status">
            <option value="">All Tests</option>
            <option value="taken" <?= ($testStatus === 'taken') ? 'selected' : '' ?>>Test taken</option>
            <option value="not_taken" <?= ($testStatus === 'not_taken') ? 'selected' : '' ?>>Test not taken</option>
        </select>

        <!-- Filter: Documents Uploaded / Missing -->
        <select name="docs_status" class="filter-select" aria-label="Filter by documents status">
            <option value="">All Documents</option>
            <option value="uploaded" <?= ($docsStatus === 'uploaded') ? 'selected' : '' ?>>Documents uploaded</option>
            <option value="missing" <?= ($docsStatus === 'missing') ? 'selected' : '' ?>>Documents missing</option>
        </select>

        <!-- Filter: Date Range -->
        <select name="date_range" class="filter-select" aria-label="Filter by registration date">
            <option value="">All Time</option>
            <option value="today" <?= ($dateRange === 'today') ? 'selected' : '' ?>>Today</option>
            <option value="7days" <?= ($dateRange === '7days') ? 'selected' : '' ?>>Last 7 days</option>
            <option value="30days" <?= ($dateRange === '30days') ? 'selected' : '' ?>>Last 30 days</option>
        </select>

        <!-- Sort Option -->
        <select name="sort" class="filter-select" aria-label="Sort candidates">
            <option value="created_desc" <?= ($sort === 'created_desc') ? 'selected' : '' ?>>Newest first</option>
            <option value="created_asc" <?= ($sort === 'created_asc') ? 'selected' : '' ?>>Oldest first</option>
            <option value="name_asc" <?= ($sort === 'name_asc') ? 'selected' : '' ?>>Name A-Z</option>
            <option value="score_desc" <?= ($sort === 'score_desc') ? 'selected' : '' ?>>Highest test score</option>
        </select>

        <button type="submit" class="filter-btn" aria-label="Apply filters">
            <svg width="15" height="15" aria-hidden="true"><use href="#icon-filter"></use></svg>
            <span>Filter</span>
        </button>

        <?php if (!empty($search) || !empty($testStatus) || !empty($docsStatus) || !empty($dateRange) || $sort !== 'created_desc'): ?>
            <a href="<?= url('/admin/candidates') ?>" class="filter-reset" aria-label="Reset all filters">Reset</a>
        <?php endif; ?>
    </form>
</div>

<!-- Table Card (Full-Width Table) -->
<div class="table-card">
    <?php if (empty($candidates)): ?>
        <div style="padding: 48px 20px; text-align: center; color: var(--muted); font-size: 14px;">
            No candidates found matching the selected filters.
        </div>
    <?php else: ?>
        <!-- Desktop / Tablet Table -->
        <div class="table-responsive">
            <table class="candidates-table" aria-label="Candidates directory table">
                <thead>
                    <tr>
                        <th style="width: 40px; text-align: center;">
                            <input type="checkbox" id="selectAllCheckbox" class="row-checkbox" aria-label="Select all candidates on page">
                        </th>
                        <th>Name</th>
                        <th>Father's name</th>
                        <th>Mobile</th>
                        <th>Joining date</th>
                        <th>Email</th>
                        <th>Documents</th>
                        <th>Test score</th>
                        <th>Registered</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidates as $c): ?>
                        <tr id="candidateRow_<?= $c['id'] ?>">
                            <td style="text-align: center;">
                                <input type="checkbox" class="row-checkbox candidate-select" value="<?= $c['id'] ?>" aria-label="Select <?= e($c['full_name']) ?>">
                            </td>
                            <td>
                                <div class="candidate-name-cell">
                                    <span style="font-weight: 600;" id="cName_<?= $c['id'] ?>"><?= e($c['full_name']) ?></span>
                                    <?php if (!empty($c['is_guest'])): ?>
                                        <span class="guest-chip">Guest</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td id="cFather_<?= $c['id'] ?>"><?= e($c['father_name'] ?? '-') ?></td>
                            <td id="cMobile_<?= $c['id'] ?>"><?= e($c['mobile'] ?? '-') ?></td>
                            <td id="cJoining_<?= $c['id'] ?>">
                                <?php if (!empty($c['joining_date'])): ?>
                                    <span class="joining-date-val"><?= date('d M Y', strtotime($c['joining_date'])) ?></span>
                                <?php else: ?>
                                    <span class="joining-date-empty">Not set</span>
                                <?php endif; ?>
                            </td>
                            <td id="cEmail_<?= $c['id'] ?>"><?= e($c['email']) ?></td>
                            <td><?= (int)$c['total_docs'] ?></td>
                            <td>
                                <?php if ($c['test_score'] !== null): ?>
                                    <span style="font-weight: 600;"><?= (int)$c['test_score'] ?>/<?= (int)($c['test_total'] ?? 15) ?></span>
                                <?php else: ?>
                                    <span style="color: var(--muted); font-style: italic;">Not taken</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                            <td style="text-align: right;">
                                <div class="action-group">
                                    <a href="<?= url('/admin/candidates/' . $c['id']) ?>" class="btn-action-view" aria-label="View profile of <?= e($c['full_name']) ?>">
                                        View
                                    </a>
                                    <button type="button" class="btn-action-edit" onclick='openEditCandidateModal(<?= json_encode([
                                        "id" => $c["id"],
                                        "full_name" => $c["full_name"],
                                        "father_name" => $c["father_name"] ?? "",
                                        "email" => $c["email"],
                                        "mobile" => $c["mobile"] ?? "",
                                        "whatsapp" => $c["whatsapp"] ?? "",
                                        "joining_date" => $c["joining_date"] ?? "",
                                        "dob" => $c["dob"] ?? "",
                                        "gender" => $c["gender"] ?? "",
                                        "city" => $c["city"] ?? "",
                                        "state" => $c["state"] ?? "",
                                        "pincode" => $c["pincode"] ?? "",
                                        "address" => $c["address"] ?? ""
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' aria-label="Edit candidate <?= e($c['full_name']) ?>">
                                        <svg width="14" height="14" aria-hidden="true"><use href="#icon-edit"></use></svg>
                                        <span>Edit</span>
                                    </button>
                                    <a href="<?= url('/admin/candidates/' . $c['id'] . '/export-pdf') ?>" class="btn-action-pdf" aria-label="Download PDF of <?= e($c['full_name']) ?>">
                                        <svg width="14" height="14" aria-hidden="true"><use href="#icon-download"></use></svg>
                                        <span>PDF</span>
                                    </a>
                                    <button type="button" class="btn-action-delete" onclick="confirmDeleteCandidate(<?= (int)$c['id'] ?>, '<?= e(addslashes($c['full_name'])) ?>')" aria-label="Delete candidate <?= e($c['full_name']) ?>">
                                        <svg width="14" height="14" aria-hidden="true"><use href="#icon-trash"></use></svg>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Rows (< 768px) -->
        <div class="candidates-mobile-list" role="list">
            <?php foreach ($candidates as $c): ?>
                <div class="candidate-mobile-card" id="candidateMobileCard_<?= $c['id'] ?>" role="listitem">
                    <div class="card-mobile-top">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <input type="checkbox" class="row-checkbox candidate-select" value="<?= $c['id'] ?>" aria-label="Select <?= e($c['full_name']) ?>">
                            <span class="card-mobile-name"><?= e($c['full_name']) ?></span>
                            <?php if (!empty($c['is_guest'])): ?>
                                <span class="guest-chip">Guest</span>
                            <?php endif; ?>
                        </div>
                        <span style="font-size: 12px; color: var(--muted);"><?= date('d M Y', strtotime($c['created_at'])) ?></span>
                    </div>

                    <div class="card-mobile-meta">
                        <div>
                            <span style="color: var(--muted); font-size: 12px;">Father:</span>
                            <div id="mFather_<?= $c['id'] ?>"><?= e($c['father_name'] ?? '-') ?></div>
                        </div>
                        <div>
                            <span style="color: var(--muted); font-size: 12px;">Mobile:</span>
                            <div id="mMobile_<?= $c['id'] ?>"><?= e($c['mobile'] ?? '-') ?></div>
                        </div>
                        <div>
                            <span style="color: var(--muted); font-size: 12px;">Joining date:</span>
                            <div id="mJoining_<?= $c['id'] ?>"><?= !empty($c['joining_date']) ? date('d M Y', strtotime($c['joining_date'])) : '<span style="color:var(--muted); font-style:italic;">Not set</span>' ?></div>
                        </div>
                        <div>
                            <span style="color: var(--muted); font-size: 12px;">Docs / Score:</span>
                            <div><?= (int)$c['total_docs'] ?> docs &bull; <?= ($c['test_score'] !== null) ? ((int)$c['test_score'] . '/' . (int)($c['test_total'] ?? 15)) : 'Not taken' ?></div>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <span style="color: var(--muted); font-size: 12px;">Email:</span>
                            <div id="mEmail_<?= $c['id'] ?>" style="word-break: break-all;"><?= e($c['email']) ?></div>
                        </div>
                    </div>

                    <div class="card-mobile-actions" style="flex-wrap: wrap;">
                        <a href="<?= url('/admin/candidates/' . $c['id']) ?>" class="btn-action-view" style="flex: 1 1 45%;" aria-label="View profile of <?= e($c['full_name']) ?>">
                            View
                        </a>
                        <button type="button" class="btn-action-edit" style="flex: 1 1 45%; justify-content: center;" onclick='openEditCandidateModal(<?= json_encode([
                            "id" => $c["id"],
                            "full_name" => $c["full_name"],
                            "father_name" => $c["father_name"] ?? "",
                            "email" => $c["email"],
                            "mobile" => $c["mobile"] ?? "",
                            "whatsapp" => $c["whatsapp"] ?? "",
                            "joining_date" => $c["joining_date"] ?? "",
                            "dob" => $c["dob"] ?? "",
                            "gender" => $c["gender"] ?? "",
                            "city" => $c["city"] ?? "",
                            "state" => $c["state"] ?? "",
                            "pincode" => $c["pincode"] ?? "",
                            "address" => $c["address"] ?? ""
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' aria-label="Edit candidate <?= e($c['full_name']) ?>">
                            <svg width="14" height="14" aria-hidden="true"><use href="#icon-edit"></use></svg>
                            <span>Edit</span>
                        </button>
                        <a href="<?= url('/admin/candidates/' . $c['id'] . '/export-pdf') ?>" class="btn-action-pdf" style="flex: 1 1 45%; justify-content: center;" aria-label="Download PDF of <?= e($c['full_name']) ?>">
                            <svg width="14" height="14" aria-hidden="true"><use href="#icon-download"></use></svg>
                            <span>Download PDF</span>
                        </a>
                        <button type="button" class="btn-action-delete" style="flex: 1 1 45%; justify-content: center;" onclick="confirmDeleteCandidate(<?= (int)$c['id'] ?>, '<?= e(addslashes($c['full_name'])) ?>')" aria-label="Delete candidate <?= e($c['full_name']) ?>">
                            <svg width="14" height="14" aria-hidden="true"><use href="#icon-trash"></use></svg>
                            <span>Delete</span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Server-side Pagination Bar -->
        <div class="pagination-bar">
            <div class="pagination-info">
                Showing <strong><?= min($total, $offset + 1) ?></strong> to <strong><?= min($total, $offset + count($candidates)) ?></strong> of <strong><?= number_format($total) ?></strong> candidates
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="pagination-pages" aria-label="Pagination Navigation">
                    <a href="<?= buildFilterUrl(['page' => max(1, $page - 1)]) ?>" class="page-btn <?= ($page <= 1) ? 'disabled' : '' ?>" aria-label="Previous page">
                        <svg width="16" height="16" aria-hidden="true"><use href="#icon-chevron-left"></use></svg>
                    </a>

                    <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
                        <a href="<?= buildFilterUrl(['page' => $p]) ?>" class="page-btn <?= ($p === $page) ? 'active' : '' ?>" aria-label="Page <?= $p ?>" <?= ($p === $page) ? 'aria-current="page"' : '' ?>>
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>

                    <a href="<?= buildFilterUrl(['page' => min($totalPages, $page + 1)]) ?>" class="page-btn <?= ($page >= $totalPages) ? 'disabled' : '' ?>" aria-label="Next page">
                        <svg width="16" height="16" aria-hidden="true"><use href="#icon-chevron-right"></use></svg>
                    </a>
                </nav>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Bulk Action Bar -->
<div class="bulk-bar" id="bulkBar" role="region" aria-label="Bulk actions bar">
    <span class="bulk-count-text" id="bulkCountText">0 selected</span>
    <form id="bulkActionForm" method="POST" style="display: flex; gap: 8px;">
        <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="candidate_ids" id="bulkCandidateIdsInput" value="">
        <button type="button" class="bulk-btn bulk-btn-primary" onclick="submitBulkAction('zip')" aria-label="Download selected candidates PDFs as ZIP">
            <svg width="16" height="16" aria-hidden="true"><use href="#icon-download"></use></svg>
            <span>Download PDFs as ZIP</span>
        </button>
        <button type="button" class="bulk-btn bulk-btn-secondary" onclick="submitBulkAction('excel')" aria-label="Export selected candidates to Excel">
            <svg width="16" height="16" aria-hidden="true"><use href="#icon-file-text"></use></svg>
            <span>Export Excel</span>
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var selectAll = document.getElementById('selectAllCheckbox');
    var checkboxes = document.querySelectorAll('.candidate-select');
    var bulkBar = document.getElementById('bulkBar');
    var bulkCountText = document.getElementById('bulkCountText');
    var bulkIdsInput = document.getElementById('bulkCandidateIdsInput');

    function updateBulkState() {
        var selected = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
        if (selected.length > 0) {
            bulkCountText.textContent = selected.length + ' candidate' + (selected.length === 1 ? '' : 's') + ' selected';
            bulkIdsInput.value = selected.join(',');
            bulkBar.classList.add('active');
        } else {
            bulkBar.classList.remove('active');
            bulkIdsInput.value = '';
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(function(cb) {
                cb.checked = selectAll.checked;
            });
            updateBulkState();
        });
    }

    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', updateBulkState);
    });
});

function submitBulkAction(action) {
    var ids = document.getElementById('bulkCandidateIdsInput').value;
    if (!ids) {
        alert('Please select at least one candidate.');
        return;
    }
    if (action === 'zip') {
        window.location.href = '<?= url("/admin/candidates/bulk-pdf-zip") ?>?ids=' + encodeURIComponent(ids);
    } else if (action === 'excel') {
        window.location.href = '<?= url("/admin/candidates/export-excel") ?>?ids=' + encodeURIComponent(ids);
    }
}

// Edit & Delete Candidate Functions
var currentDeleteId = null;

function openEditCandidateModal(data) {
    document.getElementById('editCandidateId').value = data.id || '';
    document.getElementById('editFullName').value = data.full_name || '';
    document.getElementById('editFatherName').value = data.father_name || '';
    document.getElementById('editEmail').value = data.email || '';
    document.getElementById('editMobile').value = data.mobile || '';
    document.getElementById('editWhatsApp').value = data.whatsapp || '';
    document.getElementById('editJoiningDate').value = data.joining_date || '';
    document.getElementById('editDob').value = data.dob || '';
    document.getElementById('editGender').value = data.gender || '';
    document.getElementById('editAddress').value = data.address || '';
    document.getElementById('editCity').value = data.city || '';
    document.getElementById('editState').value = data.state || '';
    document.getElementById('editPincode').value = data.pincode || '';
    document.getElementById('editFormError').style.display = 'none';
    document.getElementById('editCandidateModal').classList.add('active');
}

function closeEditCandidateModal() {
    document.getElementById('editCandidateModal').classList.remove('active');
}

function handleSaveCandidate(event) {
    event.preventDefault();
    var form = document.getElementById('editCandidateForm');
    var cid = document.getElementById('editCandidateId').value;
    var btn = document.getElementById('btnSaveCandidate');
    var errEl = document.getElementById('editFormError');

    btn.disabled = true;
    btn.textContent = 'Saving...';
    errEl.style.display = 'none';

    var fd = new FormData(form);

    fetch('<?= url("/admin/candidates/") ?>' + cid + '/update', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: fd
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        btn.disabled = false;
        btn.textContent = 'Save changes';
        if (res.success) {
            closeEditCandidateModal();
            // Update table row in real-time
            if (document.getElementById('cName_' + cid)) {
                document.getElementById('cName_' + cid).textContent = res.candidate.full_name;
            }
            if (document.getElementById('cFather_' + cid)) {
                document.getElementById('cFather_' + cid).textContent = res.candidate.father_name || '-';
            }
            if (document.getElementById('cEmail_' + cid)) {
                document.getElementById('cEmail_' + cid).textContent = res.candidate.email;
            }
            if (document.getElementById('cMobile_' + cid)) {
                document.getElementById('cMobile_' + cid).textContent = res.candidate.mobile || '-';
            }
            if (document.getElementById('cJoining_' + cid)) {
                var jEl = document.getElementById('cJoining_' + cid);
                if (res.candidate.joining_date) {
                    jEl.innerHTML = '<span class="joining-date-val">' + res.candidate.joining_date_formatted + '</span>';
                } else {
                    jEl.innerHTML = '<span class="joining-date-empty">Not set</span>';
                }
            }
            if (document.getElementById('mJoining_' + cid)) {
                document.getElementById('mJoining_' + cid).textContent = res.candidate.joining_date_formatted || 'Not set';
            }
        } else {
            errEl.textContent = res.error || 'Failed to save changes.';
            errEl.style.display = 'block';
        }
    })
    .catch(function(err) {
        btn.disabled = false;
        btn.textContent = 'Save changes';
        errEl.textContent = 'Network or server error.';
        errEl.style.display = 'block';
    });
}

function confirmDeleteCandidate(id, name) {
    currentDeleteId = id;
    document.getElementById('deleteCandidateName').textContent = name;
    document.getElementById('deleteModalError').style.display = 'none';
    document.getElementById('deleteCandidateModal').classList.add('active');
}

function closeDeleteCandidateModal() {
    document.getElementById('deleteCandidateModal').classList.remove('active');
    currentDeleteId = null;
}

function executeDeleteCandidate() {
    if (!currentDeleteId) return;
    var btn = document.getElementById('btnConfirmDelete');
    var errEl = document.getElementById('deleteModalError');

    btn.disabled = true;
    btn.textContent = 'Deleting...';
    errEl.style.display = 'none';

    var fd = new FormData();
    fd.append('_csrf_token', '<?= csrf_token() ?>');

    fetch('<?= url("/admin/candidates/") ?>' + currentDeleteId + '/delete', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: fd
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        btn.disabled = false;
        btn.textContent = 'Delete candidate';
        if (res.success) {
            closeDeleteCandidateModal();
            var row = document.getElementById('candidateRow_' + currentDeleteId);
            if (row) {
                row.style.transition = 'opacity 0.3s ease';
                row.style.opacity = '0';
                setTimeout(function() { row.remove(); }, 300);
            }
            var mCard = document.getElementById('candidateMobileCard_' + currentDeleteId);
            if (mCard) {
                mCard.style.transition = 'opacity 0.3s ease';
                mCard.style.opacity = '0';
                setTimeout(function() { mCard.remove(); }, 300);
            }
        } else {
            errEl.textContent = res.error || 'Failed to delete candidate.';
            errEl.style.display = 'block';
        }
    })
    .catch(function(err) {
        btn.disabled = false;
        btn.textContent = 'Delete candidate';
        errEl.textContent = 'Network or server error.';
        errEl.style.display = 'block';
    });
}
</script>

<!-- Edit Candidate Details Modal -->
<div class="modal-overlay" id="editCandidateModal" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
    <div class="modal-dialog">
        <div class="modal-header">
            <h2 class="modal-title" id="editModalTitle">Edit candidate details</h2>
            <button type="button" class="modal-close-btn" onclick="closeEditCandidateModal()" aria-label="Close edit dialog">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-x"></use></svg>
            </button>
        </div>
        <form id="editCandidateForm" onsubmit="handleSaveCandidate(event)">
            <?= csrf_field() ?>
            <input type="hidden" name="candidate_id" id="editCandidateId">

            <div class="modal-body">
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="editFullName">Full legal name *</label>
                        <input type="text" name="full_name" id="editFullName" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="editFatherName">Father's name</label>
                        <input type="text" name="father_name" id="editFatherName" class="form-input">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="editEmail">Email address *</label>
                        <input type="email" name="email" id="editEmail" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="editMobile">Mobile number</label>
                        <input type="tel" name="mobile" id="editMobile" class="form-input">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="editWhatsApp">WhatsApp number</label>
                        <input type="tel" name="whatsapp" id="editWhatsApp" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="editJoiningDate">Joining date</label>
                        <input type="date" name="joining_date" id="editJoiningDate" class="form-input">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="editDob">Date of birth</label>
                        <input type="date" name="dob" id="editDob" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="editGender">Gender</label>
                        <select name="gender" id="editGender" class="form-select">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="editAddress">Address</label>
                    <textarea name="address" id="editAddress" class="form-textarea" placeholder="Street address"></textarea>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="editCity">City</label>
                        <input type="text" name="city" id="editCity" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="editState">State</label>
                        <input type="text" name="state" id="editState" class="form-input">
                    </div>
                </div>

                <div class="form-group" style="max-width: 280px;">
                    <label class="form-label" for="editPincode">Pincode</label>
                    <input type="text" name="pincode" id="editPincode" class="form-input">
                </div>

                <div id="editFormError" style="display:none; color: var(--danger); font-size: 13px; margin-top: 8px;"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" id="btnCancelEdit" onclick="closeEditCandidateModal()">Cancel</button>
                <button type="button" class="btn-modal-save" id="btnSaveCandidate" onclick="handleSaveCandidate(event)">Save changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Candidate Confirmation Modal -->
<div class="modal-overlay" id="deleteCandidateModal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
    <div class="modal-dialog" style="max-width: 440px;">
        <div class="modal-header">
            <h2 class="modal-title" id="deleteModalTitle">Delete candidate</h2>
            <button type="button" class="modal-close-btn" onclick="closeDeleteCandidateModal()" aria-label="Close delete confirmation">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-x"></use></svg>
            </button>
        </div>
        <div class="modal-body">
            <p style="font-size: 14px; color: var(--text); line-height: 1.5; margin: 0 0 12px 0;">
                Are you sure you want to delete <strong id="deleteCandidateName"></strong>?
            </p>
            <p style="font-size: 13px; color: var(--muted); margin: 0;">
                This will soft-delete (archive) the candidate profile. Data and documents will be preserved in the audit archive.
            </p>
            <div id="deleteModalError" style="display:none; color: var(--danger); font-size: 13px; margin-top: 10px;"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal-cancel" id="btnCancelDelete" onclick="closeDeleteCandidateModal()">Cancel</button>
            <button type="button" class="btn-modal-delete" id="btnConfirmDelete" onclick="executeDeleteCandidate()">Delete candidate</button>
        </div>
    </div>
</div>


<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/admin.php';
?>
