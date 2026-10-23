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

        <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener noreferrer" class="btn-action-primary" style="background:#25D366; border-color:#25D366;" aria-label="Message on WhatsApp">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2m.01 1.67c4.54 0 8.24 3.7 8.24 8.24 0 2.2-.86 4.28-2.42 5.84a8.19 8.19 0 0 1-5.82 2.41c-1.42 0-2.82-.37-4.06-1.07l-.29-.17-3.12.82.83-3.04-.19-.3a8.16 8.16 0 0 1-1.26-4.49c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.02-1.25-.75-.67-1.25-1.5-1.4-1.75-.14-.25-.02-.39.11-.51.11-.11.25-.29.38-.44.13-.14.17-.25.25-.42.08-.17.04-.31-.02-.44s-.56-1.34-.76-1.84c-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.13.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.11-.23-.17-.48-.3z"/></svg>
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
                        <div class="detail-label">Segment (Commercial / Residential)</div>
                        <div class="detail-value">
                            <span class="header-chip" style="background:var(--brand-soft); color:var(--brand); font-weight:700;">
                                <?= e(ucfirst($app['applied_for_type'] ?? 'Commercial')) ?>
                            </span>
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

    <!-- RIGHT COLUMN: For HR department only (Editable, Sticky, Autosave) -->
    <div class="right-hr-col">
        <div class="section-card sticky-hr-card">
            <div class="section-card-header">
                <div>
                    <h3>For HR department only</h3>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <!-- Autosave Indicator -->
                    <span class="autosave-status-pill status-saved" id="autosaveStatus">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span id="autosaveText">Saved</span>
                    </span>

                    <!-- History Drawer Toggle -->
                    <button type="button" class="btn-action-outline" style="height: 32px; padding: 0 10px; font-size: 12.5px;" onclick="openHistoryDrawer()" aria-label="View history and email logs">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>History</span>
                    </button>
                </div>
            </div>

            <div class="section-card-body">
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

                    <!-- Candidate interested (RADIO "Yes" / "No") -->
                    <div class="hr-form-group">
                        <label class="hr-label">Candidate interested</label>
                        <div style="display: flex; gap: 16px;">
                            <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 14px; cursor: pointer;">
                                <input type="radio" name="candidate_interested" value="yes" <?= ($app['candidate_interested'] === 'yes') ? 'checked' : '' ?> <?= $isReadOnly ? 'disabled' : '' ?> style="accent-color: var(--brand);">
                                <span>Yes</span>
                            </label>
                            <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 14px; cursor: pointer;">
                                <input type="radio" name="candidate_interested" value="no" <?= ($app['candidate_interested'] === 'no') ? 'checked' : '' ?> <?= $isReadOnly ? 'disabled' : '' ?> style="accent-color: var(--brand);">
                                <span>No</span>
                            </label>
                        </div>
                    </div>

                    <!-- Position Offered (text) -->
                    <div class="hr-form-group">
                        <label for="hr_position_offered" class="hr-label">Position Offered</label>
                        <input type="text" id="hr_position_offered" name="position_offered" class="hr-control" placeholder="e.g. Associate Sales Manager" value="<?= e($app['position_offered'] ?? '') ?>" <?= $isReadOnly ? 'disabled' : '' ?>>
                    </div>

                    <!-- Offered Salary (number with rupee prefix, thousands separators, no negatives) -->
                    <div class="hr-form-group">
                        <label for="hr_offered_salary" class="hr-label">Offered Salary (₹)</label>
                        <input type="text" id="hr_offered_salary" name="offered_salary" class="hr-control" placeholder="e.g. 4,50,000" value="<?= $app['offered_salary'] ? number_format((float)$app['offered_salary'], 0) : '' ?>" <?= $isReadOnly ? 'disabled' : '' ?>>
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

                <!-- Footer Status & Automatic Computed Chip -->
                <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--line); display: flex; flex-direction: column; gap: 8px;">
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

    const statusPill = document.getElementById('autosaveStatus');
    const statusText = document.getElementById('autosaveText');
    const conflictBanner = document.getElementById('conflictBanner');
    const conflictMsg = document.getElementById('conflictMsg');
    const lastUpdatedByText = document.getElementById('lastUpdatedByText');

    let debounceTimers = {};
    let pendingQueue = [];
    let isSaving = false;
    let currentlyEditingFields = new Set();

    if (isReadOnly) return;

    // Track active typing so background polling doesn't overwrite
    document.querySelectorAll('.hr-control').forEach(el => {
        el.addEventListener('focus', () => currentlyEditingFields.add(el.name));
        el.addEventListener('blur', () => currentlyEditingFields.delete(el.name));
    });

    // Save state UI indicators
    function setAutosaveState(state, text = '') {
        statusPill.className = 'autosave-status-pill';
        if (state === 'saving') {
            statusPill.classList.add('status-saving');
            statusText.textContent = text || 'Saving...';
        } else if (state === 'saved') {
            statusPill.classList.add('status-saved');
            statusText.textContent = text || 'Saved';
        } else if (state === 'error') {
            statusPill.classList.add('status-error');
            statusText.innerHTML = `Not saved - <a href="javascript:void(0)" onclick="retryPendingSaves()" style="color:inherit; text-decoration:underline;">retry</a>`;
        }
    }

    // Unsaved changes browser warning
    window.addEventListener('beforeunload', (e) => {
        if (pendingQueue.length > 0 || isSaving) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes.';
        }
    });

    // 1. Immediate save on radios and date inputs
    // Decision radios
    document.querySelectorAll('input[name="decision"]').forEach(r => {
        r.addEventListener('change', function() {
            const dec = this.value;
            // Toggle subfields visibility
            document.getElementById('boxSelectedFields').style.display = (dec === 'selected') ? 'block' : 'none';
            document.getElementById('boxRejectedFields').style.display = (dec === 'rejected') ? 'block' : 'none';
            updateIncompleteBadge();
            queueSave('decision', dec);
        });
    });

    // Candidate interested radios
    document.querySelectorAll('input[name="candidate_interested"]').forEach(r => {
        r.addEventListener('change', function() {
            const isYes = (this.value === 'yes');
            const dateInp = document.getElementById('hr_joining_date');
            dateInp.disabled = !isYes;
            if (!isYes) {
                dateInp.value = '';
                queueSave('joining_date', '');
            }
            updateIncompleteBadge();
            queueSave('candidate_interested', this.value);
        });
    });

    // Date picker immediate save (listen to change, input, and blur)
    const joinDateEl = document.getElementById('hr_joining_date');
    if (joinDateEl) {
        ['change', 'input', 'blur'].forEach(ev => {
            joinDateEl.addEventListener(ev, function() {
                updateIncompleteBadge();
                queueSave('joining_date', this.value);
            });
        });
    }

    // 2. Debounced save (600ms) and blur save on text/textarea/number fields
    ['source_by', 'position_offered', 'offered_salary', 'rejection_reason', 'feedback_tl_sm', 'feedback_hr', 'feedback_final', 'module_discussed'].forEach(name => {
        const el = document.querySelector(`[name="${name}"]`);
        if (!el) return;

        el.addEventListener('input', function() {
            setAutosaveState('saving');
            clearTimeout(debounceTimers[name]);
            debounceTimers[name] = setTimeout(() => {
                triggerFieldSave(name, el.value);
            }, 600);
        });

        el.addEventListener('blur', function() {
            clearTimeout(debounceTimers[name]);
            triggerFieldSave(name, el.value);
        });
    });

    // Currency formatting helper on offered_salary
    const salaryInp = document.getElementById('hr_offered_salary');
    if (salaryInp) {
        salaryInp.addEventListener('blur', function() {
            let num = this.value.replace(/[^\d.]/g, '');
            if (num && !isNaN(num)) {
                this.value = Number(num).toLocaleString('en-IN');
            }
        });
    }

    function triggerFieldSave(name, val) {
        let cleanVal = val;
        if (name === 'offered_salary') {
            cleanVal = val.replace(/[^\d.]/g, '');
            if (cleanVal !== '' && Number(cleanVal) < 0) cleanVal = '0';
        }
        updateFeedbackFilledBadge();
        updateIncompleteBadge();
        queueSave(name, cleanVal);
    }

    function queueSave(field, value) {
        // Replace existing pending entry for the same field
        pendingQueue = pendingQueue.filter(item => item.field !== field);
        pendingQueue.push({ field, value });
        setAutosaveState('saving');
        processQueue();
    }

    async function processQueue() {
        if (isSaving || pendingQueue.length === 0) return;

        isSaving = true;
        setAutosaveState('saving');

        const item = pendingQueue.shift();

        try {
            const res = await fetch(`<?= url('/api/interview/') ?>${applicationId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    field: item.field,
                    value: item.value,
                    version: recordVersion,
                    csrf_token: '<?= csrf_token() ?>'
                })
            });

            if (res.status === 409) {
                // Concurrency conflict
                const json = await res.json();
                conflictMsg.textContent = `The field "${item.field.replace('_', ' ')}" was updated by ${json.updated_by_name || 'another administrator'}.`;
                conflictBanner.style.display = 'flex';
                if (json.version) recordVersion = json.version;
            } else if (!res.ok) {
                throw new Error('Save HTTP error: ' + res.status);
            } else {
                const json = await res.json();
                if (json.success) {
                    if (json.version) recordVersion = json.version;
                    if (json.last_updated_text) lastUpdatedByText.textContent = json.last_updated_text;
                } else {
                    throw new Error(json.error || 'Autosave error');
                }
            }
        } catch (err) {
            // Re-queue for retry
            pendingQueue.unshift(item);
            setAutosaveState('error');
        } finally {
            isSaving = false;
            if (pendingQueue.length > 0) {
                processQueue();
            } else {
                setAutosaveState('saved');
            }
        }
    }

    window.retryPendingSaves = function() {
        processQueue();
    };

    // Online event retry
    window.addEventListener('online', () => {
        if (pendingQueue.length > 0) processQueue();
    });

    // Update "Feedback filled" chip automatically
    function updateFeedbackFilledBadge() {
        const fbHr = document.getElementById('hr_feedback_hr')?.value.trim();
        const fbFinal = document.getElementById('hr_feedback_final')?.value.trim();
        const chip = document.getElementById('chipFeedbackStatus');
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

    // 15-second background polling to refresh unedited fields
    setInterval(async () => {
        if (document.hidden || isSaving || pendingQueue.length > 0) return;
        try {
            const res = await fetch(`<?= url('/api/interview/updates') ?>?since=<?= date('Y-m-d H:i:s', strtotime('-1 minute')) ?>`);
            if (!res.ok) return;
            const json = await res.json();
            const rows = json.data?.rows || [];
            const myRow = rows.find(r => r.id === applicationId);
            if (myRow && myRow.version > recordVersion) {
                // If the user is NOT actively typing, safe to silently update displayed fields
                if (currentlyEditingFields.size === 0) {
                    recordVersion = myRow.version;
                }
            }
        } catch(e) {}
    }, 15000);

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
