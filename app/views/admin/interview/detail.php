<?php
ob_start();
$pageTitle = e($app['full_name']) . " | Interview Candidate";
$viewTitle = "Interview Candidate Details";

// Clean Indian mobile for wa.me and tel:
$cleanMob = preg_replace('/[^\d]/', '', (string)$app['mobile']);
$waMob = (strlen($cleanMob) === 10) ? '91' . $cleanMob : $cleanMob;
$prefilledWa = "Hello " . rawurlencode($app['full_name']) . ", this is regarding your interview application for the position of " . rawurlencode($app['post_applied']) . " at Realty Smartz Pathshala.";
$waUrl = "https://wa.me/" . $waMob . "?text=" . $prefilledWa;

$hasFeedback = (!empty(trim((string)$app['feedback_hr'])) || !empty(trim((string)$app['feedback_final'])));
$isFeedbackFilled = $hasFeedback;

// Check if details incomplete for Selected
$isDetailsIncomplete = false;
if ($app['decision'] === 'selected') {
    if (empty(trim((string)$app['position_offered'])) || $app['offered_salary'] === null || $app['offered_salary'] === '') {
        $isDetailsIncomplete = true;
    } elseif ($app['candidate_interested'] === 'yes' && empty($app['joining_date'])) {
        $isDetailsIncomplete = true;
    }
}

// Compute initials avatar
$parts = array_filter(explode(' ', trim((string)$app['full_name'])));
$initials = '';
foreach (array_slice($parts, 0, 2) as $p) {
    $initials .= strtoupper(mb_substr($p, 0, 1));
}
if (empty($initials)) $initials = 'CD';

$lastUpdatedFormatted = '';
if (!empty($app['updated_at'])) {
    $updater = $app['updated_by_name'] ?: 'Administrator';
    $lastUpdatedFormatted = "Last updated by " . e($updater) . " on " . date('d M Y, h:i A', strtotime($app['updated_at']));
} else {
    $lastUpdatedFormatted = "Submitted on " . date('d M Y, h:i A', strtotime($app['submitted_at']));
}
?>

