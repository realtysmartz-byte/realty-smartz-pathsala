<?php
ob_start();

$profile = $candidate['profile'] ?? [];
$docs = $candidate['docs_by_type'] ?? [];
$social = $profile['social'] ?? [];
$emergency = $candidate['emergency'] ?? [];
$isLocked = !empty($profile['is_locked']) && !in_array($candidate['status'], ['changes_requested'], true);
$completion = (int)($profile['completion_percentage'] ?? 0);
$status = $candidate['status'] ?? 'registered';
$attempt = $candidate['test_attempt'] ?? null;

// Determine step statuses (using model method if not already passed)
if (!isset($stepStatus)) {
    $stepStatus = \App\Models\Profile::getOnboardingStepStatus((int)($candidate['id'] ?? $candidate['user_id'] ?? 0));
}
$stepACompleted = !empty($stepStatus['step_a_completed']);
$stepBCompleted = !empty($stepStatus['step_b_completed']);
$stepCCompleted = !empty($stepStatus['step_c_completed']);
$stepDCompleted = !empty($stepStatus['step_d_completed']);
$maxAllowedStep = $stepStatus['max_allowed_step'] ?? 'step_a';

// Enforce active step cannot exceed max allowed step unless locked
$activeStep = $activeStep ?? ($_GET['step'] ?? 'step_a');
if (!$isLocked) {
    $stepWeights = ['step_a' => 1, 'step_b' => 2, 'step_c' => 3, 'step_d' => 4];
    $maxWeight = $stepWeights[$maxAllowedStep] ?? 1;
    $currWeight = $stepWeights[$activeStep] ?? 1;
    if ($currWeight > $maxWeight) {
        $activeStep = $maxAllowedStep;
    }
}

// 4-Stage Pipeline Statuses
$isEmailVerified = !empty($candidate['email_verified_at']);
$isOnboardingSubmitted = !empty($profile['declaration_accepted']) || $isLocked || (int)($profile['completion_percentage'] ?? 0) >= 100 || in_array($status, ['documents_submitted', 'profile_completed', 'test_pending', 'test_completed'], true);
$isTestCompleted = !empty($attempt) && in_array($attempt['status'] ?? '', ['completed', 'passed', 'failed', 'submitted'], true);
$isDecisionDone = !empty($candidate['status']) && in_array($candidate['status'], ['accepted', 'rejected', 'shortlisted', 'joined'], true);

$step1State = 'completed';
$step2State = $isEmailVerified ? 'completed' : 'current';
if ($isOnboardingSubmitted) {
    $step3State = 'completed';
} elseif ($isEmailVerified) {
    $step3State = 'current';
} else {
    $step3State = 'pending';
}
if ($isTestCompleted) {
    $step4State = 'completed';
} elseif ($isOnboardingSubmitted) {
    $step4State = 'current';
} else {
    $step4State = 'pending';
}
if ($isDecisionDone) {
    $step5State = 'completed';
} elseif ($isTestCompleted) {
    $step5State = 'current';
} else {
    $step5State = 'pending';
}

$stepBAllowed = $stepACompleted || $isLocked;
$stepCAllowed = ($stepACompleted && $stepBCompleted) || $isLocked;
$stepDAllowed = ($stepACompleted && $stepBCompleted && $stepCCompleted) || $isLocked;

function renderCandidateDocSlot(string $type, string $label, string $description, bool $required, array $docs, bool $isLocked): void {
    $doc = $docs[$type] ?? null;
    if (!$doc && $type === 'aadhaar_front') {
        $doc = $docs['aadhaar'] ?? ($docs['aadhaar_card'] ?? null);
    }
    if (!$doc && $type === 'pan_card') {
        $doc = $docs['pan'] ?? null;
    }
    $isUploaded = !empty($doc);
    $isPdf = $isUploaded && ($doc['mime_type'] === 'application/pdf');
    $isImg = $isUploaded && str_starts_with($doc['mime_type'], 'image/');
    $sizeAfter = (int)($doc['file_size_after'] ?? ($doc['file_size_before'] ?? 0));
    $sizeBefore = (int)($doc['file_size_before'] ?? $sizeAfter);
    $savings = ($sizeBefore > $sizeAfter && $sizeBefore > 0) ? round((($sizeBefore - $sizeAfter) / $sizeBefore) * 100) : 0;
?>
    <div id="slot_container_<?= $type ?>" style="background: var(--surface); border: 1px solid var(--line); border-radius: var(--r-md); padding: 18px; margin-bottom: 14px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 10px;">
            <div>
                <strong style="font-size: 13.5px; color: var(--text);">
                    <?= e($label) ?> <?= $required ? '<span style="color:var(--danger)">*</span>' : '<span style="color:var(--muted); font-size:11px;">(Optional)</span>' ?>
                </strong>
                <div style="font-size: 11.5px; color: var(--muted); margin-top: 2px;"><?= e($description) ?></div>
            </div>

            <div id="slot_badge_<?= $type ?>">
                <?php if ($isUploaded): ?>
                    <span class="doc-uploaded-chip">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>Uploaded</span>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div id="slot_preview_<?= $type ?>" style="<?= $isUploaded ? 'display:flex;' : 'display:none;' ?> align-items:center; justify-content:space-between; padding:10px 14px; background:var(--bg); border:1px solid var(--line); border-radius:var(--r-sm); font-size:13px; margin-bottom:10px;">
            <div style="display:flex; align-items:center; gap:10px; overflow:hidden;">
                <div id="slot_icon_<?= $type ?>" style="width:36px; height:36px; border-radius:6px; background:#fff; border:1px solid var(--line); display:flex; align-items:center; justify-content:center; color:var(--brand); font-size:16px; flex-shrink:0; overflow:hidden;">
                    <?php if ($isImg): ?>
                        <img src="<?= url('/document/download/' . $doc['id']) ?>" alt="Thumbnail" style="width:100%; height:100%; object-fit:cover;">
                    <?php elseif ($isPdf): ?>
                        <i class="fas fa-file-pdf" style="color:var(--danger);"></i>
                    <?php else: ?>
                        <i class="fas fa-file-alt" style="color:var(--brand);"></i>
                    <?php endif; ?>
                </div>

                <div style="overflow:hidden;">
                    <div id="slot_name_<?= $type ?>" style="font-weight:600; color:var(--text); text-overflow:ellipsis; overflow:hidden; white-space:nowrap; max-width:220px;" title="<?= e($doc['original_name'] ?? '') ?>">
                        <?= e($doc['original_name'] ?? '') ?>
                    </div>
                    <div id="slot_meta_<?= $type ?>" style="font-size:11px; color:var(--muted); display:flex; align-items:center; gap:6px; margin-top:2px;">
                        <span><?= round($sizeAfter / 1024) ?> KB</span>
                        <?php if ($savings > 0): ?>
                            <span style="color:var(--brand); font-weight:600;">(-<?= $savings ?>% compressed)</span>
                        <?php endif; ?>
                        <span>&bull; v<?= (int)($doc['version'] ?? 1) ?></span>
                    </div>
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:6px;">
                <a id="slot_view_<?= $type ?>" href="<?= $isUploaded ? url('/document/download/' . $doc['id']) : '#' ?>" target="_blank" class="btn btn-outline btn-sm" style="padding:4px 10px; font-size:12px; border-radius:var(--r-sm);">
                    View
                </a>
                <?php if (!$isLocked && !empty($isReupload)): ?>
                    <button type="button" class="btn btn-outline btn-sm" style="padding:4px 10px; font-size:12px; border-radius:var(--r-sm);" onclick="triggerUpload('<?= $type ?>')">
                        Replace
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <input type="file" id="file_<?= $type ?>" accept="image/jpeg,image/png,image/webp,application/pdf" style="display:none;" onchange="handleFileUpload(this, '<?= $type ?>')">

        <?php if (!$isLocked): ?>
            <div class="dropzone-box" id="dropzone_<?= $type ?>" data-doctype="<?= $type ?>" onclick="triggerUpload('<?= $type ?>')" style="<?= ($isUploaded && empty($isReupload)) ? 'display:none;' : '' ?>">
                <div style="margin-bottom:6px; color:var(--brand);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                </div>
                <div style="font-size:13px; font-weight:600; color:var(--text);">Drag &amp; Drop or Browse File</div>
                <div style="font-size:11px; color:var(--muted); margin-top:2px;">JPG, PNG, PDF &bull; Max 2 MB (Pre-compressed)</div>
                <div class="dropzone-progress" id="progress_<?= $type ?>">
                    <div class="dropzone-progress-bar" id="bar_<?= $type ?>"></div>
                </div>
            </div>
        <?php elseif (!$isUploaded): ?>
            <div class="dropzone-box" id="dropzone_<?= $type ?>" style="opacity: 0.6; cursor: not-allowed; pointer-events: none; border-style: dashed; padding: 12px; text-align: center;">
                <div style="font-size:12.5px; font-weight:500; color:var(--muted);">Document not submitted (Profile Locked)</div>
            </div>
        <?php endif; ?>
    </div>
<?php
}
?>

