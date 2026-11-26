<?php
ob_start();
$pageTitle = "Assessments & Question Sets | Admin Portal";
$viewTitle = "Assessments";
?>

<style>
    .assessments-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }

    .assessments-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--text);
        letter-spacing: -0.02em;
        margin: 0 0 4px 0;
    }

    .assessments-subtitle {
        color: var(--muted);
        font-size: 14px;
        margin: 0;
    }

    .assessments-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .btn-create-link {
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
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s ease;
    }

    .btn-create-link:hover {
        background: var(--brand-hover);
    }

    .btn-outline-action {
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
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-outline-action:hover {
        background: var(--surface-2);
    }

    /* Sets Grid */
    .sets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .set-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        padding: 20px;
    }

    .set-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .set-badge {
        font-size: 13px;
        font-weight: 700;
        background: var(--brand-soft);
        color: var(--brand);
        padding: 4px 10px;
        border-radius: var(--r-full);
    }

    .set-assigned-count {
        font-size: 12px;
        color: var(--muted);
        font-weight: 500;
    }

    .set-name {
        font-size: 16px;
        font-weight: 600;
        color: var(--text);
        margin: 0 0 4px 0;
    }

    .set-meta {
        font-size: 13px;
        color: var(--muted);
    }

    /* Section Card */
    .section-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        margin-bottom: 28px;
        overflow: hidden;
    }

    .section-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--line);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .section-card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }

    .data-table th {
        background: var(--surface-2);
        color: var(--muted);
        font-weight: 600;
        font-size: 12.5px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 12px 20px;
        border-bottom: 1px solid var(--line);
        white-space: nowrap;
    }

    .data-table td {
        padding: 14px 20px;
        border-bottom: 1px solid var(--line);
        color: var(--text);
        vertical-align: middle;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .data-table tr:hover td {
        background: rgba(31, 92, 74, 0.02);
    }

    .chip {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        font-size: 12px;
        font-weight: 500;
        border-radius: var(--r-full);
        line-height: 1;
    }

    .chip-open {
        background: var(--surface-2);
        color: var(--text);
        border: 1px solid var(--line);
    }

    .chip-linked {
        background: var(--brand-soft);
        color: var(--brand);
    }

    .chip-active {
        background: var(--brand-soft);
        color: var(--brand);
    }

    .chip-in-progress {
        background: #FDF4E6;
        color: #8C5311;
    }

    .chip-submitted {
        background: var(--brand-soft);
        color: var(--brand);
    }

    .chip-expired {
        background: var(--surface-2);
        color: var(--muted);
    }

    .chip-revoked {
        background: var(--danger-soft);
        color: var(--danger);
    }

    .action-icons-wrap {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
    }

    .btn-icon-action {
        width: 32px;
        height: 32px;
        border-radius: var(--r-sm);
        border: 1px solid var(--line);
        background: var(--surface);
        color: var(--text);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    .btn-icon-action:hover {
        background: var(--surface-2);
        border-color: var(--muted);
    }

    .btn-icon-danger:hover {
        background: var(--danger-soft);
        color: var(--danger);
        border-color: var(--danger);
    }

    /* Modal Styling */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(38, 51, 45, 0.4);
        z-index: 200;
        align-items: center;
        justify-content: center;
        padding: 20px;
        backdrop-filter: blur(2px);
    }

    .modal-card {
        background: var(--surface);
        border-radius: var(--r-lg);
        border: 1px solid var(--line);
        box-shadow: 0 16px 40px rgba(38, 51, 45, 0.12);
        width: 100%;
        max-width: 500px;
        padding: 28px;
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
        margin: 0;
    }

    .modal-close {
        background: transparent;
        border: none;
        color: var(--muted);
        cursor: pointer;
        padding: 4px;
        border-radius: var(--r-sm);
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: var(--muted);
        margin-bottom: 6px;
    }

    .form-input, .form-select {
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

    .form-input:focus, .form-select:focus {
        border-color: var(--brand);
    }

    .type-radio-group {
        display: flex;
        gap: 16px;
        margin-bottom: 16px;
    }

    .type-radio-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 500;
        color: var(--text);
        cursor: pointer;
    }
</style>

<!-- Top Title + Action Bar -->
<div class="assessments-header">
    <div>
        <h1 class="assessments-title">Aptitude Assessments</h1>
        <p class="assessments-subtitle">
            5 balanced question sets (15 questions each) with guest test link distribution
        </p>
    </div>

    <div class="assessments-actions">
        <a href="<?= url('/admin/aptitude-candidates') ?>" class="btn-outline-action" aria-label="Aptitude Test Candidates">
            <svg width="16" height="16" aria-hidden="true"><use href="#icon-users"></use></svg>
            <span>Aptitude Candidates</span>
        </a>
        <a href="<?= url('/admin/qr-codes') ?>" class="btn-outline-action" aria-label="QR Code Management">
            <svg width="16" height="16" aria-hidden="true"><use href="#icon-award"></use></svg>
            <span>QR Codes</span>
        </a>
        <button type="button" class="btn-create-link" onclick="openCreateLinkModal()" aria-label="Create guest test link">
            <svg width="16" height="16" aria-hidden="true"><use href="#icon-plus"></use></svg>
            <span>Create link</span>
        </button>
        <a href="<?= url('/admin/questions') ?>" class="btn-outline-action" aria-label="View aptitude question bank">
            <svg width="16" height="16" aria-hidden="true"><use href="#icon-award"></use></svg>
            <span>Question Bank</span>
        </a>
    </div>
</div>

<!-- 5 Question Sets Grid -->
<div class="sets-grid">
    <?php foreach ($sets as $s): ?>
        <div class="set-card">
            <div class="set-card-header">
                <span class="set-badge"><?= e($s['set_code']) ?></span>
                <span class="set-assigned-count"><?= (int)$s['times_assigned'] ?> assigned</span>
            </div>
            <h2 class="set-name"><?= e($s['name']) ?></h2>
            <div class="set-meta">
                <?= (int)($s['question_count'] ?? 15) ?> Questions &bull; 10 Mins &bull; 50% Pass
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Guest Links Table Section -->
<div class="section-card">
    <div class="section-card-header">
        <h2 class="section-card-title">
            <svg width="18" height="18" aria-hidden="true"><use href="#icon-link"></use></svg>
            <span>Guest links</span>
        </h2>
        <button type="button" class="btn-create-link" style="height: 36px; padding: 0 14px; font-size: 13px;" onclick="openCreateLinkModal()" aria-label="Create link in guest section">
            <svg width="14" height="14" aria-hidden="true"><use href="#icon-plus"></use></svg>
            <span>Create link</span>
        </button>
    </div>

    <div class="table-container">
        <table class="data-table" aria-label="Guest test links table">
            <thead>
                <tr>
                    <th>Person</th>
                    <th>Kind</th>
                    <th>Set</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Expires</th>
                    <th>Score</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($guestLinks)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 48px 20px; color: var(--muted);">
                            No guest test links created yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($guestLinks as $link): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: var(--text);"><?= e($link['person_name']) ?></div>
                                <?php if (!empty($link['person_email']) || !empty($link['person_mobile'])): ?>
                                    <div style="font-size: 12.5px; color: var(--muted);">
                                        <?= e($link['person_email'] ?: $link['person_mobile']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($link['user_id'])): ?>
                                    <span class="chip chip-linked">Linked</span>
                                <?php else: ?>
                                    <span class="chip chip-open">Open</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-weight: 600; font-size: 13px;"><?= e($link['set_name']) ?></span>
                            </td>
                            <td>
                                <?php
                                $statusClass = match($link['status']) {
                                    'active' => 'chip-active',
                                    'in_progress' => 'chip-in-progress',
                                    'submitted' => 'chip-submitted',
                                    'expired' => 'chip-expired',
                                    'revoked' => 'chip-revoked',
                                    default => 'chip-open'
                                };
                                ?>
                                <span class="chip <?= $statusClass ?>">
                                    <?= ucfirst(str_replace('_', ' ', $link['status'])) ?>
                                </span>
                            </td>
                            <td style="color: var(--muted); font-size: 13px; white-space: nowrap;">
                                <?= date('d M Y, h:i A', strtotime($link['created_at'])) ?>
                            </td>
                            <td style="color: var(--muted); font-size: 13px; white-space: nowrap;">
                                <?= date('d M Y, h:i A', strtotime($link['expires_at'])) ?>
                            </td>
                            <td style="font-weight: 600; font-size: 13.5px;">
                                <?php if ($link['status'] === 'submitted' && $link['attempt_score'] !== null): ?>
                                    <?= (int)$link['attempt_score'] ?> / <?= (int)$link['attempt_total'] ?>
                                    <span style="font-size: 12px; color: var(--muted); font-weight: normal;">(<?= round($link['attempt_percentage']) ?>%)</span>
                                <?php else: ?>
                                    <span style="color: var(--muted);">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-icons-wrap">
                                    <!-- Copy link -->
                                    <?php if (!empty($link['url'])): ?>
                                        <button type="button" class="btn-icon-action" onclick="copyText('<?= e($link['url']) ?>')" title="Copy link" aria-label="Copy test link for <?= e($link['person_name']) ?>">
                                            <svg width="15" height="15" aria-hidden="true"><use href="#icon-copy"></use></svg>
                                        </button>
                                        <!-- Share on WhatsApp -->
                                        <?php
                                        $waText = urlencode("Hello " . $link['person_name'] . ", here is your Realty Smartz Pathshala Aptitude Assessment link:\n" . $link['url'] . "\nThis link is active for 48 hours. Best of luck!");
                                        $phoneClean = preg_replace('/[^0-9]/', '', $link['person_mobile'] ?? '');
                                        $waUrl = !empty($phoneClean) ? "https://wa.me/91{$phoneClean}?text={$waText}" : "https://wa.me/?text={$waText}";
                                        ?>
                                        <a href="<?= $waUrl ?>" target="_blank" rel="noopener" class="btn-icon-action" title="Share on WhatsApp" aria-label="Share test link on WhatsApp with <?= e($link['person_name']) ?>">
                                            <svg width="15" height="15" aria-hidden="true"><use href="#icon-whatsapp"></use></svg>
                                        </a>
                                    <?php endif; ?>

                                    <!-- Revoke (if not submitted and not already revoked) -->
                                    <?php if (!in_array($link['status'], ['submitted', 'revoked'], true)): ?>
                                        <button type="button" class="btn-icon-action btn-icon-danger" onclick="revokeGuestLink(<?= (int)$link['id'] ?>)" title="Revoke link" aria-label="Revoke test link for <?= e($link['person_name']) ?>">
                                            <svg width="15" height="15" aria-hidden="true"><use href="#icon-x"></use></svg>
                                        </button>
                                    <?php endif; ?>

                                    <!-- Allow another attempt (creates new link with different set) -->
                                    <form method="POST" action="<?= url('/admin/guest-links/' . $link['id'] . '/reattempt') ?>" style="display: inline;" onsubmit="return confirm('Generate a new test attempt link with a different question set?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-icon-action" title="Allow another attempt" aria-label="Allow another attempt for <?= e($link['person_name']) ?>">
                                            <svg width="15" height="15" aria-hidden="true"><use href="#icon-rotate-cw"></use></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Candidate Assessment Results Table -->
<div class="section-card">
    <div class="section-card-header">
        <h2 class="section-card-title">
            <svg width="18" height="18" aria-hidden="true"><use href="#icon-award"></use></svg>
            <span>Candidate assessment results</span>
        </h2>
    </div>

    <div class="table-container">
        <table class="data-table" aria-label="Assessment Results Table">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Set Assigned</th>
                    <th>Score</th>
                    <th>Percentage</th>
                    <th>Result</th>
                    <th>Date</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentAttempts)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 48px 20px; color: var(--muted);">
                            No assessment attempts recorded yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentAttempts as $att): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: var(--text);"><?= e($att['full_name']) ?></div>
                                <div style="font-size: 12.5px; color: var(--muted);"><?= e($att['email']) ?></div>
                            </td>
                            <td>
                                <span class="chip chip-open" style="font-weight: 600;">
                                    <?= e($att['set_code'] ?? 'Set A') ?>
                                </span>
                            </td>
                            <td style="font-weight: 600;">
                                <?= (int)$att['score'] ?> / <?= (int)$att['total_questions'] ?>
                            </td>
                            <td style="font-weight: 500;">
                                <?= round($att['percentage']) ?>%
                            </td>
                            <td>
                                <?php if (!empty($att['passed'])): ?>
                                    <span class="chip chip-active" style="font-weight: 600;">Passed</span>
                                <?php else: ?>
                                    <span class="chip chip-revoked" style="font-weight: 600;">Not passed</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: var(--muted); font-size: 13px; white-space: nowrap;">
                                <?= date('d M Y, h:i A', strtotime($att['created_at'])) ?>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?= url('/admin/candidates/' . $att['user_id']) ?>" class="btn-outline-action" style="height: 32px; padding: 0 12px; font-size: 12.5px;" aria-label="View candidate profile for <?= e($att['full_name']) ?>">
                                    <span>View</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Create Guest Test Link -->