<style>
    /* Detail Header Card */
    .candidate-header-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        padding: 24px 28px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        flex-wrap: wrap;
    }

    .header-profile-block {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .avatar-initials {
        width: 60px;
        height: 60px;
        background-color: var(--brand-soft);
        color: var(--brand);
        border: 2px solid var(--brand);
        border-radius: var(--r-full);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .header-details h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.02em;
        margin: 0 0 4px;
    }

    .header-meta-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        font-size: 13.5px;
        color: var(--muted);
    }

    .header-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--surface-2);
        color: var(--text);
        font-size: 12px;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: var(--r-full);
    }
    .chip-repeat {
        background: #FEF3D6;
        color: #9A6410;
        text-decoration: none;
    }
    .chip-repeat:hover {
        background: #FDE8B5;
    }

    .header-contacts {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-top: 6px;
        font-size: 13.5px;
        flex-wrap: wrap;
    }
    .contact-link {
        color: var(--brand);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-weight: 500;
    }
    .contact-link:hover {
        text-decoration: underline;
    }

    .header-buttons-block {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    /* Action buttons */
    .btn-action-outline,
    .btn-action-primary,
    .btn-whatsapp,
    .btn-history {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 13.5px;
        font-weight: 600;
        font-family: inherit;
        line-height: 1;
        padding: 9px 16px;
        border-radius: var(--r-md);
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
        box-sizing: border-box;
    }

    .btn-action-outline {
        background: #FFFFFF;
        color: var(--text);
        border: 1px solid var(--line);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .btn-action-outline:hover {
        background: var(--surface-2);
        border-color: #CBD5E1;
        color: var(--text);
        transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.06);
    }
    .btn-action-outline:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .btn-action-primary {
        background: var(--brand);
        color: #FFFFFF !important;
        border: 1px solid var(--brand);
        box-shadow: 0 1px 3px rgba(31, 92, 74, 0.2);
    }
    .btn-action-primary:hover {
        background: #17483A;
        border-color: #17483A;
        color: #FFFFFF !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(31, 92, 74, 0.25);
    }
    .btn-action-primary:active {
        transform: translateY(0);
    }

    .btn-whatsapp {
        background: #25D366;
        color: #FFFFFF !important;
        border: 1px solid #20BA5A;
        box-shadow: 0 2px 5px rgba(37, 211, 102, 0.25);
    }
    .btn-whatsapp:hover {
        background: #1EBE5D;
        border-color: #1EBE5D;
        color: #FFFFFF !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 211, 102, 0.35);
    }
    .btn-whatsapp:active {
        transform: translateY(0);
    }

    .btn-history {
        background: #FFFFFF;
        color: var(--text);
        border: 1px solid var(--line);
        font-size: 13px;
        padding: 7px 12px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .btn-history:hover {
        background: var(--surface-2);
        border-color: #CBD5E1;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.06);
    }

    /* Prominent Save HR Feedback Button */
    .btn-save-hr {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: linear-gradient(180deg, #1F5C4A 0%, #17483A 100%);
        color: #FFFFFF;
        border: 1px solid #143E32;
        padding: 13px 20px;
        border-radius: var(--r-md);
        font-size: 15px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 12px rgba(31, 92, 74, 0.28);
    }
    .btn-save-hr:hover:not(:disabled) {
        background: linear-gradient(180deg, #246B56 0%, #1B5242 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(31, 92, 74, 0.35);
    }
    .btn-save-hr:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(31, 92, 74, 0.25);
    }
    .btn-save-hr:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        box-shadow: none;
    }

    /* Email notification toggle card */
    .email-toggle-card {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: var(--r-md);
        padding: 12px 14px;
        margin-top: 14px;
        margin-bottom: 16px;
        transition: border-color 0.2s ease, background-color 0.2s ease;
    }
    .email-toggle-card:hover {
        background: #F1F5F9;
        border-color: #CBD5E1;
    }
    .email-toggle-label {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        cursor: pointer;
        user-select: none;
        margin: 0;
    }
    .email-toggle-checkbox {
        width: 18px;
        height: 18px;
        accent-color: var(--brand);
        margin-top: 2px;
        flex-shrink: 0;
        cursor: pointer;
    }
    .email-toggle-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .email-toggle-title {
        font-size: 13.5px;
        font-weight: 700;
        color: var(--text);
    }
    .email-toggle-sub {
        font-size: 12px;
        color: var(--muted);
        line-height: 1.4;
    }

    /* Input with prefix symbol (₹) */
    .input-with-symbol {
        position: relative;
        display: flex;
        align-items: center;
    }
    .input-with-symbol .input-symbol-prefix {
        position: absolute;
        left: 14px;
        font-size: 15px;
        font-weight: 700;
        color: var(--muted);
        pointer-events: none;
    }
    .input-with-symbol .hr-control {
        padding-left: 32px;
    }

    /* Pill radio buttons for Candidate interested */
    .pill-radio-group {
        display: flex;
        gap: 10px;
    }
    .pill-radio-item {
        flex: 1;
        position: relative;
    }
    .pill-radio-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 8px 14px;
        border: 1.5px solid var(--line);
        background: #FFFFFF;
        border-radius: var(--r-md);
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text);
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .pill-radio-label:hover {
        border-color: #CBD5E1;
        background: var(--surface-2);
    }
    .pill-radio-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .pill-radio-input:checked + .pill-radio-label {
        border-color: var(--brand);
        background: var(--brand-soft);
        color: var(--brand);
        font-weight: 700;
        box-shadow: 0 0 0 1px var(--brand);
    }

    /* HR Status Badge */
    .hr-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: var(--r-full);
        background: var(--surface-2);
        color: var(--muted);
        border: 1px solid var(--line);
        transition: all 0.2s ease;
    }
    .hr-status-badge.is-unsaved {
        background: #FEF3D6;
        color: #9A6410;
        border-color: #F6D38B;
    }
    .hr-status-badge.is-saving {
        background: #E0F2FE;
        color: #0369A1;
        border-color: #BAE6FD;
    }
    .hr-status-badge.is-saved {
        background: var(--brand-soft);
        color: var(--brand);
        border-color: rgba(31, 92, 74, 0.25);
    }
    .hr-status-badge.is-error {
        background: #FDF2F2;
        color: #B3261E;
        border-color: #F5C6CB;
    }

    /* Alert Banners inside HR card */
    .hr-save-alert {
        border-radius: var(--r-md);
        padding: 12px 16px;
        margin-bottom: 16px;
        font-size: 13.5px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: fadeInAlert 0.2s ease-in-out;
    }
    .hr-save-alert.alert-success {
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
        color: #065F46;
    }
    .hr-save-alert.alert-error {
        background: #FEF2F2;
        border: 1px solid #FECACA;
        color: #991B1B;
    }
    @keyframes fadeInAlert {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Grid Layout: Desktop 3/5 left, 2/5 right. Mobile 1 column */
    .detail-grid-layout {
        display: grid;
        grid-template-columns: 3fr 2fr;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 991.98px) {
        .detail-grid-layout {
            display: flex;
            flex-direction: column;
        }
        /* Mobile Order: Header, HR Section, Candidate Details */
        .right-hr-col {
            order: 1;
            width: 100%;
        }
        .left-candidate-col {
            order: 2;
            width: 100%;
        }
    }

    /* Cards */
    .section-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .section-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--line);
        background: var(--surface);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .section-card-header h3 {
        font-size: 17px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }

    .section-card-body {
        padding: 24px;
    }

    /* Readonly Candidate Fields Grid */
    .details-table-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px 20px;
    }
    @media (max-width: 580px) {
        .details-table-list {
            grid-template-columns: 1fr;
        }
    }
    .detail-field-item {
        padding-bottom: 12px;
        border-bottom: 1px dashed var(--line);
    }
    .detail-field-item.full-width {
        grid-column: 1 / -1;
    }
    .detail-label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--muted);
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .detail-value {
        font-size: 14.5px;
        font-weight: 500;
        color: var(--text);
        word-break: break-word;
    }

    /* Employment table inside candidate details */
    .emp-history-box {
        margin-top: 16px;
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        overflow: hidden;
    }
    .read-emp-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .read-emp-table th {
        background: var(--surface-2);
        color: var(--text);
        font-weight: 600;
        padding: 8px 12px;
        border-bottom: 1px solid var(--line);
        font-size: 12px;
    }
    .read-emp-table td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--line);
    }
    @media (max-width: 767.98px) {
        .read-emp-table, .read-emp-table thead, .read-emp-table tbody, .read-emp-table tr, .read-emp-table td {
            display: block;
            width: 100%;
        }
        .read-emp-table thead {
            display: none;
        }
        .read-emp-table tr {
            padding: 12px;
            border-bottom: 1px solid var(--line);
            background: #FFFFFF;
        }
        .read-emp-table td {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border: none;
        }
        .read-emp-table td::before {
            content: attr(data-col);
            font-weight: 600;
            color: var(--muted);
            font-size: 12px;
        }
    }

    /* Sticky HR Section */
    .sticky-hr-card {
        position: sticky;
        top: 88px;
    }

    /* Autosave status indicator */
    .autosave-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: var(--r-full);
        background: var(--surface-2);
        color: var(--muted);
        transition: all 0.2s ease;
    }
    .status-saving {
        background: #FEF3D6;
        color: #9A6410;
    }
    .status-saved {
        background: var(--brand-soft);
        color: var(--brand);
    }
    .status-error {
        background: #FDF2F2;
        color: #B3261E;
    }

    /* HR Form Controls */
    .hr-form-group {
        margin-bottom: 18px;
    }
    .hr-label {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 6px;
    }
    .hr-control {
        width: 100%;
        font-size: 14.5px;
        font-family: inherit;
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        padding: 10px 14px;
        color: var(--text);
        background: #FFFFFF;
        outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .hr-control:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(31, 92, 74, 0.12);
    }
    .hr-control:disabled {
        background-color: var(--surface-2);
        cursor: not-allowed;
        color: var(--muted);
    }
    textarea.hr-control {
        min-height: 72px;
        resize: vertical;
    }

    /* Segmented Decision Radios */
    .segmented-control {
        display: flex;
        background: var(--surface-2);
        border-radius: var(--r-md);
        padding: 4px;
        gap: 4px;
    }
    .segmented-option {
        flex: 1;
        text-align: center;
        position: relative;
    }
    .segmented-option input[type="radio"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 1;
    }
    .segmented-label {
        display: block;
        padding: 8px 12px;
        border-radius: var(--r-sm);
        font-size: 13.5px;
        font-weight: 600;
        color: var(--muted);
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .segmented-option input[type="radio"]:checked + .segmented-label {
        background: #FFFFFF;
        color: var(--text);
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .segmented-option input[type="radio"]:focus-visible + .segmented-label {
        outline: 2px solid var(--brand);
    }
    .segmented-option.is-selected input[type="radio"]:checked + .segmented-label {
        color: #226B35;
    }
    .segmented-option.is-rejected input[type="radio"]:checked + .segmented-label {
        color: #B3261E;
    }

    /* Subfields cards */
    .subfields-box {
        background: var(--surface-2);
        border-radius: var(--r-md);
        padding: 16px;
        margin-top: 14px;
        margin-bottom: 18px;
        border: 1px solid var(--line);
    }

    /* Conflict Banner (409) */
    .conflict-banner {
        display: none;
        background: #FEF3D6;
        border: 1px solid #E5C370;
        color: #9A6410;
        border-radius: var(--r-md);
        padding: 12px 16px;
        margin-bottom: 16px;
        font-size: 13.5px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    /* History Drawer */
    .drawer-overlay {
        position: fixed;
        inset: 0;
        background: rgba(38, 51, 45, 0.4);
        display: none;
        z-index: 1200;
        backdrop-filter: blur(2px);
    }
    .drawer-panel {
        position: fixed;
        top: 0;
        right: -480px;
        width: 100%;
        max-width: 440px;
        height: 100vh;
        background: #FFFFFF;
        box-shadow: -6px 0 24px rgba(0,0,0,0.12);
        z-index: 1250;
        display: flex;
        flex-direction: column;
        transition: right 0.25s ease;
    }
    .drawer-panel.open {
        right: 0;
    }
    .drawer-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--line);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .drawer-header h3 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }
    .drawer-body {
        padding: 20px 24px;
        overflow-y: auto;
        flex: 1;
    }
    .history-timeline-item {
        position: relative;
        padding-left: 20px;
        margin-bottom: 18px;
        border-left: 2px solid var(--line);
    }
    .history-timeline-item::before {
        content: '';
        position: absolute;
        left: -5px;
        top: 4px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--brand);
    }
    .history-meta {
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 2px;
    }
    .history-text {
        font-size: 13.5px;
        color: var(--text);
        font-weight: 500;
    }