<!-- 1. STATUS TIMELINE COMPONENT (5 Connected Pipeline Stages) -->
<div class="p-4 bg-white border mb-4" style="border-radius: var(--r-lg); border-color: var(--line) !important;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <span class="badge" style="background: var(--brand-soft); color: var(--brand); font-weight: 600; border-radius: var(--r-full); padding: 4px 10px; font-size: 11px; letter-spacing: 0.04em; text-transform: uppercase;">
                Onboarding Pipeline
            </span>
            <h2 class="h5 mb-0 mt-1" style="font-weight: 600; color: var(--text);">Application Progress Timeline</h2>
        </div>
            <?php if ($isOnboardingSubmitted): ?>
                <span class="badge" style="background: var(--brand-soft); color: var(--brand); border: 1px solid var(--line); padding: 6px 14px; border-radius: var(--r-full); font-weight: 600; font-size: 12.5px;">
                    Onboarding Submitted
                </span>
            <?php else: ?>
                <span class="badge" style="background: var(--bg); color: var(--muted); border: 1px solid var(--line); padding: 6px 14px; border-radius: var(--r-full); font-weight: 600; font-size: 12.5px;">
                    In Progress (<?= $completion ?>%)
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Desktop Timeline (5 columns connected on 1 horizontal row) -->
    <div class="candidate-timeline-desktop mb-0">
        <!-- Stage 1: Registered -->
        <div class="candidate-step-col <?= $step1State ?>">
            <div class="candidate-step-circle">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="candidate-step-label">Registered</div>
            <div class="small text-muted" style="font-size: 11px; margin-top: 3px;"><?= date('d M Y', strtotime($candidate['created_at'])) ?></div>
        </div>

        <!-- Stage 2: Email Verified -->
        <div class="candidate-step-col <?= $step2State ?>">
            <div class="candidate-step-circle">
                <?php if ($step2State === 'completed'): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <?php else: ?>
                    2
                <?php endif; ?>
            </div>
            <div class="candidate-step-label">Email Verified</div>
            <div class="small text-muted" style="font-size: 11px; margin-top: 3px;"><?= $isEmailVerified ? 'Verified' : 'Pending OTP' ?></div>
        </div>

        <!-- Stage 3: Onboarding Submitted -->
        <div class="candidate-step-col <?= $step3State ?>">
            <div class="candidate-step-circle">
                <?php if ($step3State === 'completed'): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <?php else: ?>
                    3
                <?php endif; ?>
            </div>
            <div class="candidate-step-label">Onboarding Submitted</div>
            <div class="small text-muted" style="font-size: 11px; margin-top: 3px;"><?= $isOnboardingSubmitted ? 'Submitted' : 'Action Required' ?></div>
        </div>

        <!-- Stage 4: Aptitude Assessment -->
        <div class="candidate-step-col <?= $step4State ?>">
            <div class="candidate-step-circle">
                <?php if ($step4State === 'completed'): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <?php else: ?>
                    4
                <?php endif; ?>
            </div>
            <div class="candidate-step-label">Aptitude Assessment</div>
            <div class="small text-muted" style="font-size: 11px; margin-top: 3px;"><?= $isTestCompleted ? 'Completed' : ($isOnboardingSubmitted ? 'Take Test' : 'Locked') ?></div>
        </div>
    </div>

    <!-- Mobile Vertical Timeline -->
    <div class="candidate-timeline-mobile">
        <div class="mobile-step-row <?= $step1State ?>">
            <div class="mobile-step-circle">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="mobile-step-content">
                <div class="mobile-step-title">1. Registered</div>
                <div class="mobile-step-date"><?= date('d M Y', strtotime($candidate['created_at'])) ?></div>
            </div>
        </div>
        <div class="mobile-step-row <?= $step2State ?>">
            <div class="mobile-step-circle">
                <?= $step2State === 'completed' ? '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' : '2' ?>
            </div>
            <div class="mobile-step-content">
                <div class="mobile-step-title">2. Email Verified</div>
                <div class="mobile-step-date"><?= $isEmailVerified ? 'Verified' : 'Pending OTP' ?></div>
            </div>
        </div>
        <div class="mobile-step-row <?= $step3State ?>">
            <div class="mobile-step-circle">
                <?= $step3State === 'completed' ? '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' : '3' ?>
            </div>
            <div class="mobile-step-content">
                <div class="mobile-step-title">3. Onboarding Submitted</div>
                <div class="mobile-step-date"><?= $isOnboardingSubmitted ? 'Submitted' : 'Action Required' ?></div>
            </div>
        </div>
        <div class="mobile-step-row <?= $step4State ?>">
            <div class="mobile-step-circle">
                <?= $step4State === 'completed' ? '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' : '4' ?>
            </div>
            <div class="mobile-step-content">
                <div class="mobile-step-title">4. Aptitude Assessment</div>
                <div class="mobile-step-date"><?= $isTestCompleted ? 'Completed' : ($isOnboardingSubmitted ? 'Take Test' : 'Locked') ?></div>
            </div>
        </div>
    </div>
</div>

<!-- 2. WIZARD SHELL: 2-Column Desktop Layout (Left Step List + Right Form Panel) & Compact Mobile Progress -->
<!-- Mobile Compact Step Header -->
<div class="d-lg-none bg-white p-3 border mb-3" style="border-radius: var(--r-md); border-color: var(--line) !important;">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="small fw-bold text-dark" id="mobileStepTitle">
            <?= $activeStep === 'step_b' ? 'Step 2 of 4 &bull; Social Links' : ($activeStep === 'step_c' ? 'Step 3 of 4 &bull; Documents & KYC' : ($activeStep === 'step_d' ? 'Step 4 of 4 &bull; Review &amp; Sign' : 'Step 1 of 4 &bull; Basic Details')) ?>
        </span>
        <span class="small text-muted" id="mobileStepPct"><?= $completion ?>%</span>
    </div>
    <div class="progress" style="height: 4px; background: var(--line);">
        <div id="mobileProgressBar" class="progress-bar" style="background-color: var(--brand) !important; width: <?= max(10, $completion) ?>%;"></div>
    </div>
</div>

