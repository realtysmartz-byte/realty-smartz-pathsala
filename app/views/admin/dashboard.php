<?php
ob_start();
$viewTitle = 'Dashboard';
$pageTitle = 'Dashboard | Realty Smartz Pathshala';
?>

<style>
    /* -------------------------------------------------------------
       3. DASHBOARD STYLES
       ------------------------------------------------------------- */
    
    /* Stat Strip: One white panel, four numbers separated by thin vertical lines, label above number */
    .stat-strip {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        margin-bottom: 32px;
    }

    .stat-cell {
        padding: 24px 28px;
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--line);
    }

    .stat-cell:last-child {
        border-right: none;
    }

    .stat-label {
        font-size: 13px;
        font-weight: 500;
        color: var(--muted);
        margin-bottom: 8px;
        line-height: 1.2;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 600;
        color: var(--text);
        line-height: 1.1;
        letter-spacing: -0.02em;
    }

    @media (max-width: 900px) {
        .stat-strip {
            grid-template-columns: repeat(2, 1fr);
        }
        .stat-cell:nth-child(2) {
            border-right: none;
        }
        .stat-cell:nth-child(1),
        .stat-cell:nth-child(2) {
            border-bottom: 1px solid var(--line);
        }
    }

    @media (max-width: 480px) {
        .stat-strip {
            grid-template-columns: 1fr;
        }
        .stat-cell {
            border-right: none;
            border-bottom: 1px solid var(--line);
            padding: 18px 20px;
        }
        .stat-cell:last-child {
            border-bottom: none;
        }
    }

    /* Dashboard Layout: 2/3 Main Table + 1/3 Right Column */
    .dashboard-layout {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
        gap: 32px;
        align-items: start;
    }

    .dashboard-layout > * {
        min-width: 0;
    }

    @media (max-width: 1024px) {
        .dashboard-layout {
            grid-template-columns: 1fr;
            gap: 24px;
        }
    }

    .dashboard-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        padding: 24px;
    }

    .card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .card-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--text);
        margin: 0;
        letter-spacing: -0.01em;
    }

    .view-all-link {
        font-size: 14px;
        font-weight: 500;
        color: var(--brand);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .view-all-link:hover {
        text-decoration: underline;
    }

    /* Clean Table Styling */
    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .clean-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        text-align: left;
    }

    .clean-table th {
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 10px 14px;
        border-bottom: 1px solid var(--line);
        white-space: nowrap;
    }

    .clean-table td {
        padding: 14px;
        border-bottom: 1px solid var(--line);
        color: var(--text);
        vertical-align: middle;
        white-space: nowrap;
    }

    .clean-table tr:last-child td {
        border-bottom: none;
    }

    .clean-table tr:hover td {
        background: var(--bg);
    }

    .candidate-name {
        font-weight: 600;
        color: var(--text);
    }

    .candidate-email {
        font-size: 12px;
        color: var(--muted);
    }

    .score-badge {
        font-weight: 600;
        color: var(--text);
    }

    .score-not-taken {
        color: var(--muted);
        font-style: italic;
    }

    .action-cell {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        justify-content: flex-end;
    }

    .btn-view {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 12px;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        color: var(--brand);
        text-decoration: none;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    .btn-view:hover {
        background: var(--brand-soft);
        border-color: var(--brand);
    }

    .btn-edit {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 12px;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        color: var(--text);
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    .btn-edit:hover {
        background: var(--surface-2);
        border-color: var(--brand);
        color: var(--brand);
    }

    .btn-delete {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 12px;
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        color: var(--danger);
        cursor: pointer;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    .btn-delete:hover {
        background: var(--danger-soft);
        border-color: var(--danger);
    }

    .joining-date-val {
        font-weight: 500;
        color: var(--text);
    }

    .joining-date-empty {
        color: var(--muted);
        font-style: italic;
    }

    /* Modal Overlay & Dialog */
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

    /* Right Column: Recent Test Results List */
    .test-result-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .test-result-item {
        padding: 14px 16px;
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: var(--surface);
    }

    .test-result-left {
        min-width: 0;
    }

    .test-result-name {
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 2px;
    }

    .test-result-meta {
        font-size: 12px;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .test-result-score {
        font-size: 15px;
        font-weight: 700;
        color: var(--brand);
        text-align: right;
        flex-shrink: 0;
    }

    /* Empty States */
    .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: var(--muted);
        font-size: 14px;
    }
</style>

<!-- Stat Strip: One white panel, four numbers separated by thin vertical lines -->
<section class="stat-strip" aria-label="Key Recruitment Metrics">
    <div class="stat-cell">
        <span class="stat-label">Total Candidates</span>
        <span class="stat-value"><?= number_format($totalCandidates) ?></span>
    </div>
    <div class="stat-cell">
        <span class="stat-label">Onboarding Submitted</span>
        <span class="stat-value"><?= number_format($onboardingSubmitted) ?></span>
    </div>
    <div class="stat-cell">
        <span class="stat-label">Tests Completed</span>
        <span class="stat-value"><?= number_format($testsCompleted) ?></span>
    </div>
    <div class="stat-cell">
        <span class="stat-label">Average Score</span>
        <span class="stat-value"><?= e($averageScore) ?></span>
    </div>
</section>

<!-- Dashboard 2-Column Content -->
<div class="dashboard-layout">
    
    <!-- Main Table: Recent candidates (10 rows, "View all" link to Candidates) -->
    <div class="dashboard-card">
        <div class="card-head">
            <h2 class="card-title">Recent candidates</h2>
            <a href="<?= url('/admin/candidates') ?>" class="view-all-link" aria-label="View all candidates">
                <span>View all</span>
                <svg width="16" height="16" aria-hidden="true"><use href="#icon-chevron-right"></use></svg>
            </a>
        </div>

        <?php if (empty($recentCandidates)): ?>
            <div class="empty-state">No candidates yet</div>
        <?php else: ?>
            <div class="table-container">
                <table class="clean-table" aria-label="Recent candidates table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Joining date</th>
                            <th>Documents</th>
                            <th>Test score</th>
                            <th>Registered</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentCandidates as $c): ?>
                            <tr id="candidateRow_<?= $c['id'] ?>">
                                <td>
                                    <div class="candidate-name" id="cName_<?= $c['id'] ?>"><?= e($c['full_name']) ?></div>
                                    <div class="candidate-email" id="cEmail_<?= $c['id'] ?>"><?= e($c['email']) ?></div>
                                </td>
                                <td id="cMobile_<?= $c['id'] ?>"><?= e($c['mobile'] ?? '-') ?></td>
                                <td id="cJoining_<?= $c['id'] ?>">
                                    <?php if (!empty($c['joining_date'])): ?>
                                        <span class="joining-date-val"><?= date('d M Y', strtotime($c['joining_date'])) ?></span>
                                    <?php else: ?>
                                        <span class="joining-date-empty">Not set</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= (int)$c['doc_count'] ?> uploaded</td>
                                <td>
                                    <?php if ($c['test_score'] !== null): ?>
                                        <span class="score-badge"><?= (int)$c['test_score'] ?>/<?= (int)($c['test_total'] ?? 15) ?></span>
                                    <?php else: ?>
                                        <span class="score-not-taken">Not taken</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                                <td style="text-align: right;">
                                    <div class="action-cell">
                                        <a href="<?= url('/admin/candidates/' . $c['id']) ?>" class="btn-view" aria-label="View details for <?= e($c['full_name']) ?>">
                                            View
                                        </a>
                                        <button type="button" class="btn-edit" onclick='openEditCandidateModal(<?= json_encode([
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
                                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' aria-label="Edit details for <?= e($c['full_name']) ?>">
                                            <svg width="14" height="14" aria-hidden="true"><use href="#icon-edit"></use></svg>
                                            <span>Edit</span>
                                        </button>
                                        <button type="button" class="btn-delete" onclick="confirmDeleteCandidate(<?= (int)$c['id'] ?>, '<?= e(addslashes($c['full_name'])) ?>')" aria-label="Delete candidate <?= e($c['full_name']) ?>">
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
        <?php endif; ?>
    </div>

    <!-- Right Column (1/3 width): Recent test results list -->
    <div class="dashboard-card">
        <div class="card-head">
            <h2 class="card-title">Recent test results</h2>
        </div>

        <?php if (empty($recentTests)): ?>
            <div class="empty-state">No test results yet</div>
        <?php else: ?>
            <div class="test-result-list" role="list">
                <?php foreach ($recentTests as $test): ?>
                    <div class="test-result-item" role="listitem">
                        <div class="test-result-left">
                            <div class="test-result-name" title="<?= e($test['candidate_name']) ?>"><?= e($test['candidate_name']) ?></div>
                            <div class="test-result-meta">
                                <span><?= e($test['set_name']) ?></span>
                                <span>&bull;</span>
                                <span><?= date('d M Y', strtotime($test['created_at'])) ?></span>
                            </div>
                        </div>
                        <div class="test-result-score">
                            <?= (int)$test['score'] ?>/<?= (int)($test['total_questions'] ?? 15) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

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

<script>
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
    if (event && event.preventDefault) {
        event.preventDefault();
    }
    var form = document.getElementById('editCandidateForm');
    var cid = document.getElementById('editCandidateId').value;
    var btn = document.getElementById('btnSaveCandidate');
    var errEl = document.getElementById('editFormError');

    console.log('[Dashboard] Saving candidate ID:', cid);
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

    var csrfToken = document.querySelector('input[name="_csrf_token"]') ? document.querySelector('input[name="_csrf_token"]').value : '';
    var fd = new FormData();
    fd.append('_csrf_token', csrfToken);

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
                row.remove();
            }
        } else {
            errEl.textContent = res.error || 'Failed to delete candidate.';
            errEl.style.display = 'block';
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.textContent = 'Delete candidate';
        errEl.textContent = 'Network or server error.';
        errEl.style.display = 'block';
    });
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/admin.php';
?>