</style>

<!-- Top Candidate Header Card -->
<div class="candidate-header-card">
    <div class="header-profile-block">
        <div class="avatar-initials"><?= e($initials) ?></div>
        <div class="header-details">
            <h2><?= e($app['full_name']) ?></h2>
            <div class="header-meta-row">
                <span><?= e($app['post_applied']) ?></span>
                <span>&bull;</span>
                <span class="header-chip" style="background:var(--brand-soft); color:var(--brand);"><?= e(ucfirst($app['applied_for_type'] ?? 'Commercial')) ?></span>
                <span>&bull;</span>
                <span class="header-chip">Submitted <?= date('d M Y, h:i A', strtotime($app['submitted_at'])) ?></span>
                
                <?php if (!empty($repeatApplicants)): ?>
                    <a href="#repeatApplicantsBox" class="header-chip chip-repeat" title="Earlier applications found for this contact">
                        Repeat applicant (<?= count($repeatApplicants) ?> earlier)
                    </a>
                <?php endif; ?>
            </div>

            <div class="header-contacts">
                <a href="tel:<?= e($app['mobile']) ?>" class="contact-link" title="Call candidate">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    <span>+91 <?= e($app['mobile']) ?></span>
                </a>
                <span>&bull;</span>
                <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener noreferrer" class="contact-link" style="color: #25D366;" title="Open WhatsApp chat">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.28-2.42 5.84a8.19 8.19 0 0 1-5.82 2.41c-1.42 0-2.82-.37-4.06-1.07l-.29-.17-3.12.82.83-3.04-.19-.3a8.16 8.16 0 0 1-1.26-4.49c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.25-1.5-1.4-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44s-.56-1.34-.76-1.84c-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.11-.23-.17-.48-.3z"/></svg>
                    <span>WhatsApp</span>
                </a>
                <span>&bull;</span>
                <a href="mailto:<?= e($app['email']) ?>" class="contact-link" style="word-break: break-all;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <span><?= e($app['email']) ?></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Header Action Buttons -->
    <div class="header-buttons-block">
        <button type="button" class="btn-action-outline" onclick="window.location.reload()" aria-label="Refresh application details">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
            <span>Refresh</span>
        </button>

        <a href="<?= url('/admin/interview-candidates/' . $app['id'] . '/pdf') ?>" class="btn-action-outline" aria-label="Download form PDF">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            <span>Download form (PDF)</span>
        </a>

        <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn-whatsapp" aria-label="Message on WhatsApp">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.28-2.42 5.84a8.19 8.19 0 0 1-5.82 2.41c-1.42 0-2.82-.37-4.06-1.07l-.29-.17-3.12.82.83-3.04-.19-.3a8.16 8.16 0 0 1-1.26-4.49c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.25-1.5-1.4-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44s-.56-1.34-.76-1.84c-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.11-.23-.17-.48-.3z"/></svg>
            <span>Message on WhatsApp</span>
        </a>
    </div>