<div class="row g-4 align-items-start">
    <!-- Desktop Left Vertical Step List -->
    <div class="col-lg-4 d-none d-lg-block position-sticky" style="top: 80px;">
        <div class="bg-white border p-3 mb-3" style="border-radius: var(--r-lg); border-color: var(--line) !important;">
            <div class="d-flex justify-content-between align-items-center px-2 mb-3">
                <span class="small text-muted text-uppercase fw-bold" style="font-size: 11.5px; letter-spacing: 0.04em;">Onboarding Steps</span>
                <?php if ($isLocked): ?>
                    <span class="badge" style="background: var(--brand-soft); color: var(--brand); border-radius: var(--r-full); font-size: 11px;">Submitted</span>
                <?php endif; ?>
            </div>

            <div class="d-flex flex-column gap-2" id="wizardTabList">
                <!-- Step 1: Basic Details -->
                <button type="button" 
                        class="wizard-tab-btn <?= $activeStep === 'step_a' ? 'active' : '' ?>" 
                        id="tab_step_a"
                        onclick="switchStep('step_a')">
                    <span class="step-num" id="badge_step_a"><?= $stepACompleted ? '✓' : '1' ?></span>
                    <div class="text-start flex-grow-1">
                        <div class="step-title">Basic Details</div>
                        <div class="step-sub text-muted">Identity &amp; Address</div>
                    </div>
                </button>

                <!-- Step 2: Social Links -->
                <button type="button" 
                        class="wizard-tab-btn <?= $activeStep === 'step_b' ? 'active' : '' ?> <?= !$stepBAllowed ? 'is-locked' : '' ?>" 
                        id="tab_step_b"
                        onclick="switchStep('step_b')">
                    <span class="step-num" id="badge_step_b"><?= $stepBCompleted ? '✓' : '2' ?></span>
                    <div class="text-start flex-grow-1">
                        <div class="step-title d-flex justify-content-between align-items-center">
                            <span>Social Links</span>
                            <span class="lock-indicator" id="lock_step_b" style="<?= $stepBAllowed ? 'display:none;' : 'display:inline-flex;' ?>" title="Complete Step 1 to unlock">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                        </div>
                        <div class="step-sub text-muted">Professional Profiles</div>
                    </div>
                </button>

                <!-- Step 3: Documents & KYC -->
                <button type="button" 
                        class="wizard-tab-btn <?= $activeStep === 'step_c' ? 'active' : '' ?> <?= !$stepCAllowed ? 'is-locked' : '' ?>" 
                        id="tab_step_c"
                        onclick="switchStep('step_c')">
                    <span class="step-num" id="badge_step_c"><?= $stepCCompleted ? '✓' : '3' ?></span>
                    <div class="text-start flex-grow-1">
                        <div class="step-title d-flex justify-content-between align-items-center">
                            <span>Documents &amp; KYC</span>
                            <span class="lock-indicator" id="lock_step_c" style="<?= $stepCAllowed ? 'display:none;' : 'display:inline-flex;' ?>" title="Complete Step 1 & 2 to unlock">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                        </div>
                        <div class="step-sub text-muted">Aadhaar, PAN &amp; Marksheets</div>
                    </div>
                </button>

                <!-- Step 4: Review & Sign -->
                <button type="button" 
                        class="wizard-tab-btn <?= $activeStep === 'step_d' ? 'active' : '' ?> <?= !$stepDAllowed ? 'is-locked' : '' ?>" 
                        id="tab_step_d"
                        onclick="switchStep('step_d')">
                    <span class="step-num" id="badge_step_d"><?= $isOnboardingSubmitted ? '✓' : '4' ?></span>
                    <div class="text-start flex-grow-1">
                        <div class="step-title d-flex justify-content-between align-items-center">
                            <span>Review &amp; Sign</span>
                            <span class="lock-indicator" id="lock_step_d" style="<?= $stepDAllowed ? 'display:none;' : 'display:inline-flex;' ?>" title="Complete Step 1, 2 & 3 to unlock">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                        </div>
                        <div class="step-sub text-muted">DPDP Declaration</div>
                    </div>
                </button>
            </div>
        </div>

        <!-- Auto-save & Completion Card -->
        <div class="p-3 bg-white border small text-muted d-flex justify-content-between align-items-center" style="border-radius: var(--r-lg); border-color: var(--line) !important;">
            <div id="autoSaveIndicator" class="d-flex align-items-center gap-2">
                <span id="autoSaveText">All changes saved</span>
            </div>
            <div class="fw-bold text-dark">
                <span id="wizardProgressPct"><?= $completion ?>%</span>
            </div>
        </div>
    </div>

    <!-- Right White Form Panel -->
    <div class="col-lg-8">
        <div class="bg-white border p-3 p-sm-4 p-md-5 wizard-card-panel" style="border-radius: var(--r-lg); border-color: var(--line) !important;">

    <style>
        .wizard-tab-btn {
            width: 100%;
            padding: 12px 14px;
            background: none;
            border: 1px solid transparent;
            border-radius: var(--r-md);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--muted);
            transition: all 0.15s ease;
            text-align: left;
        }
        .wizard-tab-btn:hover:not(.is-locked) {
            background: var(--surface-2);
            color: var(--text);
        }
        .wizard-tab-btn.active {
            background: var(--brand-soft);
            border-color: var(--line);
            color: var(--brand);
        }
        .wizard-tab-btn.is-locked {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .wizard-tab-btn .step-num {
            width: 28px;
            height: 28px;
            border-radius: var(--r-full);
            background: var(--surface);
            border: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
            flex-shrink: 0;
            color: var(--muted);
        }
        .wizard-tab-btn.active .step-num {
            background: var(--brand);
            color: #ffffff;
            border-color: var(--brand);
        }
        .wizard-tab-btn .step-title {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text);
        }
        .wizard-tab-btn.active .step-title {
            color: var(--brand);
        }
        .wizard-tab-btn .step-sub {
            font-size: 11.5px;
            color: var(--muted);
        }
        .lock-indicator {
            color: var(--muted);
            display: inline-flex;
            align-items: center;
        }
        .dropzone-box {
            border: 1px dashed var(--line);
            border-radius: var(--r-md);
            padding: 20px;
            text-align: center;
            background: var(--surface);
            cursor: pointer;
            position: relative;
            transition: all 0.15s ease;
        }
        .dropzone-box:hover, .dropzone-box.dragover {
            border-color: var(--brand);
            background: var(--brand-soft);
        }
        .dropzone-progress {
            margin-top: 10px;
            height: 4px;
            background: var(--line);
            border-radius: var(--r-full);
            overflow: hidden;
            display: none;
        }
        .dropzone-progress-bar {
            height: 100%;
            width: 0%;
            background: var(--brand);
            transition: width 0.2s ease;
        }
        .doc-uploaded-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--brand-soft);
            color: var(--brand);
            padding: 2px 10px;
            border-radius: var(--r-full);
            font-size: 11.5px;
            font-weight: 600;
            border: 1px solid var(--line);
        }
        .form-control, .form-select {
            border: 1px solid var(--line) !important;
            border-radius: var(--r-md) !important;
            font-size: 14px;
            color: var(--text);
            background-color: var(--surface);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand) !important;
            box-shadow: 0 0 0 3px var(--brand-soft) !important;
        }
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }
        @keyframes slideInToast {
            from {
                opacity: 0;
                transform: translateY(-16px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .pulse-highlight {
            animation: pulseBorder 1.5s infinite;
        }
        @keyframes pulseBorder {
            0%, 100% { border-color: var(--line); }
            50% { border-color: var(--brand); box-shadow: 0 0 0 4px var(--brand-soft); }
        }

        /* Mobile Responsive Enhancements */
        @media (max-width: 768px) {
            .wizard-card-panel {
                padding: 16px !important;
                border-radius: var(--r-md) !important;
            }
            .dropzone-box {
                padding: 14px 10px !important;
            }
            .form-control, .form-select {
                font-size: 14px !important;
            }
            #step_c_section .p-3, #step_c_section .p-sm-4 {
                padding: 12px !important;
            }
            .wizard-step-content h3 {
                font-size: 1.1rem !important;
            }
        }
    </style>

        <?php if ($isLocked): ?>
            <div class="alert d-flex align-items-center gap-3 p-3 mb-4" style="background: rgba(31,92,74,0.08); border: 1px solid var(--line); border-radius: var(--r-md); color: var(--brand);">
                <div style="font-size: 22px; flex-shrink: 0;"><i class="fas fa-lock"></i></div>
                <div>
                    <strong style="display:block; font-size: 14.5px; margin-bottom: 2px;">Application Submitted &bull; Profile Locked</strong>
                    <span style="font-size: 13px; color: var(--text);">Your onboarding information, identity proofs, and documents have been submitted for verification. Modifications are permanently disabled.</span>
                </div>
            </div>
        <?php endif; ?>

        <!-- ================================================================
             STEP A: BASIC DETAILS
             ================================================================ -->
        <div id="step_a_section" class="wizard-step-content" style="<?= $activeStep === 'step_a' ? 'display:block;' : 'display:none;' ?>">
            <form id="form_step_a" onsubmit="event.preventDefault(); saveStepData('step_a', true);">
                <?= csrf_field() ?>
                <input type="hidden" name="step" value="step_a">

                <div style="margin-bottom:24px;">
                    <h3 style="font-size:1.25rem; font-weight:800; color:var(--text); margin:0 0 4px;">Step A &bull; Personal &amp; Contact Details</h3>
                    <p style="font-size:13.5px; color:var(--muted); margin:0;">Provide your legal identification, communication coordinates, and address.</p>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:20px; margin-bottom:20px;">
                    <!-- Full Legal Name -->
                    <div>
                        <label class="form-label" for="full_name">Full Legal Name *</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" value="<?= e($candidate['full_name']) ?>" required <?= $isLocked ? 'readonly' : '' ?>>
                    </div>

                    <!-- Father's Name -->
                    <div>
                        <label class="form-label" for="father_name">Father's Full Name *</label>
                        <input type="text" id="father_name" name="father_name" class="form-control" value="<?= e($profile['father_name'] ?? ($candidate['father_name'] ?? '')) ?>" placeholder="e.g. Ramesh Malhotra" required <?= $isLocked ? 'readonly' : '' ?>>
                    </div>

                    <!-- Email Address (Readonly) -->
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                            <label class="form-label" for="email" style="margin-bottom:0;">Registered Email Address</label>
                            <span class="badge-pill badge-success" style="font-size:11px; padding:2px 8px; font-weight:700; display:inline-flex; align-items:center; gap:4px;">
                                <?= icon('check', '', 11) ?> Verified
                            </span>
                        </div>
                        <input type="email" id="email" class="form-control" style="background:var(--surface-2); color:var(--text); font-weight:600;" value="<?= e($candidate['email']) ?>" readonly>
                    </div>

                    <!-- Mobile Number (Readonly) -->
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                            <label class="form-label" for="mobile" style="margin-bottom:0;">Mobile Number</label>
                            <span style="font-size:11px; color:var(--muted); display:inline-flex; align-items:center; gap:4px;">
                                <?= icon('shield', '', 11) ?> Primary Contact
                            </span>
                        </div>
                        <div style="position:relative;">
                            <span style="position:absolute; left:12px; top:11px; font-size:13px; font-weight:700; color:var(--primary);">+91</span>
                            <input type="tel" id="mobile" class="form-control" style="padding-left:46px; background:var(--surface-2); color:var(--text); font-weight:600;" value="<?= e($candidate['mobile']) ?>" readonly>
                        </div>
                    </div>

                    <!-- WhatsApp Number -->
                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                            <label class="form-label" for="whatsapp" style="margin-bottom:0;">WhatsApp Number *</label>
                            <label style="font-size:12px; color:var(--primary); font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:4px;">
                                <input type="checkbox" id="chkSameAsMobile" onchange="toggleSameAsMobile(this)" <?= ($candidate['mobile'] === ($profile['whatsapp'] ?? '')) ? 'checked' : '' ?> <?= $isLocked ? 'disabled' : '' ?>>
                                Same as mobile
                            </label>
                        </div>
                        <div style="position:relative;">
                            <span style="position:absolute; left:12px; top:11px; font-size:13px; font-weight:700; color:var(--primary);">+91</span>
                            <input type="tel" id="whatsapp" name="whatsapp" maxlength="10" pattern="[6-9][0-9]{9}" class="form-control" style="padding-left:46px;" value="<?= e($profile['whatsapp'] ?? ($candidate['whatsapp'] ?? $candidate['mobile'])) ?>" placeholder="9876543210" required <?= $isLocked ? 'readonly' : '' ?>>
                        </div>
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="form-label" for="dob">Date of Birth *</label>
                        <input type="date" id="dob" name="dob" class="form-control" value="<?= e($profile['dob'] ?? '') ?>" required <?= $isLocked ? 'readonly' : '' ?>>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="form-label">Gender *</label>
                        <select name="gender" id="gender" class="form-control" required <?= $isLocked ? 'disabled' : '' ?>>
                            <option value="">-- Select Gender --</option>
                            <option value="male" <?= ($profile['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= ($profile['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= ($profile['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </div>
                </div>

                <!-- Current Residential Address -->
                <div style="margin-bottom:20px;">
                    <label class="form-label" for="address">Current Residential Address *</label>
                    <textarea id="address" name="address" rows="3" class="form-control" placeholder="House/Flat No., Building Name, Street/Sector, Landmark" required <?= $isLocked ? 'readonly' : '' ?>><?= e($profile['address'] ?? ($profile['current_address'] ?? '')) ?></textarea>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-bottom:30px;">
                    <!-- City -->
                    <div>
                        <label class="form-label" for="city">City *</label>
                        <input type="text" id="city" name="city" class="form-control" value="<?= e($profile['city'] ?? '') ?>" placeholder="e.g. Gurugram" required <?= $isLocked ? 'readonly' : '' ?>>
                    </div>

                    <!-- State -->
                    <div>
                        <label class="form-label" for="state">State *</label>
                        <input type="text" id="state" name="state" class="form-control" value="<?= e($profile['state'] ?? 'Haryana') ?>" placeholder="e.g. Haryana" required <?= $isLocked ? 'readonly' : '' ?>>
                    </div>

                    <!-- Pincode -->
                    <div>
                        <label class="form-label" for="pincode">Pincode (6-digit) *</label>
                        <input type="text" id="pincode" name="pincode" maxlength="6" pattern="[1-9][0-9]{5}" class="form-control" value="<?= e($profile['pincode'] ?? '') ?>" placeholder="e.g. 122002" required <?= $isLocked ? 'readonly' : '' ?>>
                    </div>
                </div>

                <!-- Actions -->
                <div style="display:flex; justify-content:flex-end; gap:12px; border-top:1px solid var(--line); padding-top:20px;">
                    <?php if ($isLocked): ?>
                        <button type="button" class="btn btn-outline" onclick="switchStep('step_b')" style="padding:12px 28px; font-weight:600; border-radius:var(--r-full);">
                            <span>Next: View Social Links &rarr;</span>
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary" style="padding:12px 28px; font-weight:600; border-radius:var(--r-full);">
                            <span>Save &amp; Continue to Social Links</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:6px;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- ================================================================
             STEP B: SOCIAL LINKS
             ================================================================ -->
        <div id="step_b_section" class="wizard-step-content" style="<?= $activeStep === 'step_b' ? 'display:block;' : 'display:none;' ?>">
            <form id="form_step_b" onsubmit="event.preventDefault(); saveStepData('step_b', true);">
                <?= csrf_field() ?>
                <input type="hidden" name="step" value="step_b">

                <div style="margin-bottom:24px;">
                    <h3 style="font-size:1.25rem; font-weight:600; color:var(--text); margin:0 0 4px;">Step B &bull; Professional &amp; Social Links</h3>
                    <p style="font-size:13.5px; color:var(--muted); margin:0;">Share your online profiles to help our hiring partners know you better. <strong style="color:var(--text);">LinkedIn profile is required.</strong></p>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px; margin-bottom:30px;">
                    <!-- LinkedIn -->
                    <div>
                        <label class="form-label" for="soc_linkedin">
                            <i class="fab fa-linkedin" style="color:#0A66C2; margin-right:4px;"></i> LinkedIn Profile URL <span style="color:var(--danger,#E05252);">*</span>
                        </label>
                        <input type="url" id="soc_linkedin" name="linkedin" class="form-control" value="<?= e($social['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/yourname" required <?= $isLocked ? 'readonly' : '' ?>>
                    </div>

                    <!-- Instagram -->
                    <div>
                        <label class="form-label" for="soc_instagram">
                            <i class="fab fa-instagram" style="color:var(--muted); margin-right:4px;"></i> Instagram Handle / URL
                        </label>
                        <input type="text" id="soc_instagram" name="instagram" class="form-control" value="<?= e($social['instagram'] ?? '') ?>" placeholder="@yourhandle or URL" <?= $isLocked ? 'readonly' : '' ?>>
                    </div>

                    <!-- Facebook -->
                    <div>
                        <label class="form-label" for="soc_facebook">
                            <i class="fab fa-facebook" style="color:var(--muted); margin-right:4px;"></i> Facebook Profile URL
                        </label>
                        <input type="url" id="soc_facebook" name="facebook" class="form-control" value="<?= e($social['facebook'] ?? '') ?>" placeholder="https://facebook.com/yourprofile" <?= $isLocked ? 'readonly' : '' ?>>
                    </div>

                    <!-- X / Twitter -->
                    <div>
                        <label class="form-label" for="soc_twitter">
                            <i class="fab fa-x-twitter" style="color:var(--muted); margin-right:4px;"></i> X (Twitter) Profile URL
                        </label>
                        <input type="url" id="soc_twitter" name="twitter" class="form-control" value="<?= e($social['twitter'] ?? '') ?>" placeholder="https://x.com/yourhandle" <?= $isLocked ? 'readonly' : '' ?>>
                    </div>

                    <!-- YouTube -->
                    <div>
                        <label class="form-label" for="soc_youtube">
                            <i class="fab fa-youtube" style="color:var(--muted); margin-right:4px;"></i> YouTube Channel / Link
                        </label>
                        <input type="url" id="soc_youtube" name="youtube" class="form-control" value="<?= e($social['youtube'] ?? '') ?>" placeholder="https://youtube.com/@yourchannel" <?= $isLocked ? 'readonly' : '' ?>>
                    </div>

                    <!-- Portfolio / Personal Website -->
                    <div>
                        <label class="form-label" for="soc_portfolio">
                            <i class="fas fa-globe" style="color:var(--muted); margin-right:4px;"></i> Portfolio / Personal Website
                        </label>
                        <input type="url" id="soc_portfolio" name="portfolio" class="form-control" value="<?= e($social['portfolio'] ?? '') ?>" placeholder="https://yourportfolio.com" <?= $isLocked ? 'readonly' : '' ?>>
                    </div>
                </div>

                <!-- Actions -->
                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--line); padding-top:20px;">
                    <button type="button" class="btn btn-outline" onclick="switchStep('step_a')" style="padding:10px 22px; border-radius:var(--r-full); font-weight:600; border-color:var(--line);">
                        &larr; Back to Basic Details
                    </button>
                    <?php if ($isLocked): ?>
                        <button type="button" class="btn btn-outline" onclick="switchStep('step_c')" style="padding:12px 28px; font-weight:600; border-radius:var(--r-full);">
                            <span>Next: View Documents &rarr;</span>
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary" style="padding:12px 28px; font-weight:600; border-radius:var(--r-full);">
                            <span>Save &amp; Continue to Documents</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:6px;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- ================================================================
             STEP C: DOCUMENTS & IDENTITY
             ================================================================ -->
        <div id="step_c_section" class="wizard-step-content" style="<?= $activeStep === 'step_c' ? 'display:block;' : 'display:none;' ?>">
            <form id="form_step_c" onsubmit="event.preventDefault(); saveStepData('step_c', true);">
                <?= csrf_field() ?>
                <input type="hidden" name="step" value="step_c">

                <div style="margin-bottom:24px;">
                    <h3 style="font-size:1.25rem; font-weight:600; color:var(--text); margin:0 0 4px;">Step C &bull; Identity Proofs &amp; Documents</h3>
                    <p style="font-size:13.5px; color:var(--muted); margin:0;">
                        <span style="color:var(--brand); font-weight:600;"><i class="fas fa-shield-alt"></i> AES-256 Encrypted</span>: Aadhaar and PAN numbers are encrypted at rest and masked in compliance with data privacy standards.
                    </p>
                </div>

                <!-- MANDATORY IDENTITY NUMBERS & UPLOADS -->
                <div class="p-3 p-sm-4 mb-4" style="background:var(--bg); border:1px solid var(--line); border-radius:var(--r-md);">
                    <div style="font-size:14px; font-weight:600; color:var(--text); margin-bottom:18px; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-id-card" style="color:var(--brand);"></i> 1. Mandatory Identity Proofs (Aadhaar &amp; PAN)
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 280px), 1fr)); gap:16px;">
                        <!-- Aadhaar Section -->
                        <div class="p-3 p-sm-4" style="background:var(--surface); border:1px solid var(--line); border-radius:var(--r-md);">
                            <label class="form-label" for="aadhaar_number">
                                Aadhaar Number (12-Digit) *
                            </label>
                            <input type="text" id="aadhaar_number" name="aadhaar_number" maxlength="14" class="form-control"
                                   style="font-size:15px; font-weight:600; letter-spacing:2px; color:var(--text); margin-bottom:14px;"
                                   value="<?= !empty($profile['aadhaar_decrypted']) ? e($profile['aadhaar_decrypted']) : (!empty($profile['aadhaar_last4']) ? ('XXXX XXXX ' . $profile['aadhaar_last4']) : '') ?>"
                                   placeholder="XXXX XXXX XXXX" oninput="formatAadhaar(this)" required <?= $isLocked ? 'readonly' : '' ?>>
                            
                            <!-- Aadhaar Card Front / Both Sides Slot -->
                            <?php renderCandidateDocSlot('aadhaar_front', 'Aadhaar Card (Front / Both Sides)', 'Clear scan or photo (PDF / JPG / PNG, Max 2 MB)', true, $docs, $isLocked); ?>
                            
                            <!-- Optional Aadhaar Back Side Slot -->
                            <?php renderCandidateDocSlot('aadhaar_back', 'Aadhaar Card (Back Side)', 'Back side with address if separate file', false, $docs, $isLocked); ?>
                        </div>

                        <!-- PAN Section -->
                        <div class="p-3 p-sm-4" style="background:var(--surface); border:1px solid var(--line); border-radius:var(--r-md);">
                            <label class="form-label" for="pan_number">
                                PAN Number (ABCDE1234F) *
                            </label>
                            <input type="text" id="pan_number" name="pan_number" maxlength="10" class="form-control"
                                   style="font-size:15px; font-weight:600; letter-spacing:2px; text-transform:uppercase; color:var(--text); margin-bottom:14px;"
                                   value="<?= !empty($profile['pan_decrypted']) ? e($profile['pan_decrypted']) : (!empty($profile['pan_last4']) ? ('XXXXX' . $profile['pan_last4']) : '') ?>"
                                   placeholder="ABCDE1234F" oninput="this.value = this.value.toUpperCase();" pattern="[A-Z]{5}[0-9]{4}[A-Z]" required <?= $isLocked ? 'readonly' : '' ?>>
                            
                            <!-- PAN Card Slot -->
                            <?php renderCandidateDocSlot('pan_card', 'PAN Card Document', 'Income Tax department PAN card scan (Max 2 MB)', true, $docs, $isLocked); ?>
                        </div>
                    </div>
                </div>

                <!-- OTHER DOCUMENTS (OPTIONAL / IF APPLICABLE) -->
                <div class="p-3 p-sm-4 mb-4" style="background:var(--surface); border:1px solid var(--line); border-radius:var(--r-md);">
                    <div style="font-size:14px; font-weight:600; color:var(--text); margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-folder-open" style="color:var(--brand);"></i> 2. Other Supporting Documents
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap:16px;">
                        <div>
                            <?php renderCandidateDocSlot('profile_photo', 'Passport-size Formal Photograph', 'Recent formal portrait photo (JPG / PNG)', false, $docs, $isLocked); ?>
                        </div>
                        <div>
                            <?php renderCandidateDocSlot('marksheet', 'Highest Qualification Degree / Marksheet', 'Degree, Diploma or 10+2 marksheet (PDF / JPG / PNG)', false, $docs, $isLocked); ?>
                        </div>
                        <div>
                            <?php renderCandidateDocSlot('offer_letter', 'Offer / Appointment Letter', 'Current or previous company offer letter (if experienced)', false, $docs, $isLocked); ?>
                        </div>
                        <div>
                            <?php renderCandidateDocSlot('relieving_letter', 'Relieving / Experience Certificate', 'Experience certificate from previous employer (if applicable)', false, $docs, $isLocked); ?>
                        </div>
                        <div>
                            <?php renderCandidateDocSlot('cancelled_cheque', 'Cancelled Cheque / Bank Passbook', 'First page showing account number & IFSC for salary credit', false, $docs, $isLocked); ?>
                        </div>
                    </div>
                </div>

                <!-- 3. EMERGENCY CONTACTS (2 CONTACTS) -->
                <div class="p-3 p-sm-4 mb-4" style="background:var(--surface); border:1px solid var(--line); border-radius:var(--r-md);">
                    <div style="font-size:14px; font-weight:600; color:var(--text); margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                        <i class="fas fa-phone-alt" style="color:var(--brand);"></i> 3. Emergency Contacts
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap:16px;">
                        <!-- Contact 1 -->
                        <div class="p-3" style="border:1px solid var(--line); border-radius:var(--r-md); background:var(--bg);">
                            <div style="font-weight:600; font-size:13px; color:var(--text); margin-bottom:12px;">Primary Emergency Contact</div>
                            <div style="margin-bottom:12px;">
                                <label class="form-label" for="emergency_name_1" style="font-size:12px;">Contact Name *</label>
                                <input type="text" id="emergency_name_1" name="emergency_name_1" class="form-control" value="<?= e($emergency['contact_name'] ?? '') ?>" placeholder="e.g. Ramesh Malhotra" required <?= $isLocked ? 'readonly' : '' ?>>
                            </div>
                            <div style="margin-bottom:12px;">
                                <label class="form-label" for="emergency_rel_1" style="font-size:12px;">Relationship *</label>
                                <input type="text" id="emergency_rel_1" name="emergency_rel_1" class="form-control" value="<?= e($emergency['relationship'] ?? 'Father') ?>" placeholder="e.g. Father, Mother, Spouse" required <?= $isLocked ? 'readonly' : '' ?>>
                            </div>
                            <div>
                                <label class="form-label" for="emergency_phone_1" style="font-size:12px;">Phone Number *</label>
                                <input type="tel" id="emergency_phone_1" name="emergency_phone_1" maxlength="10" pattern="[6-9][0-9]{9}" class="form-control" value="<?= e($emergency['mobile'] ?? '') ?>" placeholder="9876543210" required <?= $isLocked ? 'readonly' : '' ?>>
                            </div>
                        </div>

                        <!-- Contact 2 -->
                        <div class="p-3" style="border:1px solid var(--line); border-radius:var(--r-md); background:var(--bg);">
                            <div style="font-weight:600; font-size:13px; color:var(--text); margin-bottom:12px;">Secondary Emergency Contact</div>
                            <div style="margin-bottom:12px;">
                                <label class="form-label" style="font-size:12px;">Contact Name</label>
                                <input type="text" name="emergency_name_2" class="form-control" value="" placeholder="e.g. Sunita Malhotra" <?= $isLocked ? 'readonly' : '' ?>>
                            </div>
                            <div style="margin-bottom:12px;">
                                <label class="form-label" style="font-size:12px;">Relationship</label>
                                <input type="text" name="emergency_rel_2" class="form-control" value="" placeholder="e.g. Mother, Sibling" <?= $isLocked ? 'readonly' : '' ?>>
                            </div>
                            <div>
                                <label class="form-label" style="font-size:12px;">Phone Number</label>
                                <input type="tel" name="emergency_phone_2" maxlength="10" pattern="[6-9][0-9]{9}" class="form-control" value="<?= e($emergency['alternate_mobile'] ?? '') ?>" placeholder="9812345678" <?= $isLocked ? 'readonly' : '' ?>>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--line); padding-top:20px;">
                    <button type="button" class="btn btn-outline" onclick="switchStep('step_b')" style="padding:10px 22px; border-radius:var(--r-full); font-weight:600; border-color:var(--line);">
                        &larr; Back to Social Links
                    </button>
                    <?php if ($isLocked): ?>
                        <button type="button" class="btn btn-outline" onclick="switchStep('step_d')" style="padding:12px 28px; font-weight:600; border-radius:var(--r-full);">
                            <span>Next: View Declaration &rarr;</span>
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-primary" style="padding:12px 28px; font-weight:600; border-radius:var(--r-full);">
                            <span>Review &amp; Sign Declaration</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left:6px;"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- ================================================================
             STEP D: DECLARATION & REVIEW
             ================================================================ -->
        <div id="step_d_section" class="wizard-step-content" style="<?= $activeStep === 'step_d' ? 'display:block;' : 'display:none;' ?>">
            <form id="form_step_d" onsubmit="event.preventDefault(); submitFinalOnboarding();">
                <?= csrf_field() ?>

                <div style="margin-bottom:24px;">
                    <h3 style="font-size:1.25rem; font-weight:600; color:var(--text); margin:0 0 4px;">Step D &bull; Application Review &amp; Digital Signature</h3>
                    <p style="font-size:13.5px; color:var(--muted); margin:0;">Please review your submitted information carefully before providing your signature and final submission.</p>
                </div>

                <!-- Comprehensive Review Card -->
                <div class="p-4 mb-4" style="background:var(--bg); border:1px solid var(--line); border-radius:var(--r-md);">
                    <div style="font-size:14px; font-weight:600; color:var(--text); margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                        <span>Summary of Provided Credentials</span>
                    </div>

                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px 24px; font-size:13.5px;">
                        <div>
                            <span style="color:var(--muted); display:block; font-size:11.5px; text-transform:uppercase; letter-spacing:0.04em; font-weight:600; margin-bottom:2px;">Candidate Name</span>
                            <strong id="rev_full_name" style="color:var(--text); font-weight:500;"><?= e($candidate['full_name']) ?></strong>
                        </div>
                        <div>
                            <span style="color:var(--muted); display:block; font-size:11.5px; text-transform:uppercase; letter-spacing:0.04em; font-weight:600; margin-bottom:2px;">Father's Name</span>
                            <strong id="rev_father_name" style="color:var(--text); font-weight:500;"><?= e($profile['father_name'] ?? ($candidate['father_name'] ?? 'Not provided')) ?></strong>
                        </div>
                        <div>
                            <span style="color:var(--muted); display:block; font-size:11.5px; text-transform:uppercase; letter-spacing:0.04em; font-weight:600; margin-bottom:2px;">Email &bull; Mobile</span>
                            <strong style="color:var(--text); font-weight:500;"><?= e($candidate['email']) ?> &bull; <?= e($candidate['mobile']) ?></strong>
                        </div>
                        <div>
                            <span style="color:var(--muted); display:block; font-size:11.5px; text-transform:uppercase; letter-spacing:0.04em; font-weight:600; margin-bottom:2px;">WhatsApp</span>
                            <strong id="rev_whatsapp" style="color:var(--text); font-weight:500;"><?= e($profile['whatsapp'] ?? ($candidate['whatsapp'] ?? $candidate['mobile'])) ?></strong>
                        </div>
                        <div>
                            <span style="color:var(--muted); display:block; font-size:11.5px; text-transform:uppercase; letter-spacing:0.04em; font-weight:600; margin-bottom:2px;">Date of Birth &bull; Gender</span>
                            <strong id="rev_dob_gender" style="color:var(--text); font-weight:500;"><?= e($profile['dob'] ?? 'Not set') ?> (<?= ucfirst($profile['gender'] ?? '') ?>)</strong>
                        </div>
                        <div>
                            <span style="color:var(--muted); display:block; font-size:11.5px; text-transform:uppercase; letter-spacing:0.04em; font-weight:600; margin-bottom:2px;">City, State &bull; Pincode</span>
                            <strong id="rev_address" style="color:var(--text); font-weight:500;"><?= e($profile['city'] ?? '') ?>, <?= e($profile['state'] ?? '') ?> &bull; <?= e($profile['pincode'] ?? '') ?></strong>
                        </div>
                        <div>
                            <span style="color:var(--muted); display:block; font-size:11.5px; text-transform:uppercase; letter-spacing:0.04em; font-weight:600; margin-bottom:2px;">Aadhaar Number</span>
                            <strong id="rev_aadhaar" style="color:var(--text); font-weight:500;"><?= !empty($profile['aadhaar_masked']) ? e($profile['aadhaar_masked']) : 'Encrypted on file' ?></strong>
                        </div>
                        <div>
                            <span style="color:var(--muted); display:block; font-size:11.5px; text-transform:uppercase; letter-spacing:0.04em; font-weight:600; margin-bottom:2px;">PAN Number</span>
                            <strong id="rev_pan" style="color:var(--text); font-weight:500;"><?= !empty($profile['pan_masked']) ? e($profile['pan_masked']) : 'Encrypted on file' ?></strong>
                        </div>
                    </div>
                </div>

                <!-- DPDP-Friendly Consent Statement -->
                <div class="p-4 mb-4" style="background:var(--surface); border:1px solid var(--line); border-radius:var(--r-md);">
                    <div style="display:flex; gap:14px; align-items:flex-start;">
                        <input type="checkbox" id="declaration_accepted" name="declaration_accepted" value="1" style="width:20px; height:20px; margin-top:3px; cursor:pointer;" <?= !empty($profile['declaration_accepted']) ? 'checked' : '' ?> required <?= $isLocked ? 'disabled' : '' ?>>
                        <div>
                            <label for="declaration_accepted" style="font-size:14px; font-weight:600; color:var(--text); cursor:pointer;">
                                Indian DPDP Act Compliance &amp; Truthfulness Declaration *
                            </label>
                            <p style="font-size:12.5px; color:var(--muted); line-height:1.6; margin:6px 0 0;">
                                I hereby declare that all information, documents, and credentials furnished in this onboarding application are true, accurate, and complete to the best of my knowledge and belief. I understand that any false statement or omission may lead to immediate disqualification or termination of engagement. In accordance with the Digital Personal Data Protection guidelines, I give voluntary consent to Realty Smartz Pathshala to securely store, process, and verify my credentials for employment, training, and talent placement purposes.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Digital Signature Component (Typed or Drawn) -->
                <div class="p-4 mb-4" style="background:var(--bg); border:1px solid var(--line); border-radius:var(--r-md);">
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
                        <div>
                            <div style="font-size:14px; font-weight:600; color:var(--text);">Digital Signature *</div>
                            <div style="font-size:12px; color:var(--muted);">Choose your preferred signing method below</div>
                        </div>

                        <!-- Toggle Buttons -->
                        <div class="d-flex gap-1 p-1 bg-white border" style="border-radius:var(--r-full); border-color:var(--line) !important; <?= $isLocked ? 'pointer-events:none; opacity:0.6;' : '' ?>">
                            <button type="button" id="btnSigType" class="btn btn-sm <?= (($profile['signature_type'] ?? 'typed') === 'typed') ? 'btn-primary' : 'btn-outline' ?>" style="border-radius:var(--r-full); font-size:12px; padding:4px 14px;" onclick="setSignatureMode('typed')" <?= $isLocked ? 'disabled' : '' ?>>
                                Type Name
                            </button>
                            <button type="button" id="btnSigDraw" class="btn btn-sm <?= (($profile['signature_type'] ?? '') === 'drawn') ? 'btn-primary' : 'btn-outline' ?>" style="border-radius:var(--r-full); font-size:12px; padding:4px 14px;" onclick="setSignatureMode('drawn')" <?= $isLocked ? 'disabled' : '' ?>>
                                Draw Signature
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="signature_type" id="signature_type" value="<?= e($profile['signature_type'] ?? 'typed') ?>">
                    <input type="hidden" name="signature_data" id="signature_data" value="<?= e($profile['signature_data'] ?? $candidate['full_name']) ?>">

                    <!-- Type Mode -->
                    <div id="sigTypedContainer" style="<?= (($profile['signature_type'] ?? 'typed') === 'typed') ? 'display:block;' : 'display:none;' ?>">
                        <label class="form-label" for="typedSigInput">Type Your Full Legal Name to Sign</label>
                        <input type="text" id="typedSigInput" class="form-control" value="<?= e($profile['signature_data'] ?? $candidate['full_name']) ?>" placeholder="e.g. Vikram Malhotra" oninput="updateTypedSignature(this.value)" <?= $isLocked ? 'readonly' : '' ?>>
                        
                        <div style="margin-top:16px; background:var(--surface); border:1px dashed var(--line); border-radius:var(--r-md); padding:20px 24px; text-align:center;">
                            <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:0.04em; margin-bottom:8px; font-weight:600;">Live Signature Preview</div>
                            <div id="typedSigPreview" style="font-size:28px; color:var(--brand); font-weight:600; min-height:42px;">
                                <?= e($profile['signature_data'] ?? $candidate['full_name']) ?>
                            </div>
                            <div style="font-size:11px; color:var(--muted); margin-top:8px;">
                                Timestamp: <span id="sigTimestamp"><?= date('d M Y, h:i A') ?></span> &bull; Verified Candidate
                            </div>
                        </div>
                    </div>

                    <!-- Draw Mode -->
                    <div id="sigDrawnContainer" style="<?= (($profile['signature_type'] ?? '') === 'drawn') ? 'display:block;' : 'display:none;' ?>">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <label class="form-label" style="margin-bottom:0;">Draw Signature with Mouse or Touchscreen</label>
                            <?php if (!$isLocked): ?>
                                <button type="button" class="btn btn-outline btn-sm" onclick="clearSignatureCanvas()" style="font-size:11.5px; padding:4px 12px; border-radius:var(--r-full); border-color:var(--line);">
                                    Clear Canvas
                                </button>
                            <?php endif; ?>
                        </div>

                        <div style="background:#ffffff; border:1px solid var(--line); border-radius:var(--r-md); overflow:hidden; touch-action:none;">
                            <canvas id="sigCanvas" width="600" height="150" style="width:100%; height:150px; display:block; cursor:crosshair;"></canvas>
                        </div>
                        <div style="font-size:11px; color:var(--muted); margin-top:6px;">Use finger, stylus, or mouse pointer to draw your signature smoothly.</div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--line); padding-top:24px;">
                    <button type="button" class="btn btn-outline" onclick="switchStep('step_c')" style="padding:10px 22px; border-radius:var(--r-full); font-weight:600; border-color:var(--line);">
                        &larr; Back to Documents
                    </button>

                    <?php if ($isLocked): ?>
                        <a href="<?= url('/dashboard/aptitude') ?>" class="btn btn-primary" style="padding:14px 32px; font-weight:600; border-radius:var(--r-full); text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                            <span>Take Aptitude Assessment</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </a>
                    <?php else: ?>
                        <button type="submit" id="btnSubmitFinal" class="btn btn-primary" style="padding:14px 36px; font-size:15px; font-weight:600; border-radius:var(--r-full); display:inline-flex; align-items:center; gap:8px;">
                            <span>Submit Onboarding &amp; Unlock Aptitude</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        </div>
    </div>
