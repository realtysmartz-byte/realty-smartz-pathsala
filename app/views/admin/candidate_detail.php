<?php
ob_start();
$pageTitle = e($candidate['full_name']) . " | Candidate Profile";
$viewTitle = $candidate['full_name'];

$profile = $candidate['profile'] ?? [];
$social = $profile['social'] ?? [];
$test = $candidate['test_attempt'] ?? null;
$docs = $candidate['documents'] ?? [];

function formatFileSize(?int $bytes): string {
    if (!$bytes || $bytes <= 0) return '0 KB';
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 1) . ' MB';
    }
    return number_format($bytes / 1024, 0) . ' KB';
}
?>

<style>
    /* -------------------------------------------------------------
       5. CANDIDATE PROFILE PAGE STYLES
       ------------------------------------------------------------- */
    
    /* Header Card */
    .profile-header-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        padding: 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
    }

    .profile-identity-group {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        min-width: 0;
        flex: 1;
    }

    .profile-avatar-large {
        width: 64px;
        height: 64px;
        border-radius: var(--r-full);
        background: var(--brand-soft);
        color: var(--brand);
        font-size: 24px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .profile-identity-details {
        min-width: 0;
        flex: 1;
    }

    .profile-name-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 6px;
    }

    .profile-name {
        font-size: 24px;
        font-weight: 600;
        color: var(--text);
        letter-spacing: -0.02em;
        margin: 0;
        line-height: 1.2;
    }

    .chip-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        font-size: 12px;
        font-weight: 600;
        border-radius: var(--r-sm);
        background: var(--bg);
        color: var(--muted);
        border: 1px solid var(--line);
    }

    .profile-email-line {
        font-size: 14px;
        color: var(--text);
        overflow-wrap: anywhere;
        margin-bottom: 8px;
    }

    .profile-numbers-row {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }

    .number-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13.5px;
        font-weight: 500;
        color: var(--brand);
        text-decoration: none;
        white-space: nowrap;
    }

    .number-link:hover {
        text-decoration: underline;
    }

    /* Progress bar */
    .completion-bar-wrap {
        max-width: 280px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .completion-text {
        font-size: 12px;
        font-weight: 500;
        color: var(--muted);
    }

    .completion-progress {
        width: 100%;
        height: 4px;
        background: var(--line);
        border-radius: var(--r-full);
        overflow: hidden;
    }

    .completion-progress-fill {
        height: 100%;
        background: var(--brand);
        border-radius: var(--r-full);
        transition: width 0.3s ease;
    }

    /* Header Actions */
    .profile-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }

    .btn-header-primary {
        height: 44px;
        padding: 0 18px;
        background: var(--brand);
        color: var(--surface);
        border: none;
        border-radius: var(--r-md);
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.15s ease;
        white-space: nowrap;
    }

    .btn-header-primary:hover {
        background: var(--brand-hover);
    }

    .dropdown-wrap {
        position: relative;
    }

    .btn-header-more {
        height: 44px;
        padding: 0 16px;
        background: var(--surface);
        color: var(--text);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-header-more:hover {
        background: var(--surface-2);
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: calc(100% + 6px);
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        box-shadow: 0 8px 24px rgba(38, 51, 45, 0.1);
        min-width: 190px;
        z-index: 100;
        padding: 6px 0;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        font-size: 13.5px;
        color: var(--text);
        text-decoration: none;
        transition: background 0.15s ease;
    }

    .dropdown-item:hover {
        background: var(--surface-2);
    }

    /* Layout: 2 Columns on Desktop (2/3 + 1/3) */
    .profile-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 1024px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Stacked Cards Styling: --r-lg, 24px padding, 1px --line border */
    .content-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        padding: 24px;
        margin-bottom: 24px;
    }

    .content-card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
        margin: 0 0 20px 0;
        letter-spacing: -0.01em;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* 2-Column Grid for Personal Details */
    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px 24px;
    }

    @media (max-width: 640px) {
        .details-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .detail-label {
        font-size: 13px;
        font-weight: 500;
        color: var(--muted);
        line-height: 1.2;
    }

    .detail-value {
        font-size: 16px;
        font-weight: 500;
        color: var(--text);
        line-height: 1.3;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-reveal {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        background: var(--brand-soft);
        color: var(--brand);
        border: 1px solid transparent;
        border-radius: var(--r-sm);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-reveal:hover {
        background: var(--line);
    }

    .social-icons-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .social-link-icon {
        width: 32px;
        height: 32px;
        border-radius: var(--r-sm);
        border: 1px solid var(--line);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--brand);
        text-decoration: none;
        transition: background 0.15s ease;
    }

    .social-link-icon:hover {
        background: var(--surface-2);
    }

    /* Documents List */
    .documents-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .document-card {
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        padding: 16px;
        background: var(--surface);
    }

    .doc-main-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .doc-left {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
        flex: 1;
    }

    .doc-thumb {
        width: 48px;
        height: 48px;
        border-radius: var(--r-sm);
        border: 1px solid var(--line);
        background: var(--bg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--brand);
        flex-shrink: 0;
        overflow: hidden;
    }

    .doc-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .doc-info {
        min-width: 0;
    }

    .doc-title {
        font-size: 15px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 2px;
    }

    .doc-meta {
        font-size: 12.5px;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .doc-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .btn-doc-view {
        height: 34px;
        padding: 0 12px;
        background: var(--surface);
        color: var(--brand);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-doc-view:hover {
        background: var(--brand-soft);
        border-color: var(--brand);
    }

    .btn-doc-download {
        height: 34px;
        padding: 0 12px;
        background: var(--brand);
        color: var(--surface);
        border: none;
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-doc-download:hover {
        background: var(--brand-hover);
    }

    .doc-versions-row {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed var(--line);
        font-size: 12px;
        color: var(--muted);
    }

    .doc-version-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 4px 0;
    }

    /* Right Sticky Column */
    .sticky-right-col {
        position: sticky;
        top: 80px;
    }

    /* WhatsApp Button: outline button with small whatsapp icon, radius --r-md; no bright green square */
    .btn-whatsapp-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        height: 44px;
        padding: 0 18px;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        color: var(--brand);
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        width: 100%;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    .btn-whatsapp-outline:hover {
        background: var(--brand-soft);
        border-color: var(--brand);
    }

    .contact-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        color: var(--text);
        text-decoration: none;
        padding: 10px 14px;
        border-radius: var(--r-md);
        border: 1px solid var(--line);
        background: var(--bg);
        transition: background 0.15s ease;
    }

    .contact-item:hover {
        background: var(--surface-2);
    }

    .contact-item svg {
        color: var(--brand);
        flex-shrink: 0;
    }

    /* Aptitude Test Card */
    .score-display-row {
        display: flex;
        align-items: baseline;
        gap: 8px;
        margin-bottom: 8px;
    }

    .score-large {
        font-size: 28px;
        font-weight: 700;
        color: var(--brand);
        letter-spacing: -0.02em;
    }

    .score-meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        font-size: 13px;
        margin-bottom: 16px;
        padding-top: 10px;
        border-top: 1px solid var(--line);
    }

    .btn-view-answers {
        color: var(--brand);
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-view-answers:hover {
        text-decoration: underline;
    }

    .btn-create-test-link {
        height: 40px;
        width: 100%;
        background: var(--brand);
        color: var(--surface);
        border: none;
        border-radius: var(--r-md);
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-create-test-link:hover {
        background: var(--brand-hover);
    }

    /* Drawers (Slide-in Panels) */
    .drawer-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(38, 51, 45, 0.4);
        z-index: 150;
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
        max-width: 560px;
        height: 100vh;
        height: 100dvh;
        background: var(--surface);
        box-shadow: -8px 0 32px rgba(38, 51, 45, 0.15);
        z-index: 160;
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
        flex-shrink: 0;
    }

    .drawer-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
        margin: 0;
    }

    .drawer-close {
        background: transparent;
        border: none;
        color: var(--muted);
        cursor: pointer;
        padding: 6px;
        border-radius: var(--r-sm);
    }

    .drawer-close:hover {
        background: var(--surface-2);
        color: var(--text);
    }

    .drawer-body {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
    }

    /* Answer Items in Drawer */
    .answer-item {
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        padding: 16px;
        margin-bottom: 14px;
        background: var(--surface);
    }

    .answer-q-text {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 8px;
    }

    .answer-row {
        font-size: 13px;
        margin-bottom: 4px;
        display: flex;
        align-items: flex-start;
        gap: 6px;
    }

    .ans-tag {
        font-weight: 600;
        min-width: 65px;
    }

    /* Page Actions / Soft Archive Bar */
    .page-actions-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        padding: 18px 24px;
        margin-top: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .btn-archive {
        background: transparent;
        border: 1px solid var(--line);
        color: var(--danger);
        padding: 8px 16px;
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-archive:hover {
        background: var(--danger-soft);
        border-color: var(--danger);
    }

    .btn-restore {
        background: transparent;
        border: 1px solid var(--line);
        color: var(--brand);
        padding: 8px 16px;
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-restore:hover {
        background: var(--brand-soft);
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
</style>

<!-- 1. Header Card -->
<section class="profile-header-card" aria-label="Candidate Header Overview">
    <div class="profile-identity-group">
        <div class="profile-avatar-large" aria-hidden="true">
            <?= strtoupper(substr($candidate['full_name'], 0, 2)) ?>
        </div>
        <div class="profile-identity-details">
            <div class="profile-name-row">
                <h1 class="profile-name"><?= e($candidate['full_name']) ?></h1>
                <span class="chip-badge">ID #<?= sprintf('%04d', $candidate['id']) ?></span>
                <span class="chip-badge"><?= date('d M Y', strtotime($candidate['created_at'])) ?></span>
                <?php $jDate = $profile['joining_date'] ?? ($candidate['joining_date'] ?? null); ?>
                <?php if (!empty($jDate)): ?>
                    <span class="chip-badge" style="background: var(--surface-2); color: var(--text);">Joined: <?= date('d M Y', strtotime($jDate)) ?></span>
                <?php endif; ?>
                <?php if (!empty($candidate['is_guest'])): ?>
                    <span class="chip-badge" style="background: var(--brand-soft); color: var(--brand);">Guest</span>
                <?php endif; ?>
                <?php if (!empty($candidate['is_archived'])): ?>
                    <span class="chip-badge" style="background: var(--danger-soft); color: var(--danger);">Archived</span>
                <?php endif; ?>
            </div>

            <!-- Email on its own line (overflow-wrap: anywhere) -->
            <div class="profile-email-line"><?= e($candidate['email']) ?></div>

            <!-- Mobile and WhatsApp as two clickable items that wrap cleanly -->
            <div class="profile-numbers-row">
                <a href="tel:<?= e($candidate['mobile'] ?? '') ?>" class="number-link" aria-label="Call <?= e($candidate['mobile'] ?? '') ?>">
                    <svg width="15" height="15" aria-hidden="true"><use href="#icon-phone"></use></svg>
                    <span><?= e($candidate['mobile'] ?? '-') ?></span>
                </a>
                <a href="https://wa.me/91<?= preg_replace('/[^0-9]/', '', $candidate['whatsapp'] ?? ($candidate['mobile'] ?? '')) ?>" target="_blank" rel="noopener" class="number-link" aria-label="WhatsApp <?= e($candidate['whatsapp'] ?? ($candidate['mobile'] ?? '')) ?>">
                    <svg width="15" height="15" aria-hidden="true"><use href="#icon-whatsapp"></use></svg>
                    <span>WhatsApp</span>
                </a>
            </div>

            <!-- Thin progress bar: Profile 100% complete -->
            <?php $pct = (int)($profile['completion_percentage'] ?? (!empty($candidate['documents']) ? 100 : 50)); ?>
            <div class="completion-bar-wrap">
                <span class="completion-text">Profile <?= $pct ?>% complete</span>
                <div class="completion-progress" role="progressbar" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100">
                    <div class="completion-progress-fill" style="width: <?= $pct ?>%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right of Header: Primary Download full file (PDF) + Secondary Dropdown More -->
    <div class="profile-header-actions">
        <a href="<?= url('/admin/candidates/' . $candidate['id'] . '/export-full-pdf') ?>" class="btn-header-primary" aria-label="Download full file PDF">
            <svg width="16" height="16" aria-hidden="true"><use href="#icon-download"></use></svg>
            <span>Download full file (PDF)</span>
        </a>

        <div class="dropdown-wrap">
            <button type="button" class="btn-header-more" id="moreDropdownBtn" aria-label="More export options" onclick="toggleMoreMenu()">
                <span>More</span>
                <svg width="14" height="14" aria-hidden="true"><use href="#icon-chevron-down"></use></svg>
            </button>
            <div class="dropdown-menu" id="moreDropdownMenu">
                <a href="<?= url('/admin/candidates/' . $candidate['id'] . '/export-pdf') ?>" class="dropdown-item" aria-label="Download profile only PDF">
                    <svg width="15" height="15" aria-hidden="true"><use href="#icon-file-text"></use></svg>
                    <span>Profile only (PDF)</span>
                </a>
                <a href="<?= url('/admin/candidates/' . $candidate['id'] . '/export-zip') ?>" class="dropdown-item" aria-label="Download documents ZIP">
                    <svg width="15" height="15" aria-hidden="true"><use href="#icon-folder"></use></svg>
                    <span>Documents (ZIP)</span>
                </a>
                <a href="<?= url('/admin/candidates/' . $candidate['id'] . '/export-docx') ?>" class="dropdown-item" aria-label="Download profile DOCX">
                    <svg width="15" height="15" aria-hidden="true"><use href="#icon-file"></use></svg>
                    <span>Profile (DOCX)</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 2. Two-Column Layout (2/3 + 1/3) -->
<div class="profile-grid">
    
    <!-- LEFT COLUMN (2/3 width) -->
    <div class="left-col">
        
        <!-- Card 1: Personal Details -->
        <div class="content-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
                <h2 class="content-card-title" style="margin: 0;">Personal details</h2>
                <button type="button" class="btn-doc-view" onclick="openProfileEditModal()" aria-label="Edit candidate personal details">
                    <svg width="14" height="14" aria-hidden="true"><use href="#icon-edit"></use></svg>
                    <span>Edit details</span>
                </button>
            </div>

            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Full legal name</span>
                    <span class="detail-value" id="profileFullNameVal"><?= e($candidate['full_name']) ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Father's name</span>
                    <span class="detail-value" id="profileFatherNameVal"><?= e($candidate['father_name'] ?? ($profile['father_name'] ?? '-')) ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Joining date</span>
                    <span class="detail-value" id="profileJoiningDateVal">
                        <?= !empty($jDate) ? date('d M Y', strtotime($jDate)) : '<span style="color:var(--muted); font-style:italic;">Not set</span>' ?>
                    </span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Date of birth</span>
                    <span class="detail-value" id="profileDobVal"><?= !empty($profile['dob']) ? date('d M Y', strtotime($profile['dob'])) : '-' ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Gender</span>
                    <span class="detail-value" id="profileGenderVal"><?= !empty($profile['gender']) ? ucfirst($profile['gender']) : '-' ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Email</span>
                    <span class="detail-value" style="overflow-wrap: anywhere;"><?= e($candidate['email']) ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Mobile</span>
                    <span class="detail-value"><?= e($candidate['mobile'] ?? '-') ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">WhatsApp</span>
                    <span class="detail-value"><?= e($candidate['whatsapp'] ?? ($candidate['mobile'] ?? '-')) ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Address</span>
                    <span class="detail-value"><?= e(trim(($profile['address_line1'] ?? '') . ' ' . ($profile['address_line2'] ?? ''))) ?: '-' ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">City</span>
                    <span class="detail-value"><?= e($profile['city'] ?? '-') ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">State</span>
                    <span class="detail-value"><?= e($profile['state'] ?? '-') ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Pincode</span>
                    <span class="detail-value"><?= e($profile['pincode'] ?? '-') ?></span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Social links</span>
                    <div class="detail-value">
                        <div class="social-icons-row">
                            <?php if (!empty($social['linkedin'])): ?>
                                <a href="<?= e($social['linkedin']) ?>" target="_blank" rel="noopener" class="social-link-icon" aria-label="LinkedIn profile">
                                    <svg width="16" height="16" aria-hidden="true"><use href="#icon-link"></use></svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($social['twitter'])): ?>
                                <a href="<?= e($social['twitter']) ?>" target="_blank" rel="noopener" class="social-link-icon" aria-label="Twitter profile">
                                    <svg width="16" height="16" aria-hidden="true"><use href="#icon-link"></use></svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($social['instagram'])): ?>
                                <a href="<?= e($social['instagram']) ?>" target="_blank" rel="noopener" class="social-link-icon" aria-label="Instagram profile">
                                    <svg width="16" height="16" aria-hidden="true"><use href="#icon-link"></use></svg>
                                </a>
                            <?php endif; ?>
                            <?php if (empty($social['linkedin']) && empty($social['twitter']) && empty($social['instagram'])): ?>
                                <span style="color: var(--muted);">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Aadhaar Masked with Permission-Checked Reveal -->
                <div class="detail-item">
                    <span class="detail-label">Aadhaar number</span>
                    <div class="detail-value">
                        <span id="aadhaarDisplayValue">
                            <?= !empty($profile['aadhaar_masked']) ? e($profile['aadhaar_masked']) : (!empty($profile['aadhaar_last4']) ? 'XXXX XXXX ' . e($profile['aadhaar_last4']) : '-') ?>
                        </span>
                        <?php if (!empty($profile['aadhaar_enc']) || !empty($profile['aadhaar_last4'])): ?>
                            <button type="button" class="btn-reveal" onclick="revealIdentity('aadhaar')" aria-label="Reveal Aadhaar number">Reveal</button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- PAN Masked with Permission-Checked Reveal -->
                <div class="detail-item">
                    <span class="detail-label">PAN number</span>
                    <div class="detail-value">
                        <span id="panDisplayValue">
                            <?= !empty($profile['pan_masked']) ? e($profile['pan_masked']) : (!empty($profile['pan_last4']) ? 'XXXXX' . e($profile['pan_last4']) : '-') ?>
                        </span>
                        <?php if (!empty($profile['pan_enc']) || !empty($profile['pan_last4'])): ?>
                            <button type="button" class="btn-reveal" onclick="revealIdentity('pan')" aria-label="Reveal PAN number">Reveal</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Documents (ALL uploaded documents in ONE list/grid) -->
        <div class="content-card">
            <h2 class="content-card-title">
                <span>Documents</span>
                <span style="font-size: 13px; font-weight: 500; color: var(--muted);"><?= count($docs) ?> uploaded</span>
            </h2>

            <?php if (empty($docs)): ?>
                <div style="padding: 32px 16px; text-align: center; color: var(--muted); font-size: 14px;">
                    No documents uploaded
                </div>
            <?php else: ?>
                <div class="documents-list">
                    <?php foreach ($docs as $doc): ?>
                        <?php
                        $docTypeTitle = ucwords(str_replace('_', ' ', $doc['document_type']));
                        $sizeBefore = formatFileSize($doc['file_size_before'] ?? null);
                        $sizeAfter = formatFileSize($doc['file_size_after'] ?? ($doc['file_size'] ?? null));
                        $isImage = str_starts_with($doc['mime_type'] ?? '', 'image/');
                        ?>
                        <div class="document-card">
                            <div class="doc-main-row">
                                <div class="doc-left">
                                    <div class="doc-thumb">
                                        <?php if ($isImage): ?>
                                            <img src="<?= url('/document/download/' . $doc['id']) ?>" alt="Thumbnail">
                                        <?php else: ?>
                                            <svg width="24" height="24" aria-hidden="true"><use href="#icon-file-text"></use></svg>
                                        <?php endif; ?>
                                    </div>
                                    <div class="doc-info">
                                        <div class="doc-title"><?= e($docTypeTitle) ?></div>
                                        <div class="doc-meta">
                                            <span><?= e($doc['original_name']) ?></span>
                                            <span>&bull;</span>
                                            <span><?= $sizeBefore ?> &rarr; <?= $sizeAfter ?></span>
                                            <span>&bull;</span>
                                            <span><?= date('d M Y', strtotime($doc['uploaded_at'])) ?></span>
                                            <span>&bull;</span>
                                            <span style="font-weight: 600;">v<?= (int)($doc['version'] ?? 1) ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="doc-actions">
                                    <button type="button" class="btn-doc-view" 
                                            onclick="openDocPreview('<?= e(addslashes($docTypeTitle)) ?>', '<?= url('/document/download/' . $doc['id']) ?>', '<?= e($doc['mime_type'] ?? '') ?>')" 
                                            aria-label="View preview of <?= e($docTypeTitle) ?>">
                                        <svg width="14" height="14" aria-hidden="true"><use href="#icon-eye"></use></svg>
                                        <span>View</span>
                                    </button>
                                    <a href="<?= url('/document/download/' . $doc['id']) ?>" class="btn-doc-download" aria-label="Download <?= e($docTypeTitle) ?>">
                                        <svg width="14" height="14" aria-hidden="true"><use href="#icon-download"></use></svg>
                                        <span>Download</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Older versions collapsed row -->
                            <?php if (!empty($doc['versions'])): ?>
                                <details class="doc-versions-row">
                                    <summary style="cursor: pointer; font-weight: 600; color: var(--brand);">Previous versions (<?= count($doc['versions']) ?>)</summary>
                                    <div style="margin-top: 8px;">
                                        <?php foreach ($doc['versions'] as $v): ?>
                                            <div class="doc-version-item">
                                                <span>v<?= (int)$v['version'] ?> &bull; <?= e($v['original_name']) ?> &bull; <?= formatFileSize($v['file_size']) ?> &bull; <?= date('d M Y', strtotime($v['replaced_at'])) ?></span>
                                                <a href="<?= url('/document-version/download/' . $v['id']) ?>" style="font-weight: 500;" aria-label="Download version <?= (int)$v['version'] ?>">Download</a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </details>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- RIGHT COLUMN (1/3 width, sticky on desktop) -->
    <div class="right-col sticky-right-col">
        
        <!-- Card 3: Contact -->
        <div class="content-card">
            <h2 class="content-card-title">Contact</h2>

            <div class="contact-list">
                <!-- Phone -->
                <a href="tel:<?= e($candidate['mobile'] ?? '') ?>" class="contact-item" aria-label="Call candidate directly">
                    <svg width="18" height="18" aria-hidden="true"><use href="#icon-phone"></use></svg>
                    <span><?= e($candidate['mobile'] ?? 'No phone provided') ?></span>
                </a>

                <!-- WhatsApp Button: outline button with small whatsapp icon, radius --r-md; no bright green square -->
                <a href="https://wa.me/91<?= preg_replace('/[^0-9]/', '', $candidate['whatsapp'] ?? ($candidate['mobile'] ?? '')) ?>" target="_blank" rel="noopener" class="btn-whatsapp-outline" aria-label="Open WhatsApp chat with candidate">
                    <svg width="18" height="18" aria-hidden="true"><use href="#icon-whatsapp"></use></svg>
                    <span>Message on WhatsApp</span>
                </a>

                <!-- Email -->
                <a href="mailto:<?= e($candidate['email']) ?>" class="contact-item" aria-label="Send email to candidate">
                    <svg width="18" height="18" aria-hidden="true"><use href="#icon-mail"></use></svg>
                    <span style="overflow-wrap: anywhere;"><?= e($candidate['email']) ?></span>
                </a>
            </div>
        </div>

        <!-- Card 4: Aptitude Test -->
        <div class="content-card">
            <h2 class="content-card-title">Aptitude test</h2>

            <?php if ($test && isset($test['score'])): ?>
                <div class="score-display-row">
                    <span class="score-large"><?= (int)$test['score'] ?> / <?= (int)($test['total_questions'] ?? 15) ?></span>
                    <span style="font-size: 16px; font-weight: 600; color: var(--muted);">(<?= round($test['percentage'] ?? 0) ?>%)</span>
                    <span class="chip-badge" style="margin-left: auto; <?= !empty($test['passed']) ? 'background: var(--brand-soft); color: var(--brand);' : 'background: var(--danger-soft); color: var(--danger);' ?>">
                        <?= !empty($test['passed']) ? 'Passed' : 'Not passed' ?>
                    </span>
                </div>

                <div class="score-meta-grid">
                    <div>
                        <span style="color: var(--muted);">Set used:</span>
                        <div style="font-weight: 600;"><?= e($test['set_code'] ?? ($test['set_name'] ?? 'Set A')) ?></div>
                    </div>
                    <div>
                        <span style="color: var(--muted);">Time taken:</span>
                        <div style="font-weight: 600;">
                            <?= !empty($test['time_spent_seconds']) ? sprintf('%dm %ds', floor($test['time_spent_seconds'] / 60), $test['time_spent_seconds'] % 60) : '-' ?>
                        </div>
                    </div>
                    <div>
                        <span style="color: var(--muted);">Date:</span>
                        <div style="font-weight: 600;"><?= date('d M Y', strtotime($test['created_at'])) ?></div>
                    </div>
                </div>

                <?php if (!empty($testAnswers)): ?>
                    <button type="button" class="btn-view-answers" onclick="openAnswersDrawer()" aria-label="View candidate test answers">
                        <svg width="16" height="16" aria-hidden="true"><use href="#icon-file-check"></use></svg>
                        <span>View answers</span>
                    </button>
                <?php endif; ?>

            <?php else: ?>
                <div style="margin-bottom: 16px;">
                    <div style="font-size: 16px; font-weight: 600; color: var(--muted); margin-bottom: 4px;">Test not taken</div>
                    <p style="font-size: 13px; color: var(--muted); margin: 0 0 16px 0;">Generate a direct test link to allow candidate evaluation without login.</p>
                    <button type="button" class="btn-create-test-link" onclick="openCreateTestLinkModal()" aria-label="Create guest test link for candidate">
                        <svg width="16" height="16" aria-hidden="true"><use href="#icon-link"></use></svg>
                        <span>Create test link</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Existing Guest Links if any -->
            <?php if (!empty($guestLinks)): ?>
                <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--line);">
                    <div style="font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; margin-bottom: 8px;">Generated Test Links</div>
                    <?php foreach ($guestLinks as $gl): ?>
                        <div style="font-size: 12.5px; padding: 6px 0; border-bottom: 1px dashed var(--line); display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <span style="font-weight:600;"><?= e($gl['set_name']) ?></span> &bull; 
                                <span style="color: <?= $gl['status'] === 'active' ? 'var(--brand)' : 'var(--muted)' ?>;"><?= ucfirst($gl['status']) ?></span>
                            </div>
                            <?php if ($gl['status'] === 'active'): ?>
                                <button type="button" class="btn-reveal" onclick="revokeGuestLink(<?= $gl['id'] ?>)" aria-label="Revoke link">Revoke</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Page Actions: Soft Archive Only -->
        <div class="page-actions-card">
            <span style="font-size: 13px; color: var(--muted);">Candidate record action:</span>
            <?php if (empty($candidate['is_archived'])): ?>
                <form action="<?= url('/admin/candidates/' . $candidate['id'] . '/archive') ?>" method="POST" onsubmit="return confirm('Archive this candidate profile?');">
                    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                    <button type="submit" class="btn-archive" aria-label="Archive candidate">
                        Archive candidate
                    </button>
                </form>
            <?php else: ?>
                <form action="<?= url('/admin/candidates/' . $candidate['id'] . '/restore') ?>" method="POST">
                    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                    <button type="submit" class="btn-restore" aria-label="Restore candidate">
                        Restore candidate
                    </button>
                </form>
            <?php endif; ?>
        </div>

    </div>

</div>

<!-- -----------------------------------------------------------------
     DRAWERS & MODALS
     ----------------------------------------------------------------- -->

<!-- Drawer Overlay -->
<div class="drawer-overlay" id="drawerOverlay" onclick="closeAllDrawers()"></div>

<!-- Document Preview Drawer -->
<div class="drawer-panel" id="docPreviewDrawer" role="dialog" aria-modal="true" aria-label="Document Preview">
    <div class="drawer-header">
        <h3 class="drawer-title" id="docPreviewTitle">Document Preview</h3>
        <button type="button" class="drawer-close" onclick="closeAllDrawers()" aria-label="Close document preview">
            <svg width="20" height="20" aria-hidden="true"><use href="#icon-x"></use></svg>
        </button>
    </div>
    <div class="drawer-body" id="docPreviewBody">
        <!-- Rendered dynamically -->
    </div>
</div>

<!-- Test Answers Drawer -->
<div class="drawer-panel" id="answersDrawer" role="dialog" aria-modal="true" aria-label="Aptitude Test Answers">
    <div class="drawer-header">
        <h3 class="drawer-title">Test Answers Evaluation</h3>
        <button type="button" class="drawer-close" onclick="closeAllDrawers()" aria-label="Close answers drawer">
            <svg width="20" height="20" aria-hidden="true"><use href="#icon-x"></use></svg>
        </button>
    </div>
    <div class="drawer-body">
        <?php if (!empty($testAnswers)): ?>
            <?php foreach ($testAnswers as $idx => $ans): ?>
                <div class="answer-item">
                    <div class="answer-q-text">Q<?= $idx + 1 ?>. <?= e($ans['question_text']) ?></div>
                    <div class="answer-row">
                        <span class="ans-tag" style="color: <?= !empty($ans['is_correct']) ? 'var(--brand)' : 'var(--danger)' ?>;">Candidate:</span>
                        <span><?= e($ans['candidate_answer'] ?? 'Not answered') ?></span>
                        <?php if (!empty($ans['is_correct'])): ?>
                            <svg width="16" height="16" style="color: var(--brand); margin-left: auto;" aria-hidden="true"><use href="#icon-check-circle"></use></svg>
                        <?php else: ?>
                            <svg width="16" height="16" style="color: var(--danger); margin-left: auto;" aria-hidden="true"><use href="#icon-alert-circle"></use></svg>
                        <?php endif; ?>
                    </div>
                    <?php if (empty($ans['is_correct']) && !empty($ans['correct_answer'])): ?>
                        <div class="answer-row" style="color: var(--muted);">
                            <span class="ans-tag" style="color: var(--brand);">Correct:</span>
                            <span><?= e($ans['correct_answer']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: var(--muted);">No answers recorded for this attempt.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Create Test Link Modal -->
<div class="drawer-overlay" id="testLinkModal" style="display:none; align-items:center; justify-content:center;">
    <div style="background: var(--surface); border-radius: var(--r-lg); border: 1px solid var(--line); padding: 28px; width: 90%; max-width: 480px; box-shadow: 0 16px 40px rgba(38,51,45,0.15);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
            <h3 style="font-size: 18px; font-weight: 600; margin: 0;">Create Guest Test Link</h3>
            <button type="button" class="drawer-close" onclick="closeTestLinkModal()" aria-label="Close modal">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-x"></use></svg>
            </button>
        </div>

        <form id="createTestLinkForm" onsubmit="submitCreateGuestLink(event)">
            <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="user_id" value="<?= $candidate['id'] ?>">

            <div style="margin-bottom: 14px;">
                <label style="font-size: 13px; font-weight: 500; color: var(--muted); display: block; margin-bottom: 6px;">Link Expiry</label>
                <select name="expiry_hours" class="filter-select" style="width: 100%;">
                    <option value="24">24 hours</option>
                    <option value="48" selected>48 hours (Standard)</option>
                    <option value="72">72 hours</option>
                </select>
            </div>

            <button type="submit" class="btn-create-test-link" id="genLinkBtn" aria-label="Generate Link">
                Generate Link
            </button>
        </form>

        <div id="testLinkResultArea" style="display:none; margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--line);">
            <div style="font-size: 13px; font-weight: 500; color: var(--muted); margin-bottom: 6px;">Sharable Test Link:</div>
            <div style="display:flex; gap: 8px; margin-bottom: 12px;">
                <input type="text" id="generatedLinkInput" readonly style="flex:1; height: 38px; padding: 0 10px; border-radius: var(--r-md); border: 1px solid var(--line); font-size: 13px;" aria-label="Generated guest link URL">
                <button type="button" class="btn-reveal" onclick="copyGeneratedLink()" aria-label="Copy guest test link">Copy</button>
            </div>
            <a id="shareWhatsAppBtn" href="#" target="_blank" rel="noopener" class="btn-whatsapp-outline" style="height: 38px;" aria-label="Share guest test link via WhatsApp">
                <svg width="16" height="16" aria-hidden="true"><use href="#icon-whatsapp"></use></svg>
                <span>Share on WhatsApp</span>
            </a>
        </div>
    </div>
</div>

<script>
function toggleMoreMenu() {
    var menu = document.getElementById('moreDropdownMenu');
    menu.classList.toggle('show');
}

window.addEventListener('click', function(e) {
    var btn = document.getElementById('moreDropdownBtn');
    var menu = document.getElementById('moreDropdownMenu');
    if (btn && menu && !btn.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.remove('show');
    }
});

function openDocPreview(title, url, mime) {
    document.getElementById('docPreviewTitle').textContent = title;
    var body = document.getElementById('docPreviewBody');
    body.innerHTML = '';
    if (mime.indexOf('image/') === 0) {
        body.innerHTML = '<img src="' + url + '" style="max-width:100%; height:auto; border-radius: var(--r-sm);" alt="' + title + '">';
    } else {
        body.innerHTML = '<iframe src="' + url + '#toolbar=0" style="width:100%; height:75vh; border:none; border-radius: var(--r-sm);"></iframe>';
    }
    document.getElementById('drawerOverlay').classList.add('active');
    document.getElementById('docPreviewDrawer').classList.add('open');
}

function openAnswersDrawer() {
    document.getElementById('drawerOverlay').classList.add('active');
    document.getElementById('answersDrawer').classList.add('open');
}

function closeAllDrawers() {
    document.getElementById('drawerOverlay').classList.remove('active');
    var p1 = document.getElementById('docPreviewDrawer');
    var p2 = document.getElementById('answersDrawer');
    if (p1) p1.classList.remove('open');
    if (p2) p2.classList.remove('open');
}

function openCreateTestLinkModal() {
    document.getElementById('testLinkModal').style.display = 'flex';
}

function closeTestLinkModal() {
    document.getElementById('testLinkModal').style.display = 'none';
}

function submitCreateGuestLink(e) {
    e.preventDefault();
    var form = document.getElementById('createTestLinkForm');
    var btn = document.getElementById('genLinkBtn');
    btn.disabled = true;
    btn.textContent = 'Generating...';

    var formData = new FormData(form);
    fetch('<?= url("/admin/guest-links/create") ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'Generate Link';
        if (data.success && data.url) {
            document.getElementById('testLinkResultArea').style.display = 'block';
            document.getElementById('generatedLinkInput').value = data.url;
            var waMsg = encodeURIComponent("Hello <?= e(addslashes($candidate['full_name'])) ?>, here is your Realty Smartz Pathshala Aptitude Assessment test link:\n" + data.url + "\nThis link is active for 48 hours. Best of luck!");
            document.getElementById('shareWhatsAppBtn').href = 'https://wa.me/91<?= preg_replace("/[^0-9]/", "", $candidate["whatsapp"] ?? ($candidate["mobile"] ?? "")) ?>?text=' + waMsg;
        } else {
            alert(data.error || 'Failed to create guest link.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Generate Link';
        alert('Server connection error.');
    });
}

function copyGeneratedLink() {
    var input = document.getElementById('generatedLinkInput');
    input.select();
    navigator.clipboard.writeText(input.value);
    alert('Guest test link copied to clipboard!');
}

function revokeGuestLink(id) {
    if (!confirm('Revoke this guest test link?')) return;
    var fd = new FormData();
    fd.append('_csrf_token', '<?= csrf_token() ?>');
    fetch('<?= url("/admin/guest-links/") ?>' + id + '/revoke', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        location.reload();
    });
}

function revealIdentity(type) {
    var fd = new FormData();
    fd.append('_csrf_token', '<?= csrf_token() ?>');
    fd.append('type', type);

    fetch('<?= url("/admin/candidates/" . $candidate["id"] . "/reveal-identity") ?>', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.revealed) {
            if (type === 'aadhaar') {
                document.getElementById('aadhaarDisplayValue').textContent = data.revealed;
            } else if (type === 'pan') {
                document.getElementById('panDisplayValue').textContent = data.revealed;
            }
        } else {
            alert('Unable to reveal identity number.');
        }
    });
}

// Edit Candidate Details Modal in Profile Page
var profileCandidateData = <?= json_encode([
    'id' => $candidate['id'],
    'full_name' => $candidate['full_name'],
    'father_name' => $candidate['father_name'] ?? ($profile['father_name'] ?? ''),
    'email' => $candidate['email'],
    'mobile' => $candidate['mobile'] ?? '',
    'whatsapp' => $candidate['whatsapp'] ?? '',
    'joining_date' => $candidate['joining_date'] ?? ($profile['joining_date'] ?? ''),
    'dob' => $profile['dob'] ?? '',
    'gender' => $profile['gender'] ?? '',
    'city' => $profile['city'] ?? '',
    'state' => $profile['state'] ?? '',
    'pincode' => $profile['pincode'] ?? '',
    'address' => $profile['permanent_address'] ?? ($profile['address'] ?? '')
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

function openProfileEditModal() {
    document.getElementById('editFullName').value = profileCandidateData.full_name || '';
    document.getElementById('editFatherName').value = profileCandidateData.father_name || '';
    document.getElementById('editEmail').value = profileCandidateData.email || '';
    document.getElementById('editMobile').value = profileCandidateData.mobile || '';
    document.getElementById('editWhatsApp').value = profileCandidateData.whatsapp || '';
    document.getElementById('editJoiningDate').value = profileCandidateData.joining_date || '';
    document.getElementById('editDob').value = profileCandidateData.dob || '';
    document.getElementById('editGender').value = profileCandidateData.gender || '';
    document.getElementById('editAddress').value = profileCandidateData.address || '';
    document.getElementById('editCity').value = profileCandidateData.city || '';
    document.getElementById('editState').value = profileCandidateData.state || '';
    document.getElementById('editPincode').value = profileCandidateData.pincode || '';
    document.getElementById('editProfileFormError').style.display = 'none';
    document.getElementById('editProfileModal').classList.add('active');
}

function closeProfileEditModal() {
    document.getElementById('editProfileModal').classList.remove('active');
}

function handleSaveProfileDetails(e) {
    if (e && typeof e.preventDefault === 'function') {
        e.preventDefault();
    }
    var form = document.getElementById('editProfileForm');
    var btn = document.getElementById('btnSaveProfile');
    var errEl = document.getElementById('editProfileFormError');

    btn.disabled = true;
    btn.textContent = 'Saving...';
    errEl.style.display = 'none';

    var fd = new FormData(form);

    fetch('<?= url("/admin/candidates/" . $candidate["id"] . "/update") ?>', {
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
            closeProfileEditModal();
            // Cache updated data
            profileCandidateData.full_name = res.candidate.full_name;
            profileCandidateData.father_name = res.candidate.father_name || '';
            profileCandidateData.email = res.candidate.email;
            profileCandidateData.mobile = res.candidate.mobile || '';
            profileCandidateData.whatsapp = res.candidate.whatsapp || '';
            profileCandidateData.joining_date = res.candidate.joining_date || '';
            profileCandidateData.dob = res.candidate.dob || '';
            profileCandidateData.gender = res.candidate.gender || '';
            profileCandidateData.city = res.candidate.city || '';
            profileCandidateData.state = res.candidate.state || '';
            profileCandidateData.pincode = res.candidate.pincode || '';
            profileCandidateData.address = res.candidate.address || '';

            // Update DOM fields in real time
            if (document.getElementById('profileFullNameVal')) {
                document.getElementById('profileFullNameVal').textContent = res.candidate.full_name;
            }
            if (document.querySelector('.profile-name')) {
                document.querySelector('.profile-name').textContent = res.candidate.full_name;
            }
            if (document.getElementById('profileFatherNameVal')) {
                document.getElementById('profileFatherNameVal').textContent = res.candidate.father_name || '-';
            }
            if (document.getElementById('profileJoiningDateVal')) {
                document.getElementById('profileJoiningDateVal').textContent = res.candidate.joining_date_formatted || 'Not set';
            }
            if (document.getElementById('profileDobVal')) {
                document.getElementById('profileDobVal').textContent = res.candidate.dob ? res.candidate.dob : '-';
            }
            if (document.getElementById('profileGenderVal')) {
                document.getElementById('profileGenderVal').textContent = res.candidate.gender ? (res.candidate.gender.charAt(0).toUpperCase() + res.candidate.gender.slice(1)) : '-';
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
</script>

<!-- Edit Candidate Details Modal (Profile Page) -->
<div class="modal-overlay" id="editProfileModal" role="dialog" aria-modal="true" aria-labelledby="editProfileModalTitle">
    <div class="modal-dialog">
        <div class="modal-header">
            <h2 class="modal-title" id="editProfileModalTitle">Edit candidate details</h2>
            <button type="button" class="modal-close-btn" onclick="closeProfileEditModal()" aria-label="Close edit dialog">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-x"></use></svg>
            </button>
        </div>
        <form id="editProfileForm" onsubmit="handleSaveProfileDetails(event)">
            <?= csrf_field() ?>
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

                <div id="editProfileFormError" style="display:none; color: var(--danger); font-size: 13px; margin-top: 8px;"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" id="btnCancelProfileEdit" onclick="closeProfileEditModal()">Cancel</button>
                <button type="button" class="btn-modal-save" id="btnSaveProfile" onclick="handleSaveProfileDetails(event)">Save changes</button>
            </div>
        </form>
    </div>
</div>


<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/admin.php';
?>