</div>

<!-- Concurrency Conflict Warning Banner (409) -->
<div class="conflict-banner" id="conflictBanner">
    <span>
        <strong id="conflictMsg">This field was updated by another administrator.</strong> Your typed changes are kept visible.
    </span>
    <button type="button" class="btn-action-outline" style="background:#FFFFFF; font-size:12px; height:30px; padding:0 10px;" onclick="window.location.reload()">Reload latest</button>
</div>

<!-- Main 2-Column Grid -->
<div class="detail-grid-layout">

    <!-- LEFT COLUMN: Candidate details (Read-only) -->
    <div class="left-candidate-col">
        <div class="section-card">
            <div class="section-card-header">
                <h3>Candidate details</h3>
                <span style="font-size: 13px; color: var(--muted);">Submission Ref #<?= $app['id'] ?></span>
            </div>
            <div class="section-card-body">
                <div class="details-table-list">
                    <div class="detail-field-item">
                        <div class="detail-label">Post Applied For</div>
                        <div class="detail-value"><?= e($app['post_applied']) ?></div>
                    </div>

                    <div class="detail-field-item">
                        <div class="detail-label">Experience / Fresher</div>
                        <div class="detail-value"><?= ucfirst(e($app['experience_type'])) ?></div>
                    </div>

                    <div class="detail-field-item">
                        <div class="detail-label">Years in Real Estate</div>
                        <div class="detail-value">
                            <?= ($app['experience_type'] === 'experience') ? (($app['years_in_real_estate'] !== null) ? $app['years_in_real_estate'] . ' Years' : '0 Years') : 'N/A (Fresher)' ?>
                        </div>
                    </div>

                    <div class="detail-field-item">
                        <div class="detail-label">Full Name</div>
                        <div class="detail-value"><?= e($app['full_name']) ?></div>
                    </div>

                    <div class="detail-field-item">
                        <div class="detail-label">Mail ID</div>
                        <div class="detail-value"><?= e($app['email']) ?></div>
                    </div>

                    <div class="detail-field-item">
                        <div class="detail-label">Contact Number</div>
                        <div class="detail-value">+91 <?= e($app['mobile']) ?></div>
                    </div>

                    <div class="detail-field-item">
                        <div class="detail-label">Segment (Commercial / Residential)</div>
                        <div class="detail-value">
                            <span class="header-chip" style="background:var(--brand-soft); color:var(--brand); font-weight:700;">
                                <?= e(ucfirst($app['applied_for_type'] ?? 'Commercial')) ?>
                            </span>
                        </div>
                    </div>

                    <div class="detail-field-item">
                        <div class="detail-label">Vehicle Available</div>
                        <div class="detail-value"><?= ucfirst(e($app['vehicle_available'])) ?></div>
                    </div>

                    <div class="detail-field-item">
                        <div class="detail-label">Current Location</div>
                        <div class="detail-value"><?= e($app['current_location']) ?></div>
                    </div>

                    <div class="detail-field-item">
                        <div class="detail-label">Higher Education</div>
                        <div class="detail-value"><?= e($app['higher_education']) ?></div>
                    </div>

                    <div class="detail-field-item">
                        <div class="detail-label">Any References</div>
                        <div class="detail-value"><?= ucfirst(e($app['has_references'])) ?></div>
                    </div>

                    <?php if ($app['has_references'] === 'yes'): ?>
                    <div class="detail-field-item full-width">
                        <div class="detail-label">Reference details</div>
                        <div class="detail-value" style="white-space: pre-wrap;"><?= e($app['reference_details'] ?: 'No details provided') ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if ($app['experience_type'] === 'experience'): ?>
                    <div class="detail-field-item">
                        <div class="detail-label">Last Company HR and TL Name</div>
                        <div class="detail-value"><?= e($app['last_company_hr_tl'] ?: '-') ?></div>
                    </div>

                    <div class="detail-field-item">
                        <div class="detail-label">Reason to Leave</div>
                        <div class="detail-value"><?= e($app['reason_to_leave'] ?: '-') ?></div>
                    </div>
                    <?php endif; ?>

                    <div class="detail-field-item">
                        <div class="detail-label">Immediate Joiner</div>
                        <div class="detail-value"><?= ucfirst(e($app['immediate_joiner'])) ?></div>
                    </div>

                    <div class="detail-field-item">
                        <div class="detail-label">HR Name chosen by the candidate</div>
                        <div class="detail-value"><?= e($app['hr_name']) ?></div>
                    </div>
                </div>

                <!-- Employment Table if Experience -->
                <?php if ($app['experience_type'] === 'experience'): ?>
                <div style="margin-top: 24px;">
                    <div class="detail-label" style="margin-bottom: 8px;">Employment history</div>
                    <div class="emp-history-box">
                        <table class="read-emp-table" aria-label="Employment history">
                            <thead>
                                <tr>
                                    <th>Organisation</th>
                                    <th>Designation</th>
                                    <th>Salary</th>
                                    <th>HR Name &amp; Number</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($app['employment_rows'])): ?>
                                    <tr><td colspan="5" style="color:var(--muted); text-align:center;">No employment records</td></tr>
                                <?php else: ?>
                                    <?php foreach ($app['employment_rows'] as $emp): ?>
                                    <tr>
                                        <td data-col="Organisation"><strong><?= e($emp['organisation'] ?: '-') ?></strong></td>
                                        <td data-col="Designation"><?= e($emp['designation'] ?: '-') ?></td>
                                        <td data-col="Salary"><?= e($emp['salary'] ?: '-') ?></td>
                                        <td data-col="HR Contact"><?= e($emp['hr_name_number'] ?: '-') ?></td>
                                        <td data-col="Duration"><?= e($emp['duration'] ?: '-') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Repeat applicants section if any -->
                <?php if (!empty($repeatApplicants)): ?>
                <div id="repeatApplicantsBox" style="margin-top: 24px; padding: 16px; background: #FFFBF2; border: 1px solid #F3E5C7; border-radius: var(--r-md);">
                    <div style="font-size: 13.5px; font-weight: 700; color: #9A6410; margin-bottom: 8px;">
                        Repeat Applicant History (<?= count($repeatApplicants) ?> earlier submissions)
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <?php foreach ($repeatApplicants as $prev): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px; padding-bottom: 6px; border-bottom: 1px dashed #EEDFBC;">
                                <span>
                                    <strong><?= e($prev['post_applied']) ?></strong> &bull; <?= date('d M Y', strtotime($prev['submitted_at'])) ?>
                                </span>
                                <a href="<?= url('/admin/interview-candidates/' . $prev['id']) ?>" class="btn-action-outline" style="height: 28px; padding: 0 10px; font-size: 12px; background: #FFFFFF;">
                                    View Form #<?= $prev['id'] ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Archive / Restore action -->
                <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 12.5px; color: var(--muted);">Data Retention Policy: Nothing is ever hard-deleted.</span>
                    <?php if (auth()['role'] !== 'reviewer'): ?>
                        <?php if (empty($app['is_archived'])): ?>
                            <form method="POST" action="<?= url('/admin/interview-candidates/' . $app['id'] . '/archive') ?>" onsubmit="return confirm('Archive this candidate application?');">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <button type="submit" class="btn-action-outline" style="color: var(--danger); border-color: #F0C4C1;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"></polyline><rect x="1" y="3" width="22" height="5"></rect><line x1="10" y1="12" x2="14" y2="12"></line></svg>
                                    <span>Archive Application</span>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="<?= url('/admin/interview-candidates/' . $app['id'] . '/restore') ?>">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <button type="submit" class="btn-action-primary">
                                    <span>Restore Application</span>
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: For HR department only (Manual Save & Email Notification) -->
    <div class="right-hr-col">
        <div class="section-card sticky-hr-card">
            <div class="section-card-header">
                <div>
                    <h3>For HR department only</h3>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <!-- Status Badge -->
                    <span class="hr-status-badge is-saved" id="hrStatusBadge">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span id="hrStatusText">Saved</span>
                    </span>

                    <!-- History Drawer Toggle -->
                    <button type="button" class="btn-history" onclick="openHistoryDrawer()" aria-label="View history and email logs">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>History</span>
                    </button>
                </div>
            </div>

            <div class="section-card-body">
                <!-- Save feedback form wrapping all HR inputs -->
                <form id="hrFeedbackForm" method="POST" action="<?= url('/admin/interview-candidates/' . $app['id'] . '/save-feedback') ?>">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="version" id="hrFormVersion" value="<?= (int)$app['version'] ?>">

                    <!-- Alert message container -->
                    <div id="hrSaveAlert" class="hr-save-alert" style="display:none;"></div>

                    <!-- 1. Decision: segmented radio "Pending", "Selected", "Rejected" -->
                    <div class="hr-form-group">
                        <label class="hr-label">Decision</label>
                        <div class="segmented-control" role="radiogroup" aria-label="Candidate Decision">
                            <div class="segmented-option">
                                <input type="radio" name="decision" id="dec_pending" value="pending" <?= ($app['decision'] === 'pending') ? 'checked' : '' ?> <?= $isReadOnly ? 'disabled' : '' ?>>
                                <label for="dec_pending" class="segmented-label">Pending</label>
                            </div>
                            <div class="segmented-option is-selected">
                                <input type="radio" name="decision" id="dec_selected" value="selected" <?= ($app['decision'] === 'selected') ? 'checked' : '' ?> <?= $isReadOnly ? 'disabled' : '' ?>>
                                <label for="dec_selected" class="segmented-label">Selected</label>
                            </div>
                            <div class="segmented-option is-rejected">
                                <input type="radio" name="decision" id="dec_rejected" value="rejected" <?= ($app['decision'] === 'rejected') ? 'checked' : '' ?> <?= $isReadOnly ? 'disabled' : '' ?>>
                                <label for="dec_rejected" class="segmented-label">Rejected</label>
                            </div>
                        </div>
                    </div>

                    <!-- Subfields: When Decision = Selected -->
                    <div class="subfields-box" id="boxSelectedFields" style="display: <?= ($app['decision'] === 'selected') ? 'block' : 'none' ?>;">
                        <div style="font-size: 13px; font-weight: 700; color: #226B35; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                            <span>Selection Offer Details</span>
                            <span class="header-chip" id="chipIncomplete" style="background:#FEF3D6; color:#9A6410; display: <?= $isDetailsIncomplete ? 'inline-flex' : 'none' ?>;">Details incomplete</span>
                        </div>

                        <!-- Candidate interested (Pill Radio "Yes" / "No") -->
                        <div class="hr-form-group">
                            <label class="hr-label">Candidate interested</label>
                            <div class="pill-radio-group">
                                <div class="pill-radio-item">
                                    <input type="radio" class="pill-radio-input" id="ci_yes" name="candidate_interested" value="yes" <?= ($app['candidate_interested'] === 'yes') ? 'checked' : '' ?> <?= $isReadOnly ? 'disabled' : '' ?>>
                                    <label class="pill-radio-label" for="ci_yes">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        <span>Yes</span>
                                    </label>
                                </div>
                                <div class="pill-radio-item">
                                    <input type="radio" class="pill-radio-input" id="ci_no" name="candidate_interested" value="no" <?= ($app['candidate_interested'] === 'no') ? 'checked' : '' ?> <?= $isReadOnly ? 'disabled' : '' ?>>
                                    <label class="pill-radio-label" for="ci_no">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        <span>No</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Position Offered (text) -->
                        <div class="hr-form-group">
                            <label for="hr_position_offered" class="hr-label">Position Offered</label>
                            <input type="text" id="hr_position_offered" name="position_offered" class="hr-control" placeholder="e.g. Associate Sales Manager" value="<?= e($app['position_offered'] ?? '') ?>" <?= $isReadOnly ? 'disabled' : '' ?>>
                        </div>

                        <!-- Offered Salary with rupee prefix -->
                        <div class="hr-form-group">
                            <label for="hr_offered_salary" class="hr-label">Offered Salary</label>
                            <div class="input-with-symbol">
                                <span class="input-symbol-prefix">₹</span>
                                <input type="text" id="hr_offered_salary" name="offered_salary" class="hr-control" placeholder="e.g. 4,50,000" value="<?= $app['offered_salary'] ? number_format((float)$app['offered_salary'], 0) : '' ?>" <?= $isReadOnly ? 'disabled' : '' ?>>
                            </div>
                        </div>

                        <!-- Date of Joining (date picker, enabled & required only when interested = Yes, not before submitted date) -->
                        <div class="hr-form-group">
                            <label for="hr_joining_date" class="hr-label">Date of Joining</label>
                            <?php
                            $minDate = !empty($app['submitted_at']) ? date('Y-m-d', strtotime($app['submitted_at'])) : date('Y-m-d');
                            $joiningDisabled = ($isReadOnly || $app['candidate_interested'] !== 'yes');
                            ?>
                            <input type="date" id="hr_joining_date" name="joining_date" min="<?= e($minDate) ?>" class="hr-control" value="<?= e($app['joining_date'] ?? '') ?>" <?= $joiningDisabled ? 'disabled' : '' ?>>
                        </div>
                    </div>

                    <!-- Subfields: When Decision = Rejected -->
                    <div class="subfields-box" id="boxRejectedFields" style="display: <?= ($app['decision'] === 'rejected') ? 'block' : 'none' ?>; background: #FFF5F5; border-color: #F8D7DA;">
                        <div style="font-size: 13px; font-weight: 700; color: #B3261E; margin-bottom: 10px;">Rejection Information</div>
                        <div class="hr-form-group" style="margin-bottom: 0;">
                            <label for="hr_rejection_reason" class="hr-label">Rejection reason (internal notes)</label>
                            <textarea id="hr_rejection_reason" name="rejection_reason" class="hr-control" placeholder="Reason for rejection (optional)..." <?= $isReadOnly ? 'disabled' : '' ?>><?= e($app['rejection_reason'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- 2. Source By (text) -->
                    <div class="hr-form-group">
                        <label for="hr_source_by" class="hr-label">Source By</label>
                        <input type="text" id="hr_source_by" name="source_by" class="hr-control" placeholder="e.g. Campus, Referral, Job Portal" value="<?= e($app['source_by'] ?? '') ?>" <?= $isReadOnly ? 'disabled' : '' ?>>
                    </div>

                    <!-- 3. Feedback by (TL / SM) (textarea) -->
                    <div class="hr-form-group">
                        <label for="hr_feedback_tl_sm" class="hr-label">Feedback by (TL / SM)</label>
                        <textarea id="hr_feedback_tl_sm" name="feedback_tl_sm" class="hr-control" placeholder="TL / Sales Manager remarks..." <?= $isReadOnly ? 'disabled' : '' ?>><?= e($app['feedback_tl_sm'] ?? '') ?></textarea>
                    </div>

                    <!-- 4. Feedback by HR (textarea) -->
                    <div class="hr-form-group">
                        <label for="hr_feedback_hr" class="hr-label">Feedback by HR</label>
                        <textarea id="hr_feedback_hr" name="feedback_hr" class="hr-control" placeholder="HR evaluation and comments..." <?= $isReadOnly ? 'disabled' : '' ?>><?= e($app['feedback_hr'] ?? '') ?></textarea>
                    </div>

                    <!-- 5. Feedback Final (textarea) -->
                    <div class="hr-form-group">
                        <label for="hr_feedback_final" class="hr-label">Feedback Final</label>
                        <textarea id="hr_feedback_final" name="feedback_final" class="hr-control" placeholder="Final panel feedback..." <?= $isReadOnly ? 'disabled' : '' ?>><?= e($app['feedback_final'] ?? '') ?></textarea>
                    </div>

                    <!-- 6. Module Discussed (textarea) -->
                    <div class="hr-form-group">
                        <label for="hr_module_discussed" class="hr-label">Module Discussed</label>
                        <textarea id="hr_module_discussed" name="module_discussed" class="hr-control" placeholder="Curriculum / modules / department discussed..." <?= $isReadOnly ? 'disabled' : '' ?>><?= e($app['module_discussed'] ?? '') ?></textarea>
                    </div>

                    <!-- Email Notification Option Checkbox -->
                    <div class="email-toggle-card">
                        <label class="email-toggle-label" for="send_candidate_email">
                            <input type="checkbox" name="send_candidate_email" id="send_candidate_email" class="email-toggle-checkbox" value="1" checked <?= $isReadOnly ? 'disabled' : '' ?>>
                            <div class="email-toggle-info">
                                <span class="email-toggle-title">Send automated email to candidate on save</span>
                                <span class="email-toggle-sub">Sends official offer or rejection letter to <strong><?= e($app['email']) ?></strong> once you save.</span>
                            </div>
                        </label>
                    </div>

                    <!-- Prominent Save Button -->
                    <?php if (!$isReadOnly): ?>
                    <div style="margin-top: 10px;">
                        <button type="submit" id="btnSaveFeedback" class="btn-save-hr">
                            <svg class="save-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                            <span id="btnSaveText">Save Feedback &amp; Decision</span>
                        </button>
                    </div>
                    <?php endif; ?>

                    <!-- Footer Status & Automatic Computed Chip -->
                    <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--line); display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                            <span class="header-chip <?= $isFeedbackFilled ? 'feedback-filled' : 'feedback-pending' ?>" id="chipFeedbackStatus" style="font-size: 12px;">
                                <?= $isFeedbackFilled ? 'Feedback filled' : 'Feedback pending' ?>
                            </span>

                            <span style="font-size: 12px; color: var(--muted);" id="lastUpdatedByText">
                                <?= $lastUpdatedFormatted ?>
                            </span>
                        </div>

                        <?php if ($isReadOnly): ?>
                            <div style="font-size: 12px; color: var(--muted); font-style: italic;">
                                Reviewer account: Read-only access enabled.
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- History Drawer Modal -->
<div class="drawer-overlay" id="drawerOverlay" onclick="closeHistoryDrawer()"></div>
<div class="drawer-panel" id="historyDrawer" role="dialog" aria-label="Application Change History and Email Logs">
    <div class="drawer-header">
        <h3>History &amp; Email Logs</h3>
        <button type="button" class="btn-action-outline" style="border:none; padding:4px;" onclick="closeHistoryDrawer()" aria-label="Close drawer">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    <div class="drawer-body">
        <h4 style="font-size: 14px; font-weight: 700; color: var(--brand); margin-bottom: 12px; text-transform: uppercase;">Audit Trail (Recent Changes)</h4>
        <?php if (empty($app['field_versions'])): ?>
            <p style="font-size: 13px; color: var(--muted);">No field changes recorded yet.</p>
        <?php else: ?>
            <?php foreach ($app['field_versions'] as $fv): ?>
            <div class="history-timeline-item">
                <div class="history-meta"><?= date('d M Y, h:i:s A', strtotime($fv['updated_at'])) ?> &bull; <?= e($fv['admin_name'] ?: 'Admin') ?></div>
                <div class="history-text">
                    Updated <strong><?= e(str_replace('_', ' ', $fv['field'])) ?></strong>
                    <?php if (!empty($fv['value'])): ?>
                        to "<em><?= e(mb_strimwidth($fv['value'], 0, 80, '...')) ?></em>"
                    <?php else: ?>
                        (cleared)
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <h4 style="font-size: 14px; font-weight: 700; color: var(--brand); margin-top: 24px; margin-bottom: 12px; text-transform: uppercase;">Dispatched Emails</h4>
        <?php if (empty($app['email_logs'])): ?>
            <p style="font-size: 13px; color: var(--muted);">No automated emails logged yet.</p>
        <?php else: ?>
            <?php foreach ($app['email_logs'] as $el): ?>
            <div class="history-timeline-item">
                <div class="history-meta"><?= date('d M Y, h:i A', strtotime($el['created_at'])) ?> &bull; To: <?= e($el['to_email']) ?></div>
                <div class="history-text">
                    Type: <strong><?= e($el['type']) ?></strong> &bull; Status: 
                    <span style="color: <?= ($el['status'] === 'sent') ? '#226B35' : '#B3261E' ?>; font-weight:700; text-transform:capitalize;">
                        <?= e($el['status']) ?>
                    </span>
                    <?php if (!empty($el['error'])): ?>
                        <div style="font-size: 11.5px; color: var(--danger); margin-top: 2px;"><?= e($el['error']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
(function() {
    const applicationId = <?= (int)$app['id'] ?>;
    let recordVersion = <?= (int)$app['version'] ?>;
    const isReadOnly = <?= $isReadOnly ? 'true' : 'false' ?>;

    const hrFeedbackForm = document.getElementById('hrFeedbackForm');
    const btnSaveFeedback = document.getElementById('btnSaveFeedback');
    const hrStatusBadge = document.getElementById('hrStatusBadge');
    const hrSaveAlert = document.getElementById('hrSaveAlert');
    const lastUpdatedByText = document.getElementById('lastUpdatedByText');
    const boxSelectedFields = document.getElementById('boxSelectedFields');
    const boxRejectedFields = document.getElementById('boxRejectedFields');
    const joinDateEl = document.getElementById('hr_joining_date');
    const salaryInp = document.getElementById('hr_offered_salary');

    let isDirty = false;

    if (isReadOnly) return;

    function markDirty() {
        if (!isDirty) {
            isDirty = true;
            if (hrStatusBadge) {
                hrStatusBadge.className = 'hr-status-badge is-unsaved';
                hrStatusBadge.innerHTML = `<span style="font-size:11px; color:#D97706;">●</span> <span>Unsaved changes</span>`;
            }
        }
    }

    function markSaved(text = 'Saved') {
        isDirty = false;
        if (hrStatusBadge) {
            hrStatusBadge.className = 'hr-status-badge is-saved';
            hrStatusBadge.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> <span>${text}</span>`;
        }
    }

    function markSaving() {
        if (hrStatusBadge) {
            hrStatusBadge.className = 'hr-status-badge is-saving';
            hrStatusBadge.innerHTML = `<svg class="spin" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10"></path></svg> <span>Saving...</span>`;
        }
    }

    function markError(text = 'Save failed') {
        if (hrStatusBadge) {
            hrStatusBadge.className = 'hr-status-badge is-error';
            hrStatusBadge.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg> <span>${text}</span>`;
        }
    }

    // Unsaved changes browser warning
    window.addEventListener('beforeunload', (e) => {
        if (isDirty) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes in HR feedback.';
        }
    });

    // 1. Decision radio changes
    document.querySelectorAll('input[name="decision"]').forEach(r => {
        r.addEventListener('change', function() {
            const dec = this.value;
            if (boxSelectedFields) boxSelectedFields.style.display = (dec === 'selected') ? 'block' : 'none';
            if (boxRejectedFields) boxRejectedFields.style.display = (dec === 'rejected') ? 'block' : 'none';
            updateIncompleteBadge();
            markDirty();
        });
    });

    // 2. Candidate interested radios
    document.querySelectorAll('input[name="candidate_interested"]').forEach(r => {
        r.addEventListener('change', function() {
            const isYes = (this.value === 'yes');
            if (joinDateEl) {
                joinDateEl.disabled = !isYes;
                if (!isYes) joinDateEl.value = '';
            }
            updateIncompleteBadge();
            markDirty();
        });
    });

    // 3. Date picker change
    if (joinDateEl) {
        joinDateEl.addEventListener('input', () => { updateIncompleteBadge(); markDirty(); });
        joinDateEl.addEventListener('change', () => { updateIncompleteBadge(); markDirty(); });
    }

    // 4. Currency formatting helper on offered_salary
    if (salaryInp) {
        salaryInp.addEventListener('blur', function() {
            let num = this.value.replace(/[^\d.]/g, '');
            if (num && !isNaN(num)) {
                this.value = Number(num).toLocaleString('en-IN');
            }
        });
    }

    // 5. Track changes on all inputs and textareas inside the HR form
    if (hrFeedbackForm) {
        hrFeedbackForm.querySelectorAll('input, textarea, select').forEach(el => {
            el.addEventListener('input', () => {
                markDirty();
                updateFeedbackFilledBadge();
                updateIncompleteBadge();
            });
            el.addEventListener('change', () => {
                markDirty();
                updateFeedbackFilledBadge();
                updateIncompleteBadge();
            });
        });

        // 6. Manual form submission
        hrFeedbackForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!btnSaveFeedback) return;

            // Hide previous alerts
            if (hrSaveAlert) {
                hrSaveAlert.style.display = 'none';
                hrSaveAlert.innerHTML = '';
            }

            const originalBtnHtml = btnSaveFeedback.innerHTML;
            btnSaveFeedback.disabled = true;
            btnSaveFeedback.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="animation:spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle><path d="M12 2a10 10 0 0 1 10 10"></path></svg> <span>Saving Feedback...</span>`;
            markSaving();

            try {
                const formData = new FormData(hrFeedbackForm);
                const res = await fetch(hrFeedbackForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const json = await res.json();

                if (!res.ok || !json.success) {
                    throw new Error(json.error || 'Failed to save HR feedback.');
                }

                // Success
                markSaved('Saved');
                if (json.version) {
                    const verInput = document.getElementById('hrFormVersion');
                    if (verInput) verInput.value = json.version;
                    recordVersion = json.version;
                }
                if (json.last_updated_text && lastUpdatedByText) {
                    lastUpdatedByText.textContent = json.last_updated_text;
                }

                if (hrSaveAlert) {
                    let msg = json.message || 'HR details and feedback saved successfully.';
                    hrSaveAlert.className = 'hr-save-alert alert-success';
                    hrSaveAlert.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg><div><strong>Saved!</strong> ${msg}</div>`;
                    hrSaveAlert.style.display = 'flex';
                }

                updateFeedbackFilledBadge();
                updateIncompleteBadge();

            } catch (err) {
                markError('Failed');
                if (hrSaveAlert) {
                    hrSaveAlert.className = 'hr-save-alert alert-error';
                    hrSaveAlert.innerHTML = `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg><div><strong>Error:</strong> ${err.message}</div>`;
                    hrSaveAlert.style.display = 'flex';
                }
            } finally {
                btnSaveFeedback.disabled = false;
                btnSaveFeedback.innerHTML = originalBtnHtml;
            }
        });
    }

    // Update "Feedback filled" chip automatically
    function updateFeedbackFilledBadge() {
        const fbHr = document.getElementById('hr_feedback_hr')?.value.trim();
        const fbFinal = document.getElementById('hr_feedback_final')?.value.trim();
        const chip = document.getElementById('chipFeedbackStatus');
        if (!chip) return;
        if (fbHr || fbFinal) {
            chip.className = 'header-chip feedback-filled';
            chip.textContent = 'Feedback filled';
        } else {
            chip.className = 'header-chip feedback-pending';
            chip.textContent = 'Feedback pending';
        }
    }

    // Update "Details incomplete" chip automatically for Selected
    function updateIncompleteBadge() {
        const dec = document.querySelector('input[name="decision"]:checked')?.value;
        const chip = document.getElementById('chipIncomplete');
        if (!chip) return;
        if (dec !== 'selected') {
            chip.style.display = 'none';
            return;
        }

        const pos = document.getElementById('hr_position_offered')?.value.trim();
        const sal = document.getElementById('hr_offered_salary')?.value.trim();
        const interested = document.querySelector('input[name="candidate_interested"]:checked')?.value;
        const joinDate = document.getElementById('hr_joining_date')?.value;

        let isIncomplete = false;
        if (!pos || !sal) {
            isIncomplete = true;
        } else if (interested === 'yes' && !joinDate) {
            isIncomplete = true;
        }

        chip.style.display = isIncomplete ? 'inline-flex' : 'none';
    }

})();

// Drawer functions
function openHistoryDrawer() {
    document.getElementById('drawerOverlay').style.display = 'block';
    document.getElementById('historyDrawer').classList.add('open');
}
function closeHistoryDrawer() {
    document.getElementById('drawerOverlay').style.display = 'none';
    document.getElementById('historyDrawer').classList.remove('open');
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/admin.php';
?>