</div>

<!-- Compression, In-Place Upload & Wizard Engine Script -->
<script>
let currentStepId = '<?= e($activeStep) ?>';
let autoSaveTimeout = null;
let canvas, ctx, isDrawing = false;

let stepState = {
    step_a_completed: <?= $stepACompleted ? 'true' : 'false' ?>,
    step_b_completed: <?= $stepBCompleted ? 'true' : 'false' ?>,
    step_c_completed: <?= $stepCCompleted ? 'true' : 'false' ?>,
    step_d_completed: <?= $stepDCompleted ? 'true' : 'false' ?>,
    max_allowed_step: '<?= e($maxAllowedStep) ?>',
    is_locked: <?= $isLocked ? 'true' : 'false' ?>
};

// Map of uploaded documents
const uploadedDocs = {
    aadhaar_front: <?= (!empty($docs['aadhaar_front']) || !empty($docs['aadhaar']) || !empty($docs['aadhaar_card'])) ? 'true' : 'false' ?>,
    aadhaar_back: <?= !empty($docs['aadhaar_back']) ? 'true' : 'false' ?>,
    pan_card: <?= (!empty($docs['pan_card']) || !empty($docs['pan'])) ? 'true' : 'false' ?>,
    profile_photo: <?= !empty($docs['profile_photo']) ? 'true' : 'false' ?>,
    marksheet: <?= !empty($docs['marksheet']) ? 'true' : 'false' ?>,
    offer_letter: <?= !empty($docs['offer_letter']) ? 'true' : 'false' ?>,
    relieving_letter: <?= !empty($docs['relieving_letter']) ? 'true' : 'false' ?>,
    cancelled_cheque: <?= !empty($docs['cancelled_cheque']) ? 'true' : 'false' ?>
};