<div class="modal-overlay" id="createLinkModal" role="dialog" aria-modal="true" aria-labelledby="createLinkModalTitle">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title" id="createLinkModalTitle">Create guest test link</h3>
            <button type="button" class="modal-close" onclick="closeCreateLinkModal()" aria-label="Close create guest test link modal">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-x"></use></svg>
            </button>
        </div>

        <form id="createGuestLinkForm" onsubmit="handleCreateGuestLink(event)">
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label">Link kind</label>
                <div class="type-radio-group">
                    <label class="type-radio-label">
                        <input type="radio" name="link_kind" value="open" checked onchange="toggleLinkKind(this.value)">
                        <span>Open guest link</span>
                    </label>
                    <label class="type-radio-label">
                        <input type="radio" name="link_kind" value="linked" onchange="toggleLinkKind(this.value)">
                        <span>Linked to candidate</span>
                    </label>
                </div>
            </div>

            <!-- Candidate selector (for linked kind) -->
            <div class="form-group" id="linkedCandidateGroup" style="display: none;">
                <label class="form-label" for="candidateSelect">Candidate</label>
                <select name="user_id" id="candidateSelect" class="form-select">
                    <option value="">Select Candidate</option>
                    <?php foreach ($candidates as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['full_name']) ?> (<?= e($c['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Open guest fields (for open kind) -->
            <div id="openGuestFields">
                <div class="form-group">
                    <label class="form-label" for="guestNameInput">Full name</label>
                    <input type="text" name="guest_name" id="guestNameInput" class="form-input" placeholder="Guest candidate name">
                </div>
                <div class="form-group">
                    <label class="form-label" for="guestMobileInput">Mobile number</label>
                    <input type="tel" name="guest_mobile" id="guestMobileInput" class="form-input" placeholder="e.g. 9876543210">
                </div>
                <div class="form-group">
                    <label class="form-label" for="guestEmailInput">Email address</label>
                    <input type="email" name="guest_email" id="guestEmailInput" class="form-input" placeholder="guest@example.com">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="setSelect">Question set</label>
                <select name="set_id" id="setSelect" class="form-select">
                    <option value="">Auto-assigned (Dynamic)</option>
                    <?php foreach ($sets as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= e($s['set_code']) ?> &ndash; <?= e($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="expiryHoursInput">Expiry (hours)</label>
                <input type="number" name="expiry_hours" id="expiryHoursInput" class="form-input" value="48" min="1" max="168">
            </div>

            <!-- Result Box (Hidden initially) -->
            <div id="modalResultBox" style="display: none; background: var(--surface-2); border: 1px solid var(--line); border-radius: var(--r-md); padding: 14px; margin-bottom: 16px;">
                <div style="font-size: 13px; font-weight: 600; color: var(--brand); margin-bottom: 6px;">Link generated successfully!</div>
                <input type="text" id="modalResultInput" readonly class="form-input" style="background: var(--surface); margin-bottom: 10px;">
                <div style="display: flex; gap: 8px;">
                    <button type="button" class="btn-create-link" style="height: 36px; padding: 0 14px; font-size: 13px;" onclick="copyModalLink()" aria-label="Copy generated test link">
                        <svg width="14" height="14" aria-hidden="true"><use href="#icon-copy"></use></svg>
                        <span>Copy link</span>
                    </button>
                    <a id="modalWhatsAppBtn" href="#" target="_blank" rel="noopener" class="btn-outline-action" style="height: 36px; padding: 0 14px; font-size: 13px;" aria-label="Share on WhatsApp">
                        <svg width="14" height="14" aria-hidden="true"><use href="#icon-whatsapp"></use></svg>
                        <span>Share on WhatsApp</span>
                    </a>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn-outline-action" onclick="closeCreateLinkModal()">Close</button>
                <button type="submit" id="btnSubmitCreateLink" class="btn-create-link">Generate link</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateLinkModal() {
    document.getElementById('modalResultBox').style.display = 'none';
    document.getElementById('createLinkModal').style.display = 'flex';
}

function closeCreateLinkModal() {
    document.getElementById('createLinkModal').style.display = 'none';
}

function toggleLinkKind(kind) {
    if (kind === 'linked') {
        document.getElementById('linkedCandidateGroup').style.display = 'block';
        document.getElementById('openGuestFields').style.display = 'none';
    } else {
        document.getElementById('linkedCandidateGroup').style.display = 'none';
        document.getElementById('openGuestFields').style.display = 'block';
    }
}

function handleCreateGuestLink(e) {
    e.preventDefault();
    var form = document.getElementById('createGuestLinkForm');
    var btn = document.getElementById('btnSubmitCreateLink');
    btn.disabled = true;
    btn.textContent = 'Generating...';

    var fd = new FormData(form);
    fetch('<?= url("/admin/guest-links/create") ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'Generate link';
        if (data.success && data.url) {
            document.getElementById('modalResultBox').style.display = 'block';
            document.getElementById('modalResultInput').value = data.url;
            var waMsg = encodeURIComponent("Hello, here is your Realty Smartz Pathshala Aptitude Assessment link:\n" + data.url + "\nThis link is active for 48 hours. Best of luck!");
            document.getElementById('modalWhatsAppBtn').href = 'https://wa.me/?text=' + waMsg;
        } else {
            alert(data.error || 'Failed to create guest link.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Generate link';
        alert('Server connection error.');
    });
}

function copyModalLink() {
    var input = document.getElementById('modalResultInput');
    input.select();
    navigator.clipboard.writeText(input.value);
    alert('Link copied to clipboard!');
}

function copyText(txt) {
    navigator.clipboard.writeText(txt).then(() => {
        alert('Guest link copied to clipboard!');
    });
}

function revokeGuestLink(id) {
    if (!confirm('Are you sure you want to revoke this guest test link?')) return;
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
    })
    .catch(() => {
        location.reload();
    });
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/admin.php';
?>
