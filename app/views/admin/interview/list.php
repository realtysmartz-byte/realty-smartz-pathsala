<?php
ob_start();
$pageTitle = "Interview Candidates | Realty Smartz Pathshala";
$viewTitle = "Interview Candidates";
?>

<style>
    /* Header row */
    .top-action-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .top-left-group {
        display: flex;
        align-items: baseline;
        gap: 12px;
        flex-wrap: wrap;
    }
    .top-left-group h2 {
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.02em;
        margin: 0;
    }
    .last-updated-text {
        font-size: 13px;
        color: var(--muted);
    }
    .top-right-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-action-outline {
        height: 38px;
        padding: 0 14px;
        background: transparent;
        color: var(--text);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.15s ease;
    }
    .btn-action-outline:hover {
        background-color: var(--surface-2);
        color: var(--text);
    }

    .btn-action-primary {
        height: 38px;
        padding: 0 16px;
        background: var(--brand);
        color: #FFFFFF;
        border: none;
        border-radius: var(--r-md);
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.15s ease;
    }
    .btn-action-primary:hover {
        background-color: var(--brand-hover);
        color: #FFFFFF;
    }

    /* Filter Chips */
    .filter-chips-row {
        display: flex;
        gap: 8px;
        margin-bottom: 18px;
        overflow-x: auto;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
    }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 36px;
        padding: 0 16px;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-full);
        font-size: 13.5px;
        font-weight: 500;
        color: var(--text);
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.15s ease;
    }
    .filter-chip:hover {
        background: var(--surface-2);
        color: var(--text);
    }
    .filter-chip.active {
        background: var(--brand);
        color: #FFFFFF;
        border-color: var(--brand);
        font-weight: 600;
    }
    .chip-count {
        padding: 2px 8px;
        border-radius: var(--r-full);
        font-size: 11.5px;
        font-weight: 700;
        background: var(--surface-2);
        color: var(--text);
    }
    .filter-chip.active .chip-count {
        background: rgba(255, 255, 255, 0.25);
        color: #FFFFFF;
    }

    /* Filters Bar */
    .filter-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        padding: 16px 20px;
        margin-bottom: 20px;
    }
    .filter-form-grid {
        display: grid;
        grid-template-columns: 2fr 1.2fr 1fr 1fr 1.2fr auto;
        gap: 12px;
        align-items: center;
    }
    @media (max-width: 1024px) {
        .filter-form-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    @media (max-width: 600px) {
        .filter-form-grid {
            grid-template-columns: 1fr;
        }
    }
    .filter-input {
        height: 40px;
        width: 100%;
        font-size: 14px;
        font-family: inherit;
        padding: 0 12px;
        border: 1px solid var(--line);
        border-radius: var(--r-sm);
        background: #FFFFFF;
        color: var(--text);
        outline: none;
    }
    .filter-input:focus {
        border-color: var(--brand);
    }

    /* Floating New Submissions Pill */
    .new-pill-banner {
        display: none;
        position: sticky;
        top: 76px;
        z-index: 25;
        margin-bottom: 14px;
        text-align: center;
    }
    .btn-new-pill {
        background: var(--brand);
        color: #FFFFFF;
        border: none;
        padding: 8px 18px;
        border-radius: var(--r-full);
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(31, 92, 74, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        animation: bounce 1.2s infinite alternate;
    }
    @keyframes bounce {
        from { transform: translateY(0); }
        to { transform: translateY(-3px); }
    }

    /* Table & Stacked Rows */
    .table-responsive-wrapper {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        overflow: hidden;
        margin-bottom: 24px;
    }
    .interview-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        text-align: left;
    }
    .interview-table th {
        background: var(--surface-2);
        color: var(--text);
        font-weight: 600;
        padding: 12px 14px;
        font-size: 13px;
        border-bottom: 1px solid var(--line);
        white-space: nowrap;
    }
    .interview-table td {
        padding: 14px;
        border-bottom: 1px solid var(--line);
        vertical-align: middle;
        color: var(--text);
    }
    .interview-table tbody tr {
        cursor: pointer;
        transition: background-color 0.12s ease;
    }
    .interview-table tbody tr:hover {
        background-color: rgba(31, 92, 74, 0.03);
    }
    .interview-table tbody tr.new-highlight {
        animation: highlightFade 3s ease forwards;
    }
    @keyframes highlightFade {
        0% { background-color: #E8F1EE; }
        100% { background-color: transparent; }
    }

    /* Decision Pill Badges */
    .pill-decision {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: var(--r-full);
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }
    .pill-pending {
        background: #FEF3D6;
        color: #9A6410;
    }
    .pill-selected {
        background: #E8F5E9;
        color: #226B35;
    }
    .pill-rejected {
        background: #FDF2F2;
        color: #B3261E;
    }

    .pill-feedback {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: var(--r-full);
        font-size: 11.5px;
        font-weight: 600;
    }
    .feedback-filled {
        background: var(--brand-soft);
        color: var(--brand);
    }
    .feedback-pending {
        background: var(--surface-2);
        color: var(--muted);
    }

    .new-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: var(--brand);
        display: inline-block;
        margin-right: 6px;
    }

    .candidate-name-cell {
        font-weight: 500;
    }
    .unopened-row .candidate-name-cell {
        font-weight: 700;
    }

    /* Mobile Stacked Cards */
    @media (max-width: 767.98px) {
        .interview-table, .interview-table thead, .interview-table tbody, .interview-table tr, .interview-table td {
            display: block;
            width: 100%;
        }
        .interview-table thead {
            display: none;
        }
        .interview-table tbody tr {
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            margin: 12px;
            padding: 16px;
            background: #FFFFFF;
        }
        .interview-table td {
            padding: 6px 0;
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13.5px;
        }
        .interview-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--muted);
            font-size: 12.5px;
        }
        .mobile-main-header {
            border-bottom: 1px solid var(--line) !important;
            padding-bottom: 10px !important;
            margin-bottom: 8px;
        }
    }

    /* Empty State */
    .empty-state {
        padding: 56px 20px;
        text-align: center;
        color: var(--muted);
    }
    .empty-state svg {
        margin-bottom: 14px;
        color: var(--muted);
    }
    .empty-state p {
        font-size: 16px;
        font-weight: 500;
        margin: 0;
    }

    /* Toast Notification */
    .quiet-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: var(--text);
        color: #FFFFFF;
        padding: 12px 20px;
        border-radius: var(--r-md);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        font-size: 14px;
        font-weight: 500;
        z-index: 1000;
        display: none;
        align-items: center;
        gap: 10px;
        animation: slideUp 0.25s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    /* Automation Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(38, 51, 45, 0.45);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1100;
        backdrop-filter: blur(2px);
        padding: 16px;
    }
    .modal-dialog {
        background: #FFFFFF;
        border-radius: var(--r-lg);
        border: 1px solid var(--line);
        width: 100%;
        max-width: 640px;
        max-height: 90vh;
        overflow-y: auto;
        padding: 28px;
        box-shadow: 0 12px 36px rgba(0,0,0,0.12);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid var(--line);
        padding-bottom: 12px;
    }
    .modal-header h3 {
        font-size: 20px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }
    .modal-close-btn {
        background: transparent;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: var(--muted);
    }
    .setting-group {
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px dashed var(--line);
    }
    .setting-group:last-child {
        border-bottom: none;
    }
    .setting-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 6px;
    }
    .setting-desc {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 8px;
    }
</style>

<div class="top-action-bar">
    <div class="top-left-group">
        <h2>Interview Candidates</h2>
        <span class="last-updated-text" id="updatedText">Updated just now</span>
    </div>
    <div class="top-right-group">
        <button type="button" class="btn-action-outline" id="btnRefresh" onclick="manualRefresh()" aria-label="Refresh interview list">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="refreshIcon"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
            <span id="refreshLabel">Refresh</span>
        </button>

        <a href="<?= url('/admin/interview-candidates/export?' . http_build_query($_GET)) ?>" class="btn-action-outline" aria-label="Export interview list">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            <span>Export Excel/CSV</span>
        </a>

        <?php if (auth()['role'] !== 'reviewer'): ?>
        <button type="button" class="btn-action-primary" onclick="openAutomationModal()" aria-label="Open automation settings">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            <span>Automation</span>
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Filter Chips with Live Counts -->
<div class="filter-chips-row">
    <?php
    $curDecision = $filters['decision'] ?? '';
    $chipAllUrl = url('/admin/interview-candidates?' . http_build_query(array_merge($filters, ['decision' => ''])));
    $chipPendUrl = url('/admin/interview-candidates?' . http_build_query(array_merge($filters, ['decision' => 'pending'])));
    $chipSelUrl = url('/admin/interview-candidates?' . http_build_query(array_merge($filters, ['decision' => 'selected'])));
    $chipRejUrl = url('/admin/interview-candidates?' . http_build_query(array_merge($filters, ['decision' => 'rejected'])));
    ?>
    <a href="<?= $chipAllUrl ?>" class="filter-chip <?= ($curDecision === '') ? 'active' : '' ?>">
        <span>All</span>
        <span class="chip-count" id="count_all"><?= $counts['all'] ?></span>
    </a>
    <a href="<?= $chipPendUrl ?>" class="filter-chip <?= ($curDecision === 'pending') ? 'active' : '' ?>">
        <span>Pending</span>
        <span class="chip-count" id="count_pending"><?= $counts['pending'] ?></span>
    </a>
    <a href="<?= $chipSelUrl ?>" class="filter-chip <?= ($curDecision === 'selected') ? 'active' : '' ?>">
        <span>Selected</span>
        <span class="chip-count" id="count_selected"><?= $counts['selected'] ?></span>
    </a>
    <a href="<?= $chipRejUrl ?>" class="filter-chip <?= ($curDecision === 'rejected') ? 'active' : '' ?>">
        <span>Rejected</span>
        <span class="chip-count" id="count_rejected"><?= $counts['rejected'] ?></span>
    </a>
</div>

<!-- Filters Bar -->
<div class="filter-card">
    <form method="GET" action="<?= url('/admin/interview-candidates') ?>" id="filterForm">
        <?php if (!empty($filters['decision'])): ?>
            <input type="hidden" name="decision" value="<?= e($filters['decision']) ?>">
        <?php endif; ?>
        <div class="filter-form-grid">
            <input type="text" name="search" class="filter-input" placeholder="Search name, mobile, email, post..." value="<?= e($filters['search']) ?>">

            <select name="hr_name" class="filter-input">
                <option value="">All HR Names</option>
                <?php foreach ($hrNames as $hn): ?>
                    <option value="<?= e($hn) ?>" <?= ($filters['hr_name'] === $hn) ? 'selected' : '' ?>><?= e($hn) ?></option>
                <?php endforeach; ?>
            </select>

            <select name="experience_type" class="filter-input">
                <option value="">Exp / Fresher</option>
                <option value="experience" <?= ($filters['experience_type'] === 'experience') ? 'selected' : '' ?>>Experience</option>
                <option value="fresher" <?= ($filters['experience_type'] === 'fresher') ? 'selected' : '' ?>>Fresher</option>
            </select>

            <select name="immediate_joiner" class="filter-input">
                <option value="">Immediate</option>
                <option value="yes" <?= ($filters['immediate_joiner'] === 'yes') ? 'selected' : '' ?>>Immediate (Yes)</option>
                <option value="no" <?= ($filters['immediate_joiner'] === 'no') ? 'selected' : '' ?>>Immediate (No)</option>
            </select>

            <select name="feedback_status" class="filter-input">
                <option value="">Feedback</option>
                <option value="pending" <?= ($filters['feedback_status'] === 'pending') ? 'selected' : '' ?>>Feedback Pending</option>
                <option value="filled" <?= ($filters['feedback_status'] === 'filled') ? 'selected' : '' ?>>Feedback Filled</option>
            </select>

            <div style="display: flex; gap: 6px;">
                <button type="submit" class="btn-action-primary" style="height: 40px; padding: 0 16px;">Filter</button>
                <a href="<?= url('/admin/interview-candidates') ?>" class="btn-action-outline" style="height: 40px; padding: 0 12px;" title="Reset filters">Reset</a>
            </div>
        </div>
    </form>
</div>

<!-- Floating "N new, show" pill for scrolled/filtered users -->
<div class="new-pill-banner" id="newPillBanner">
    <button type="button" class="btn-new-pill" onclick="showNewSubmissionsNow()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"></polyline></svg>
        <span id="newPillText">1 new form, show</span>
    </button>
</div>

<!-- Main Applications Table -->
<div class="table-responsive-wrapper">
    <table class="interview-table" id="interviewTable" aria-label="Interview Candidate Applications">
        <thead>
            <tr>
                <th>Name</th>
                <th>Post applied</th>
                <th>Mobile</th>
                <th>HR name</th>
                <th>Experience / Fresher</th>
                <th>Submitted</th>
                <th>Decision</th>
                <th>Offered salary</th>
                <th>Joining date</th>
                <th>Feedback</th>
            </tr>
        </thead>
        <tbody id="interviewTableBody">
            <?php if (empty($result['items'])): ?>
                <tr id="emptyRow">
                    <td colspan="10">
                        <div class="empty-state">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <p>No interview forms yet</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($result['items'] as $item): 
                    $isUnopened = empty($item['first_viewed_at']);
                    $hasFeedback = (!empty(trim((string)$item['feedback_hr'])) || !empty(trim((string)$item['feedback_final'])));
                    $exactTime = date('d M Y, h:i A', strtotime($item['submitted_at']));
                    $timeAgo = time_ago($item['submitted_at']);
                ?>
                <tr class="<?= $isUnopened ? 'unopened-row' : '' ?>" data-id="<?= $item['id'] ?>" onclick="window.location.href='<?= url('/admin/interview-candidates/' . $item['id']) ?>'">
                    <td data-label="Name" class="mobile-main-header candidate-name-cell">
                        <?php if ($isUnopened): ?>
                            <span class="new-dot" title="New application"></span>
                        <?php endif; ?>
                        <?= e($item['full_name']) ?>
                    </td>
                    <td data-label="Post applied"><?= e($item['post_applied']) ?></td>
                    <td data-label="Mobile"><?= e($item['mobile']) ?></td>
                    <td data-label="HR name"><?= e($item['hr_name']) ?></td>
                    <td data-label="Experience / Fresher"><?= ucfirst(e($item['experience_type'])) ?></td>
                    <td data-label="Submitted" title="<?= e($exactTime) ?>"><?= e($timeAgo) ?></td>
                    <td data-label="Decision">
                        <?php if ($item['decision'] === 'selected'): ?>
                            <span class="pill-decision pill-selected">Selected</span>
                        <?php elseif ($item['decision'] === 'rejected'): ?>
                            <span class="pill-decision pill-rejected">Rejected</span>
                        <?php else: ?>
                            <span class="pill-decision pill-pending">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Offered salary">
                        <?= $item['offered_salary'] ? '₹' . number_format((float)$item['offered_salary'], 0) : '-' ?>
                    </td>
                    <td data-label="Joining date">
                        <?= $item['joining_date'] ? date('d M Y', strtotime($item['joining_date'])) : '-' ?>
                    </td>
                    <td data-label="Feedback">
                        <?php if ($hasFeedback): ?>
                            <span class="pill-feedback feedback-filled">Filled</span>
                        <?php else: ?>
                            <span class="pill-feedback feedback-pending">Pending</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Server-side Pagination -->
<?php if ($result['total_pages'] > 1): ?>
<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; flex-wrap: wrap; gap: 12px;">
    <div style="font-size: 13.5px; color: var(--muted);">
        Showing <?= count($result['items']) ?> of <?= $result['total'] ?> interview forms
    </div>
    <div style="display: flex; gap: 6px;">
        <?php for ($p = 1; $p <= $result['total_pages']; $p++): ?>
            <a href="<?= url('/admin/interview-candidates?' . http_build_query(array_merge($filters, ['page' => $p]))) ?>" 
               class="btn-action-outline" 
               style="<?= ($p === $result['page']) ? 'background:var(--brand); color:#FFFFFF; border-color:var(--brand);' : '' ?> min-width: 38px; justify-content: center;">
                <?= $p ?>
            </a>
        <?php endfor; ?>
    </div>
</div>
<?php endif; ?>

<!-- Quiet Toast -->
<div class="quiet-toast" id="quietToast" role="alert">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
    <span id="toastMsg">New form received</span>
</div>

<!-- Automation Settings Modal (B5) -->
<div class="modal-overlay" id="automationModal" onclick="if(event.target===this) closeAutomationModal()">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3>Interview Automation Settings</h3>
            <button type="button" class="modal-close-btn" onclick="closeAutomationModal()" aria-label="Close modal">&times;</button>
        </div>

        <form method="POST" action="<?= url('/admin/interview/settings') ?>">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <!-- 1. HR Alert on New Form -->
            <div class="setting-group">
                <label class="setting-label">
                    <input type="checkbox" name="interview_email_hr_new_form" value="1" <?= Setting::get('interview_email_hr_new_form', 1) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--brand);">
                    <span>Email HR team when a new form is submitted</span>
                </label>
                <p class="setting-desc">Sends candidate name, post applied, mobile and a direct link to the record.</p>
                <div style="margin-top: 8px;">
                    <label style="font-size: 12.5px; font-weight: 600; color: var(--muted); display: block; margin-bottom: 4px;">Recipients (comma separated):</label>
                    <input type="text" name="interview_email_hr_recipients" class="filter-input" value="<?= e(Setting::get('interview_email_hr_recipients', 'hr@realtysmartzpathshala.in')) ?>" placeholder="hr@realtysmartzpathshala.in, manager@realtysmartzpathshala.in">
                </div>
            </div>

            <!-- 2. Candidate Selected Email -->
            <div class="setting-group">
                <label class="setting-label">
                    <input type="checkbox" name="interview_email_candidate_selected" value="1" <?= Setting::get('interview_email_candidate_selected', 0) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--brand);">
                    <span>Email candidate when Selected</span>
                </label>
                <p class="setting-desc">Includes position offered and joining date (never the salary). 24h deduplicated.</p>
                <div style="margin-top: 8px;">
                    <label style="font-size: 12.5px; font-weight: 600; color: var(--muted); display: block; margin-bottom: 4px;">Email Subject:</label>
                    <input type="text" name="interview_email_selected_subject" class="filter-input" value="<?= e(Setting::get('interview_email_selected_subject', 'Offer of Employment | Realty Smartz Pathshala')) ?>">
                </div>
                <div style="margin-top: 8px;">
                    <label style="font-size: 12.5px; font-weight: 600; color: var(--muted); display: block; margin-bottom: 4px;">Template Body ({name}, {position}, {joining_date}):</label>
                    <textarea name="interview_email_selected_body" class="filter-input" style="height: 80px; padding: 8px 12px;"><?= e(Setting::get('interview_email_selected_body', "Dear {name},\n\nWe are pleased to inform you that you have been selected for the position of {position} at Realty Smartz Pathshala.\n\nDate of Joining: {joining_date}.")) ?></textarea>
                </div>
            </div>

            <!-- 3. Candidate Rejected Email -->
            <div class="setting-group">
                <label class="setting-label">
                    <input type="checkbox" name="interview_email_candidate_rejected" value="1" <?= Setting::get('interview_email_candidate_rejected', 0) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--brand);">
                    <span>Email candidate when Rejected</span>
                </label>
                <p class="setting-desc">Short, polite acknowledgement without rejection reasons. 24h deduplicated.</p>
                <div style="margin-top: 8px;">
                    <label style="font-size: 12.5px; font-weight: 600; color: var(--muted); display: block; margin-bottom: 4px;">Email Subject:</label>
                    <input type="text" name="interview_email_rejected_subject" class="filter-input" value="<?= e(Setting::get('interview_email_rejected_subject', 'Application Update | Realty Smartz Pathshala')) ?>">
                </div>
                <div style="margin-top: 8px;">
                    <label style="font-size: 12.5px; font-weight: 600; color: var(--muted); display: block; margin-bottom: 4px;">Template Body ({name}, {position}):</label>
                    <textarea name="interview_email_rejected_body" class="filter-input" style="height: 70px; padding: 8px 12px;"><?= e(Setting::get('interview_email_rejected_body', "Dear {name},\n\nThank you for attending the interview at Realty Smartz Pathshala. We appreciate your interest; however, we have chosen to proceed with other candidates at this time.")) ?></textarea>
                </div>
            </div>

            <!-- Test Email Section -->
            <div style="background: var(--surface-2); padding: 14px; border-radius: var(--r-md); margin-bottom: 20px;">
                <label style="font-size: 13px; font-weight: 600; color: var(--text); display: block; margin-bottom: 6px;">Send a Test Email</label>
                <div style="display: flex; gap: 8px;">
                    <input type="email" id="testEmailInput" placeholder="test@realtysmartzpathshala.in" class="filter-input" style="background:#FFFFFF;">
                    <button type="button" class="btn-action-outline" style="background:#FFFFFF; white-space:nowrap;" onclick="sendTestAutomationEmail()">Send Test</button>
                </div>
                <span id="testEmailStatus" style="font-size: 12px; margin-top: 4px; display: block;"></span>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-action-outline" onclick="closeAutomationModal()">Cancel</button>
                <button type="submit" class="btn-action-primary">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<script>
let lastCursor = '<?= date('Y-m-d H:i:s') ?>';
let lastRefreshTime = Date.now();
let pollInterval = 10000;
let pollTimer = null;
let queuedNewRows = [];
let isTabVisible = !document.hidden;

// Relative time "Updated just now / 2 min ago" ticker
setInterval(() => {
    const elapsedSec = Math.floor((Date.now() - lastRefreshTime) / 1000);
    const updatedEl = document.getElementById('updatedText');
    if (elapsedSec < 30) {
        updatedEl.textContent = 'Updated just now';
    } else if (elapsedSec < 90) {
        updatedEl.textContent = 'Updated 1 min ago';
    } else {
        const mins = Math.floor(elapsedSec / 60);
        updatedEl.textContent = `Updated ${mins} min ago`;
    }
}, 5000);

// Document visibility tracking
document.addEventListener('visibilitychange', () => {
    isTabVisible = !document.hidden;
    if (isTabVisible) {
        pollUpdates();
    }
});

// Polling loop (every 10s, backs off to 60s on failure)
function startPolling() {
    clearTimeout(pollTimer);
    pollTimer = setTimeout(async () => {
        if (isTabVisible) {
            await pollUpdates();
        }
        startPolling();
    }, pollInterval);
}

async function pollUpdates() {
    try {
        const res = await fetch(`<?= url('/api/interview/updates') ?>?since=${encodeURIComponent(lastCursor)}`, {
            headers: { 'Accept': 'application/json' }
        });
        if (!res.ok) throw new Error('Poll failed');
        const json = await res.json();
        if (json.success && json.data) {
            pollInterval = 10000; // Reset backoff
            handleLiveUpdates(json.data);
            if (json.data.server_timestamp) {
                lastCursor = json.data.server_timestamp;
            }
        }
    } catch (e) {
        pollInterval = 60000; // Back off to 60s
    }
}

function handleLiveUpdates(data) {
    // 1. Update filter chips live counts
    if (data.counts) {
        document.getElementById('count_all').textContent = data.counts.all;
        document.getElementById('count_pending').textContent = data.counts.pending;
        document.getElementById('count_selected').textContent = data.counts.selected;
        document.getElementById('count_rejected').textContent = data.counts.rejected;
    }

    // 2. Update sidebar badge
    const badge = document.getElementById('interviewSidebarBadge');
    if (badge && data.unopened !== undefined) {
        if (data.unopened > 0) {
            badge.textContent = data.unopened;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }

    // 3. Process new or changed rows
    const rows = data.rows || [];
    if (rows.length === 0) return;

    // Check if user has scrolled down or applied search/filters
    const isScrolledDown = window.scrollY > 150;
    const hasFilter = Boolean('<?= !empty($filters['search']) || !empty($filters['hr_name']) || !empty($filters['experience_type']) ?>');

    if (isScrolledDown || hasFilter) {
        // Show floating "N new, show" pill
        queuedNewRows.push(...rows);
        const pill = document.getElementById('newPillBanner');
        document.getElementById('newPillText').textContent = `${queuedNewRows.length} new form${queuedNewRows.length > 1 ? 's' : ''}, show`;
        pill.style.display = 'block';
    } else {
        // Insert new submissions at top with animation
        renderNewRows(rows);
    }

    // Quiet toast
    const latest = rows[0];
    showToast(`New form: ${latest.full_name}`);
}

function renderNewRows(rows) {
    const tbody = document.getElementById('interviewTableBody');
    const emptyRow = document.getElementById('emptyRow');
    if (emptyRow) emptyRow.remove();

    rows.forEach(item => {
        // Check if row already exists in table
        const existing = tbody.querySelector(`tr[data-id="${item.id}"]`);
        if (existing) {
            existing.remove();
        }

        const tr = document.createElement('tr');
        tr.className = 'unopened-row new-highlight';
        tr.setAttribute('data-id', item.id);
        tr.onclick = () => window.location.href = `<?= url('/admin/interview-candidates/') ?>${item.id}`;

        const decPill = (item.decision === 'selected') ? '<span class="pill-decision pill-selected">Selected</span>' :
                        (item.decision === 'rejected') ? '<span class="pill-decision pill-rejected">Rejected</span>' :
                        '<span class="pill-decision pill-pending">Pending</span>';

        const fbFilled = Boolean(item.feedback_hr || item.feedback_final);
        const fbPill = fbFilled ? '<span class="pill-feedback feedback-filled">Filled</span>' : '<span class="pill-feedback feedback-pending">Pending</span>';

        tr.innerHTML = `
            <td data-label="Name" class="mobile-main-header candidate-name-cell">
                <span class="new-dot" title="New application"></span>
                ${escapeHtml(item.full_name)}
            </td>
            <td data-label="Post applied">${escapeHtml(item.post_applied)}</td>
            <td data-label="Mobile">${escapeHtml(item.mobile)}</td>
            <td data-label="HR name">${escapeHtml(item.hr_name)}</td>
            <td data-label="Experience / Fresher">${capitalize(item.experience_type)}</td>
            <td data-label="Submitted" title="${item.submitted_at}">Just now</td>
            <td data-label="Decision">${decPill}</td>
            <td data-label="Offered salary">${item.offered_salary ? '₹' + Number(item.offered_salary).toLocaleString('en-IN') : '-'}</td>
            <td data-label="Joining date">${item.joining_date ? item.joining_date : '-'}</td>
            <td data-label="Feedback">${fbPill}</td>
        `;
        tbody.prepend(tr);
    });
}

function showNewSubmissionsNow() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    document.getElementById('newPillBanner').style.display = 'none';
    if (queuedNewRows.length > 0) {
        renderNewRows(queuedNewRows);
        queuedNewRows = [];
    } else {
        manualRefresh();
    }
}

function showToast(text) {
    const toast = document.getElementById('quietToast');
    document.getElementById('toastMsg').textContent = text;
    toast.style.display = 'inline-flex';
    setTimeout(() => { toast.style.display = 'none'; }, 4000);
}

function manualRefresh() {
    const icon = document.getElementById('refreshIcon');
    const label = document.getElementById('refreshLabel');
    icon.style.animation = 'spin 0.6s linear infinite';
    label.textContent = 'Loading...';

    window.location.reload();
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function capitalize(s) {
    if (!s) return '';
    return s.charAt(0).toUpperCase() + s.slice(1);
}

// Modal handling
function openAutomationModal() {
    document.getElementById('automationModal').style.display = 'flex';
}
function closeAutomationModal() {
    document.getElementById('automationModal').style.display = 'none';
}

async function sendTestAutomationEmail() {
    const em = document.getElementById('testEmailInput').value.trim();
    const st = document.getElementById('testEmailStatus');
    if (!em) {
        st.textContent = 'Enter an email first.';
        st.style.color = 'var(--danger)';
        return;
    }
    st.textContent = 'Sending...';
    st.style.color = 'var(--muted)';

    try {
        const fd = new FormData();
        fd.append('action', 'send_test_email');
        fd.append('test_email', em);
        fd.append('csrf_token', '<?= csrf_token() ?>');

        const res = await fetch('<?= url('/admin/interview/settings') ?>', {
            method: 'POST',
            body: fd
        });
        const json = await res.json();
        if (json.success) {
            st.textContent = json.message;
            st.style.color = '#226B35';
        } else {
            st.textContent = json.error || 'Failed to send.';
            st.style.color = 'var(--danger)';
        }
    } catch(e) {
        st.textContent = 'Error dispatching test email.';
        st.style.color = 'var(--danger)';
    }
}

// Start polling on load
startPolling();
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/admin.php';
?>