function getDocLabel(docType) {
    const labels = {
        aadhaar_front: 'Aadhaar Card (Front / Both Sides)',
        aadhaar_back: 'Aadhaar Card (Back Side)',
        pan_card: 'PAN Card Document',
        profile_photo: 'Passport Photograph',
        marksheet: 'Degree / Marksheet',
        offer_letter: 'Offer Letter',
        relieving_letter: 'Relieving Certificate',
        cancelled_cheque: 'Cancelled Cheque / Passbook'
    };
    return labels[docType] || 'Document';
}

// Unified Prominent Toast Notification System
function showToast(msg, type = 'success') {
    let toast = document.getElementById('wizardGlobalToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'wizardGlobalToast';
        document.body.appendChild(toast);
    }

    const isWarn = (type === 'warning' || type === 'error' || type === 'danger');
    const bg = isWarn ? '#B35C00' : 'var(--brand)';
    const shadow = isWarn ? '0 10px 30px rgba(179,92,0,0.35)' : '0 10px 30px rgba(31,92,74,0.35)';

    toast.style.cssText = `
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 10000;
        max-width: 460px;
        background: ${bg};
        color: #FFFFFF;
        padding: 14px 20px;
        border-radius: var(--r-md);
        box-shadow: ${shadow};
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        font-weight: 500;
        animation: slideInToast 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    `;

    const iconSvg = isWarn 
        ? `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`
        : `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>`;

    toast.innerHTML = `
        ${iconSvg}
        <span style="flex:1; line-height:1.45;">${msg}</span>
        <button type="button" onclick="this.parentElement.style.display='none'" style="background:transparent; border:none; color:#FFFFFF; opacity:0.8; cursor:pointer; font-size:18px; line-height:1; padding:0 4px; margin-left:6px;">&times;</button>
    `;
    toast.style.display = 'flex';

    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => {
        toast.style.display = 'none';
    }, 4500);
}

function showStepGateNotice(msg) {
    showToast(msg, 'warning');
}

function isStepAllowed(targetStep) {
    if (stepState.is_locked) return true;
    if (targetStep === 'step_a') return true;
    if (targetStep === 'step_b') {
        return !!stepState.step_a_completed;
    }
    if (targetStep === 'step_c') {
        return !!(stepState.step_a_completed && stepState.step_b_completed);
    }
    if (targetStep === 'step_d') {
        return !!(stepState.step_a_completed && stepState.step_b_completed && stepState.step_c_completed);
    }
    return false;
}

function getFirstMissingFieldStepA() {
    const fields = ['full_name', 'father_name', 'whatsapp', 'dob', 'gender', 'address', 'city', 'state', 'pincode'];
    for (const id of fields) {
        const el = document.getElementById(id);
        if (el) {
            const val = (el.value || '').trim();
            if (!val) return el;
            if (id === 'whatsapp' && val.replace(/\D/g, '').length !== 10) return el;
            if (id === 'pincode' && val.replace(/\D/g, '').length !== 6) return el;
        }
    }
    return null;
}

function validateStepA() {
    const missing = getFirstMissingFieldStepA();
    if (missing) {
        const labels = {
            'full_name': 'Full Legal Name',
            'father_name': "Father's Full Name",
            'whatsapp': '10-digit WhatsApp Number',
            'dob': 'Date of Birth',
            'gender': 'Gender',
            'address': 'Current Residential Address',
            'city': 'City',
            'state': 'State',
            'pincode': '6-digit Pincode'
        };
        const labelText = labels[missing.id] || 'required fields';
        showToast('Please provide your ' + labelText + ' in Step 1 to proceed.', 'warning');
        missing.focus();
        missing.classList.add('pulse-highlight');
        setTimeout(() => missing.classList.remove('pulse-highlight'), 3000);
        return false;
    }
    return true;
}

function validateStepB() {
    const linkedinInput = document.getElementById('soc_linkedin');
    const linkedinVal = linkedinInput ? linkedinInput.value.trim() : '';
    if (!linkedinVal) {
        showToast('LinkedIn profile URL is required to proceed.', 'warning');
        if (linkedinInput) {
            linkedinInput.focus();
            linkedinInput.classList.add('pulse-highlight');
            setTimeout(() => linkedinInput.classList.remove('pulse-highlight'), 3000);
        }
        return false;
    }
    const linkedinPattern = /^(https?:\/\/)?([a-zA-Z0-9_\-]+\.)?linkedin\.com\/.+/i;
    if (!linkedinPattern.test(linkedinVal)) {
        showToast('Please enter a valid LinkedIn URL (e.g. https://www.linkedin.com/in/yourname)', 'warning');
        if (linkedinInput) {
            linkedinInput.focus();
            linkedinInput.classList.add('pulse-highlight');
            setTimeout(() => linkedinInput.classList.remove('pulse-highlight'), 3000);
        }
        return false;
    }
    return true;
}

function validateStepC() {
    // 1. Aadhaar Number
    const aadhaarInput = document.getElementById('aadhaar_number');
    const aadhaarDigits = aadhaarInput ? aadhaarInput.value.replace(/\D/g, '') : '';
    if (aadhaarDigits.length !== 12) {
        showToast('Please enter a valid 12-digit Aadhaar Number in Step 3.', 'warning');
        if (aadhaarInput) {
            aadhaarInput.focus();
            aadhaarInput.classList.add('pulse-highlight');
            setTimeout(() => aadhaarInput.classList.remove('pulse-highlight'), 3000);
        }
        return false;
    }

    // 2. Aadhaar Document Upload
    if (!uploadedDocs.aadhaar_front) {
        showToast('Please upload your Aadhaar Card (Front / Both Sides) before proceeding.', 'warning');
        const container = document.getElementById('slot_container_aadhaar_front');
        if (container) {
            container.scrollIntoView({ behavior: 'smooth', block: 'center' });
            container.classList.add('pulse-highlight');
            setTimeout(() => container.classList.remove('pulse-highlight'), 3000);
        }
        return false;
    }

    // 3. PAN Number
    const panInput = document.getElementById('pan_number');
    const panVal = panInput ? panInput.value.trim().toUpperCase() : '';
    const panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]$/;
    if (!panPattern.test(panVal)) {
        showToast('Please enter a valid 10-character PAN Number (e.g. ABCDE1234F).', 'warning');
        if (panInput) {
            panInput.focus();
            panInput.classList.add('pulse-highlight');
            setTimeout(() => panInput.classList.remove('pulse-highlight'), 3000);
        }
        return false;
    }

    // 4. PAN Card Upload
    if (!uploadedDocs.pan_card) {
        showToast('Please upload your PAN Card document before proceeding.', 'warning');
        const container = document.getElementById('slot_container_pan_card');
        if (container) {
            container.scrollIntoView({ behavior: 'smooth', block: 'center' });
            container.classList.add('pulse-highlight');
            setTimeout(() => container.classList.remove('pulse-highlight'), 3000);
        }
        return false;
    }

    // 5. Emergency Contact 1
    const formC = document.getElementById('form_step_c');
    if (formC) {
        const name1 = (formC.querySelector('input[name="emergency_name_1"]')?.value || '').trim();
        const rel1 = (formC.querySelector('input[name="emergency_rel_1"]')?.value || '').trim();
        const phone1 = (formC.querySelector('input[name="emergency_phone_1"]')?.value || '').trim().replace(/\D/g, '');

        if (!name1) {
            showToast('Please provide Primary Emergency Contact Name in Step 3.', 'warning');
            formC.querySelector('input[name="emergency_name_1"]')?.focus();
            return false;
        }
        if (!rel1) {
            showToast('Please provide Primary Emergency Contact Relationship.', 'warning');
            formC.querySelector('input[name="emergency_rel_1"]')?.focus();
            return false;
        }
        if (phone1.length !== 10) {
            showToast('Please enter a valid 10-digit Primary Emergency Contact Phone Number.', 'warning');
            formC.querySelector('input[name="emergency_phone_1"]')?.focus();
            return false;
        }
    }

    return true;
}

function syncReviewDetails() {
    const formA = document.getElementById('form_step_a');
    if (formA) {
        const fullName = formA.querySelector('#full_name')?.value;
        const fatherName = formA.querySelector('#father_name')?.value;
        const whatsapp = formA.querySelector('#whatsapp')?.value;
        const dob = formA.querySelector('#dob')?.value;
        const gender = formA.querySelector('#gender')?.value;
        const city = formA.querySelector('#city')?.value;
        const state = formA.querySelector('#state')?.value;
        const pincode = formA.querySelector('#pincode')?.value;

        if (fullName) {
            const el = document.getElementById('rev_full_name');
            if (el) el.textContent = fullName;
        }
        if (fatherName) {
            const el = document.getElementById('rev_father_name');
            if (el) el.textContent = fatherName;
        }
        if (whatsapp) {
            const el = document.getElementById('rev_whatsapp');
            if (el) el.textContent = whatsapp;
        }
        if (dob) {
            const el = document.getElementById('rev_dob_gender');
            if (el) el.textContent = dob + (gender ? ' (' + gender.charAt(0).toUpperCase() + gender.slice(1) + ')' : '');
        }
        if (city && state) {
            const el = document.getElementById('rev_address');
            if (el) el.textContent = city + ', ' + state + (pincode ? ' • ' + pincode : '');
        }
    }

    const formC = document.getElementById('form_step_c');
    if (formC) {
        const aadhaar = formC.querySelector('#aadhaar_number')?.value;
        if (aadhaar) {
            const digits = aadhaar.replace(/\D/g, '');
            const masked = digits.length === 12 ? ('XXXX XXXX ' + digits.slice(-4)) : aadhaar;
            const el = document.getElementById('rev_aadhaar');
            if (el) el.textContent = masked;
        }
        const pan = formC.querySelector('#pan_number')?.value;
        if (pan) {
            const p = pan.trim().toUpperCase();
            const masked = p.length === 10 ? ('XXXXX' + p.slice(-4)) : p;
            const el = document.getElementById('rev_pan');
            if (el) el.textContent = masked;
        }
    }
}

function switchStep(stepId, updateHistory = true) {
    if (!isStepAllowed(stepId)) {
        if (!stepState.step_a_completed) {
            showToast('Please complete and save Step 1 (Basic Details) first.', 'warning');
            switchStep('step_a', updateHistory);
            setTimeout(() => {
                const missing = getFirstMissingFieldStepA();
                if (missing) {
                    missing.focus();
                    missing.classList.add('pulse-highlight');
                    setTimeout(() => missing.classList.remove('pulse-highlight'), 3000);
                }
            }, 100);
            return;
        }
        if (!stepState.step_b_completed && (stepId === 'step_c' || stepId === 'step_d')) {
            showToast('Please save Step 2 (Social Profiles) before continuing.', 'warning');
            switchStep('step_b', updateHistory);
            return;
        }
        if (!stepState.step_c_completed && stepId === 'step_d') {
            showToast('Please complete Step 3 (Identity Proofs & Emergency Contacts) before review.', 'warning');
            switchStep('step_c', updateHistory);
            return;
        }
        showToast('This step is locked until previous steps are completed.', 'warning');
        return;
    }

    if (stepId === 'step_d') {
        syncReviewDetails();
    }

    document.querySelectorAll('.wizard-step-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.wizard-tab-btn').forEach(btn => btn.classList.remove('active'));

    const targetSection = document.getElementById(stepId + '_section');
    if (targetSection) targetSection.style.display = 'block';

    const tabBtn = document.getElementById('tab_' + stepId);
    if (tabBtn) tabBtn.classList.add('active');

    const stepTitles = {
        'step_a': 'Step 1 of 4 • Basic Details',
        'step_b': 'Step 2 of 4 • Social Links',
        'step_c': 'Step 3 of 4 • Documents & KYC',
        'step_d': 'Step 4 of 4 • Review & Sign'
    };
    const mobTitle = document.getElementById('mobileStepTitle');
    if (mobTitle && stepTitles[stepId]) {
        mobTitle.textContent = stepTitles[stepId];
    }

    currentStepId = stepId;

    try {
        localStorage.setItem('candidate_wizard_step', stepId);
        if (updateHistory && window.history && window.history.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.set('step', stepId);
            window.history.replaceState({ step: stepId }, '', url.toString());
        }
    } catch (e) {}

    window.scrollTo({ top: 120, behavior: 'smooth' });
}

function updateStepLocks(status) {
    if (!status) return;
    stepState.step_a_completed = !!status.step_a_completed;
    stepState.step_b_completed = !!status.step_b_completed;
    stepState.step_c_completed = !!status.step_c_completed;
    stepState.step_d_completed = !!status.step_d_completed;
    stepState.max_allowed_step = status.max_allowed_step || 'step_a';

    const stepBAllowed = stepState.step_a_completed || stepState.is_locked;
    const stepCAllowed = (stepState.step_a_completed && stepState.step_b_completed) || stepState.is_locked;
    const stepDAllowed = (stepState.step_a_completed && stepState.step_b_completed && stepState.step_c_completed) || stepState.is_locked;

    // Step A
    const badgeA = document.getElementById('badge_step_a');
    if (badgeA) badgeA.textContent = stepState.step_a_completed ? '✓' : '1';

    // Step B
    const tabB = document.getElementById('tab_step_b');
    const badgeB = document.getElementById('badge_step_b');
    const lockB = document.getElementById('lock_step_b');
    if (tabB) tabB.classList.toggle('is-locked', !stepBAllowed);
    if (badgeB) badgeB.textContent = stepState.step_b_completed ? '✓' : '2';
    if (lockB) lockB.style.display = stepBAllowed ? 'none' : 'inline-flex';

    // Step C
    const tabC = document.getElementById('tab_step_c');
    const badgeC = document.getElementById('badge_step_c');
    const lockC = document.getElementById('lock_step_c');
    if (tabC) tabC.classList.toggle('is-locked', !stepCAllowed);
    if (badgeC) badgeC.textContent = stepState.step_c_completed ? '✓' : '3';
    if (lockC) lockC.style.display = stepCAllowed ? 'none' : 'inline-flex';

    // Step D
    const tabD = document.getElementById('tab_step_d');
    const badgeD = document.getElementById('badge_step_d');
    const lockD = document.getElementById('lock_step_d');
    if (tabD) tabD.classList.toggle('is-locked', !stepDAllowed);
    if (badgeD) badgeD.textContent = stepState.step_d_completed ? '✓' : '4';
    if (lockD) lockD.style.display = stepDAllowed ? 'none' : 'inline-flex';
}

document.addEventListener('DOMContentLoaded', function () {
    initCanvas();
    setupAutoSave();
    initDropzones();

    // Restore or sync active wizard step across reloads / uploads, strictly adhering to gating
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const urlStep = urlParams.get('step');
        const savedStep = localStorage.getItem('candidate_wizard_step');
        const validSteps = ['step_a', 'step_b', 'step_c', 'step_d'];
        let targetStep = (urlStep && validSteps.includes(urlStep)) 
            ? urlStep 
            : ((savedStep && validSteps.includes(savedStep)) ? savedStep : currentStepId);

        if (!isStepAllowed(targetStep)) {
            targetStep = stepState.max_allowed_step || 'step_a';
            try { localStorage.setItem('candidate_wizard_step', targetStep); } catch (e) {}
        }

        switchStep(targetStep, false);
    } catch (e) {
        switchStep('step_a', false);
    }
});

function toggleSameAsMobile(chk) {
    const mobileVal = document.getElementById('mobile').value;
    const waInput = document.getElementById('whatsapp');
    if (chk.checked) {
        waInput.value = mobileVal;
    }
}

function formatAadhaar(input) {
    let val = input.value.replace(/\D/g, '');
    let formatted = '';
    for (let i = 0; i < val.length; i++) {
        if (i > 0 && i % 4 === 0) formatted += ' ';
        formatted += val[i];
    }
    input.value = formatted;
}

function setSignatureMode(mode) {
    document.getElementById('signature_type').value = mode;
    const btnType = document.getElementById('btnSigType');
    const btnDraw = document.getElementById('btnSigDraw');
    const contType = document.getElementById('sigTypedContainer');
    const contDraw = document.getElementById('sigDrawnContainer');

    if (mode === 'typed') {
        btnType.className = 'btn btn-sm btn-primary';
        btnDraw.className = 'btn btn-sm btn-outline';
        contType.style.display = 'block';
        contDraw.style.display = 'none';
        updateTypedSignature(document.getElementById('typedSigInput').value);
    } else {
        btnDraw.className = 'btn btn-sm btn-primary';
        btnType.className = 'btn btn-sm btn-outline';
        contDraw.style.display = 'block';
        contType.style.display = 'none';
        setTimeout(initCanvas, 50);
    }
}

function updateTypedSignature(val) {
    const preview = document.getElementById('typedSigPreview');
    const hidden = document.getElementById('signature_data');
    if (preview) preview.innerText = val || 'Your Signature';
    if (hidden && document.getElementById('signature_type').value === 'typed') {
        hidden.value = val;
    }
}

function initCanvas() {
    canvas = document.getElementById('sigCanvas');
    if (!canvas) return;
    ctx = canvas.getContext('2d');
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#1F5C4A';

    // Mouse events
    canvas.onmousedown = (e) => {
        isDrawing = true;
        ctx.beginPath();
        ctx.moveTo(e.offsetX, e.offsetY);
    };
    canvas.onmousemove = (e) => {
        if (!isDrawing) return;
        ctx.lineTo(e.offsetX, e.offsetY);
        ctx.stroke();
    };
    window.addEventListener('mouseup', () => {
        if (isDrawing) {
            isDrawing = false;
            saveCanvasData();
        }
    });

    // Touch events
    canvas.addEventListener('touchstart', (e) => {
        e.preventDefault();
        const touch = e.touches[0];
        const rect = canvas.getBoundingClientRect();
        isDrawing = true;
        ctx.beginPath();
        ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
    });
    canvas.addEventListener('touchmove', (e) => {
        e.preventDefault();
        if (!isDrawing) return;
        const touch = e.touches[0];
        const rect = canvas.getBoundingClientRect();
        ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
        ctx.stroke();
    });
    canvas.addEventListener('touchend', () => {
        if (isDrawing) {
            isDrawing = false;
            saveCanvasData();
        }
    });
}

function clearSignatureCanvas() {
    if (!canvas || !ctx) return;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById('signature_data').value = '';
}

function saveCanvasData() {
    if (!canvas) return;
    const dataUrl = canvas.toDataURL('image/png');
    document.getElementById('signature_data').value = dataUrl;
}

// Format bytes helper
function formatBytes(bytes) {
    if (!bytes || bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return (bytes / Math.pow(k, i)).toFixed(1) + ' ' + sizes[i];
}

// Client-Side Canvas Image Compression (< 1600px, multi-pass adaptive quality)
function compressImageClientSide(file, maxWidth = 1600, targetMaxBytes = 1887436) {
    return new Promise((resolve) => {
        if (!file.type.startsWith('image/') || file.type === 'image/svg+xml') {
            return resolve(file);
        }
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (e) => {
            const img = new Image();
            img.src = e.target.result;
            img.onload = () => {
                let width = img.width;
                let height = img.height;

                if (width > maxWidth || height > maxWidth) {
                    if (width > height) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    } else {
                        width = Math.round((width * maxWidth) / height);
                        height = maxWidth;
                    }
                }

                const cvs = document.createElement('canvas');
                cvs.width = width;
                cvs.height = height;
                const cCtx = cvs.getContext('2d');
                cCtx.drawImage(img, 0, 0, width, height);

                // Multi-pass adaptive quality step-down (0.85 -> 0.70 -> 0.55) to guarantee < 2 MB
                const qualitySteps = [0.85, 0.70, 0.55];
                let stepIdx = 0;

                function tryQuality() {
                    const q = qualitySteps[stepIdx];
                    cvs.toBlob((blob) => {
                        if (!blob) {
                            return resolve(file);
                        }
                        if (blob.size <= targetMaxBytes || stepIdx >= qualitySteps.length - 1) {
                            const newName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                            resolve(new File([blob], newName, { type: 'image/jpeg' }));
                        } else {
                            stepIdx++;
                            tryQuality();
                        }
                    }, 'image/jpeg', q);
                }
                tryQuality();
            };
            img.onerror = () => resolve(file);
        };
        reader.onerror = () => resolve(file);
    });
}

function triggerUpload(docType) {
    if (stepState.is_locked) {
        showToast('Your onboarding is submitted and locked. Documents cannot be modified.', 'warning');
        return;
    }
    const input = document.getElementById('file_' + docType);
    if (input) input.click();
}

async function handleFileUpload(fileInputOrFile, docType) {
    if (stepState.is_locked) {
        showToast('Your onboarding is submitted and locked. Documents cannot be modified.', 'warning');
        return;
    }
    let originalFile = null;
    if (fileInputOrFile instanceof File) {
        originalFile = fileInputOrFile;
    } else if (fileInputOrFile && fileInputOrFile.files && fileInputOrFile.files[0]) {
        originalFile = fileInputOrFile.files[0];
    }
    if (!originalFile) return;

    const progressBar = document.getElementById('bar_' + docType);
    const progressContainer = document.getElementById('progress_' + docType);
    if (progressContainer) progressContainer.style.display = 'block';
    if (progressBar) progressBar.style.width = '10%';

    const originalSize = originalFile.size;
    let fileToUpload = originalFile;

    // Check if client-side compression applies
    if (originalFile.type.startsWith('image/')) {
        showAutoSaveStatus('Compressing ' + formatBytes(originalSize) + '...', 'var(--brand)');
        try {
            fileToUpload = await compressImageClientSide(originalFile, 1600);
        } catch (e) {
            console.warn('Canvas pre-compression bypassed:', e);
        }
    }

    const compressedSize = fileToUpload.size;
    const savings = originalSize > compressedSize ? Math.round(((originalSize - compressedSize) / originalSize) * 100) : 0;
    const sizeNote = savings > 0 
        ? (formatBytes(originalSize) + ' → ' + formatBytes(compressedSize) + ' (-' + savings + '%)')
        : formatBytes(compressedSize);

    showAutoSaveStatus('Uploading ' + sizeNote + '...', 'var(--brand)');
    if (progressBar) progressBar.style.width = '35%';

    const formData = new FormData();
    formData.append('document_type', docType);
    formData.append('file', fileToUpload, originalFile.name);
    formData.append('size_before', originalSize);
    formData.append('_csrf_token', '<?= csrf_token() ?>');

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '<?= url("/dashboard/document/upload") ?>', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            const pct = Math.round((e.loaded / e.total) * 100);
            if (progressBar) progressBar.style.width = Math.max(35, pct) + '%';
            showAutoSaveStatus('Uploading ' + sizeNote + ' (' + pct + '%)...', 'var(--brand)');
        }
    };

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            try {
                const res = JSON.parse(xhr.responseText);
                if (res.success) {
                    if (progressBar) progressBar.style.width = '100%';
                    showAutoSaveStatus('Uploaded & verified! (' + sizeNote + ')', 'var(--brand)');

                    // Mark document as uploaded
                    uploadedDocs[docType] = true;

                    // In-Place DOM Update without page reload!
                    const previewBox = document.getElementById('slot_preview_' + docType);
                    const dropzone = document.getElementById('dropzone_' + docType);
                    const badgeContainer = document.getElementById('slot_badge_' + docType);
                    const nameEl = document.getElementById('slot_name_' + docType);
                    const metaEl = document.getElementById('slot_meta_' + docType);
                    const viewEl = document.getElementById('slot_view_' + docType);
                    const iconEl = document.getElementById('slot_icon_' + docType);

                    if (badgeContainer) {
                        badgeContainer.innerHTML = `
                            <span class="doc-uploaded-chip">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Uploaded</span>
                            </span>
                        `;
                    }

                    if (nameEl) {
                        nameEl.textContent = res.document?.original_name || originalFile.name;
                        nameEl.title = res.document?.original_name || originalFile.name;
                    }

                    if (metaEl) {
                        const finalSizeKb = Math.round(compressedSize / 1024);
                        metaEl.innerHTML = `<span>${finalSizeKb} KB</span>` + 
                            (savings > 0 ? `<span style="color:var(--brand); font-weight:600;">(-${savings}% compressed)</span>` : '') +
                            `<span>&bull; v${res.document?.version || 1}</span>`;
                    }

                    if (viewEl && res.document?.id) {
                        viewEl.href = '<?= url("/document/download/") ?>' + res.document.id;
                    }

                    if (iconEl) {
                        if (originalFile.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = (ev) => {
                                iconEl.innerHTML = `<img src="${ev.target.result}" alt="Preview" style="width:100%; height:100%; object-fit:cover;">`;
                            };
                            reader.readAsDataURL(fileToUpload);
                        } else if (originalFile.type === 'application/pdf') {
                            iconEl.innerHTML = `<i class="fas fa-file-pdf" style="color:var(--danger);"></i>`;
                        } else {
                            iconEl.innerHTML = `<i class="fas fa-file-alt" style="color:var(--brand);"></i>`;
                        }
                    }

                    if (previewBox) previewBox.style.display = 'flex';
                    if (dropzone) dropzone.style.display = 'none';
                    if (progressContainer) {
                        setTimeout(() => { progressContainer.style.display = 'none'; }, 600);
                    }

                    // Reset file input so re-uploading the same file works
                    const fileInput = document.getElementById('file_' + docType);
                    if (fileInput) fileInput.value = '';

                    // Update Locks and Progress
                    if (res.step_status) {
                        updateStepLocks(res.step_status);
                    }
                    if (res.completion_percentage !== undefined) {
                        updateProgressUI(res.completion_percentage);
                    }

                    // Prominent Toast Notification
                    showToast('✓ ' + getDocLabel(docType) + ' uploaded & verified successfully!', 'success');
                } else {
                    showToast('Upload failed: ' + (res.error || 'Validation error'), 'warning');
                    showAutoSaveStatus('Upload failed', 'var(--danger)');
                    if (progressContainer) progressContainer.style.display = 'none';
                }
            } catch (err) {
                showToast('Upload error: Invalid server response', 'warning');
                showAutoSaveStatus('Upload error', 'var(--danger)');
                if (progressContainer) progressContainer.style.display = 'none';
            }
        } else {
            let errorMsg = 'Upload error (' + xhr.status + ')';
            try {
                const errRes = JSON.parse(xhr.responseText);
                if (errRes && errRes.error) {
                    errorMsg = errRes.error;
                }
            } catch (e) {}
            showToast(errorMsg, 'warning');
            showAutoSaveStatus(errorMsg, 'var(--danger)');
            if (progressContainer) progressContainer.style.display = 'none';
        }
    };

    xhr.onerror = function() {
        showToast('Network connection error while uploading document', 'warning');
        showAutoSaveStatus('Network error', 'var(--danger)');
        if (progressContainer) progressContainer.style.display = 'none';
    };

    xhr.send(formData);
}

function initDropzones() {
    document.querySelectorAll('.dropzone-box').forEach(box => {
        const docType = box.getAttribute('data-doctype');
        ['dragenter', 'dragover'].forEach(eventName => {
            box.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                box.classList.add('dragover');
            }, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            box.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                box.classList.remove('dragover');
            }, false);
        });
        box.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0 && docType) {
                handleFileUpload(dt.files[0], docType);
            }
        }, false);
    });
}

// Auto-Save Engine (text fields only, excluding file inputs and hidden inputs)
function setupAutoSave() {
    if (stepState.is_locked) {
        return; // Disable all auto-saving if profile is locked
    }
    document.querySelectorAll('#form_step_a, #form_step_b, #form_step_c').forEach(form => {
        form.querySelectorAll('input:not([type="file"]):not([type="hidden"]), select, textarea').forEach(input => {
            input.addEventListener('input', () => {
                showAutoSaveStatus('Saving changes...', 'var(--muted)');
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => {
                    saveStepData(currentStepId, false);
                }, 1500);
            });
        });
    });
}

async function saveStepData(stepId, advanceToNext = false) {
    if (stepState.is_locked) {
        if (advanceToNext) {
            const nextStep = (stepId === 'step_a') ? 'step_b' : ((stepId === 'step_b') ? 'step_c' : 'step_d');
            switchStep(nextStep);
        }
        return;
    }

    if (stepId === 'step_a' && advanceToNext) {
        if (!validateStepA()) {
            return;
        }
    }

    if (stepId === 'step_b' && advanceToNext) {
        if (!validateStepB()) {
            return;
        }
    }

    if (stepId === 'step_c' && advanceToNext) {
        if (!validateStepC()) {
            return;
        }
    }

    const form = document.getElementById('form_' + stepId);
    if (!form) return;

    const formData = new FormData(form);
    formData.append('step', stepId);
    formData.append('_csrf_token', '<?= csrf_token() ?>');

    showAutoSaveStatus('Saving to server...', 'var(--brand)');

    try {
        const res = await fetch('<?= parse_url(url("/dashboard/onboarding/save-step"), PHP_URL_PATH) ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        let data = null;
        try {
            data = await res.json();
        } catch (jsonErr) {
            if (res.status === 401 || res.status === 419) {
                showToast('Your session has expired. Please refresh the page or log in again.', 'warning');
            } else {
                showToast('Server returned an unexpected response (' + res.status + '). Please try again.', 'warning');
            }
            showAutoSaveStatus('Failed to save', 'var(--danger)');
            return;
        }

        if (data && data.success) {
            showAutoSaveStatus('All changes saved', 'var(--brand)');
            if (data.completion_percentage !== undefined) {
                updateProgressUI(data.completion_percentage);
            }
            if (data.step_status) {
                updateStepLocks(data.step_status);
            }

            if (advanceToNext) {
                const stepSuccessMsgs = {
                    'step_a': '✓ Step 1: Personal & contact details saved successfully!',
                    'step_b': '✓ Step 2: Professional & social profiles saved successfully!',
                    'step_c': '✓ Step 3: Identity proofs & emergency contacts saved successfully!'
                };
                showToast(stepSuccessMsgs[stepId] || '✓ Details saved successfully!', 'success');

                const nextStep = (stepId === 'step_a') ? 'step_b' : ((stepId === 'step_b') ? 'step_c' : 'step_d');
                switchStep(nextStep);
            }
        } else {
            showAutoSaveStatus('Failed to save', 'var(--danger)');
            showToast((data && data.error) ? data.error : 'Failed to save details.', 'warning');
        }
    } catch (e) {
        console.error('Save step failed:', e);
        showAutoSaveStatus('Network error while saving', 'var(--danger)');
        showToast('Network error while saving changes. Please check your internet connection.', 'warning');
    }
}

async function submitFinalOnboarding() {
    const form = document.getElementById('form_step_d');
    if (!form) return;

    if (!document.getElementById('declaration_accepted').checked) {
        showToast('Please review and check the DPDP consent declaration to proceed.', 'warning');
        return;
    }

    const sigType = document.getElementById('signature_type').value;
    if (sigType === 'drawn' && (!canvas || document.getElementById('signature_data').value === '')) {
        saveCanvasData();
    }
    const sigData = document.getElementById('signature_data').value;
    if (!sigData || sigData.trim() === '') {
        showToast('Please provide your digital signature before final submission.', 'warning');
        return;
    }

    const btn = document.getElementById('btnSubmitFinal');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting Onboarding Application...';
    }

    const formData = new FormData(form);
    formData.append('signature_type', sigType);
    formData.append('signature_data', sigData);
    formData.append('_csrf_token', '<?= csrf_token() ?>');

    try {
        const res = await fetch('<?= parse_url(url("/dashboard/onboarding/submit"), PHP_URL_PATH) ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        let data = null;
        try {
            data = await res.json();
        } catch (jsonErr) {
            if (res.status === 401 || res.status === 419) {
                showToast('Your session has expired. Please refresh the page or log in again.', 'warning');
            } else {
                showToast('Submission error (' + res.status + '). Please try again.', 'warning');
            }
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span>Submit Onboarding &amp; Unlock Aptitude</span>';
            }
            return;
        }

        if (data && data.success) {
            showToast('✓ Onboarding application submitted successfully! Redirecting...', 'success');
            setTimeout(() => {
                window.location.href = data.redirect || '<?= url("/dashboard/aptitude") ?>';
            }, 800);
        } else {
            showToast('Submission Error: ' + ((data && data.error) ? data.error : 'Validation failed'), 'warning');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span>Submit Onboarding &amp; Unlock Aptitude</span>';
            }
        }
    } catch (e) {
        console.error('Final submit error:', e);
        showToast('Error connecting to submission service: ' + e.message, 'warning');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span>Submit Onboarding &amp; Unlock Aptitude</span>';
        }
    }
}

function showAutoSaveStatus(msg, color) {
    const el = document.getElementById('autoSaveIndicator');
    const txt = document.getElementById('autoSaveText');
    if (el && txt) {
        txt.innerText = msg;
        el.style.color = color;
    }
}

function updateProgressUI(pct) {
    const bar = document.getElementById('wizardProgressBar');
    const txt = document.getElementById('wizardProgressPct');
    if (bar) bar.style.width = Math.max(10, pct) + '%';
    if (txt) txt.innerText = pct + '%';
}
</script>

<?php
$content = ob_get_clean();
$pageTitle = "Onboarding Wizard & KYC | Realty Smartz Pathshala";
require APP_PATH . '/views/layouts/candidate.php';
?>
