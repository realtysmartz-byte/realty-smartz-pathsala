<?php
ob_start();

$profile = $candidate['profile'] ?? [];
$completion = (int)($profile['completion_percentage'] ?? 0);
$status = $candidate['status'] ?? 'registered';
$docs = $candidate['docs_by_type'] ?? [];
$social = $profile['social'] ?? [];
$attempt = $candidate['test_attempt'] ?? null;

// Determine state for 4 steps: Registered, Email Verified, Onboarding Submitted, Aptitude Test
// States: 'completed', 'current', 'pending'
$isEmailVerified = !empty($candidate['email_verified_at']);
$isOnboardingSubmitted = !empty($profile['declaration_accepted']) || !empty($profile['is_locked']) || (int)($profile['completion_percentage'] ?? 0) >= 100 || in_array($status, ['documents_submitted', 'profile_completed', 'test_pending', 'test_completed'], true);
$isTestCompleted = !empty($attempt) && in_array($attempt['status'] ?? '', ['completed', 'passed', 'failed', 'submitted'], true);

// Step 1: Registered
$step1State = 'completed';

// Step 2: Email Verified
if ($isEmailVerified) {
    $step2State = 'completed';
} else {
    $step2State = 'current';
}

// Step 3: Onboarding Submitted
if ($isOnboardingSubmitted) {
    $step3State = 'completed';
} elseif ($isEmailVerified) {
    $step3State = 'current';
} else {
    $step3State = 'pending';
}

// Step 4: Aptitude Test
if ($isTestCompleted) {
    $step4State = 'completed';
} elseif ($isOnboardingSubmitted) {
    $step4State = 'current';
} else {
    $step4State = 'pending';
}

$attemptPercent = 0;
$attemptScore = 0;
$attemptTotal = 15;
$attemptPassed = false;
if (!empty($attempt)) {
    $attemptPercent = isset($attempt['percentage']) ? (float)$attempt['percentage'] : (isset($attempt['score_percentage']) ? (float)$attempt['score_percentage'] : round(((float)($attempt['score'] ?? 0) / max(1, (int)($attempt['total_questions'] ?? 15))) * 100));
    $attemptScore = (int)($attempt['score'] ?? ($attempt['correct_answers'] ?? 0));
    $attemptTotal = (int)($attempt['total_questions'] ?? 15);
    $attemptPassed = !empty($attempt['passed']) || $attemptPercent >= 50;
}

$mobileNum = $candidate['mobile'] ?? '';
$whatsappNum = $profile['whatsapp_number'] ?? ($profile['whatsapp'] ?? $mobileNum);
$isPhoneSame = empty($whatsappNum) || ($whatsappNum === $mobileNum);

$aadhaarLast4 = $profile['aadhaar_last4'] ?? '1234';
$panLast4 = $profile['pan_last4'] ?? '5678';
?>

<!-- Page Title (DM Sans H1 above content) -->
<div class="mb-4">
    <span class="eyebrow">Candidate Portal</span>
    <h1 class="h2 mb-2">Your application</h1>
    <hr class="gold-rule">
</div>

<!-- Progress Timeline: 4 Steps (Registered, Email Verified, Onboarding Submitted, Aptitude Test) -->
<div class="p-4 bg-white border mb-4" style="border-radius: var(--r-lg); border-color: var(--line) !important;">
    <h2 class="h5 mb-3">Application Stages</h2>

    <!-- Desktop Timeline (Horizontal) -->
    <div class="candidate-timeline-desktop">
        <!-- 1. Registered -->
        <div class="candidate-step-col <?= $step1State ?>">
            <div class="candidate-step-circle">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="candidate-step-label">Registered</div>
        </div>

        <!-- 2. Email Verified -->
        <div class="candidate-step-col <?= $step2State ?>">
            <div class="candidate-step-circle">
                <?php if ($step2State === 'completed'): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <?php else: ?>
                    2
                <?php endif; ?>
            </div>
            <div class="candidate-step-label">Email Verified</div>
        </div>

        <!-- 3. Onboarding Submitted -->
        <div class="candidate-step-col <?= $step3State ?>">
            <div class="candidate-step-circle">
                <?php if ($step3State === 'completed'): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <?php else: ?>
                    3
                <?php endif; ?>
            </div>
            <div class="candidate-step-label">Onboarding Submitted</div>
        </div>

        <!-- 4. Aptitude Test (with score on completion) -->
        <div class="candidate-step-col <?= $step4State ?>">
            <div class="candidate-step-circle">
                <?php if ($step4State === 'completed'): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <?php else: ?>
                    4
                <?php endif; ?>
            </div>
            <div class="candidate-step-label">
                Aptitude Test
                <?php if ($isTestCompleted): ?>
                    <div style="font-size: 11.5px; color: var(--brand); font-weight: 600; margin-top: 2px;">
                        <?= $attemptScore ?>/<?= $attemptTotal ?> (<?= round($attemptPercent) ?>%)
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Mobile Timeline (Vertical) -->
    <div class="candidate-timeline-mobile">
        <!-- 1. Registered -->
        <div class="mobile-step-row <?= $step1State ?>">
            <div class="mobile-step-circle">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="mobile-step-content">
                <div class="mobile-step-title">Registered</div>
                <div class="mobile-step-date"><?= date('d M Y', strtotime($candidate['created_at'])) ?></div>
            </div>
        </div>

        <!-- 2. Email Verified -->
        <div class="mobile-step-row <?= $step2State ?>">
            <div class="mobile-step-circle">
                <?php if ($step2State === 'completed'): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <?php else: ?>
                    2
                <?php endif; ?>
            </div>
            <div class="mobile-step-content">
                <div class="mobile-step-title">Email Verified</div>
                <div class="mobile-step-date"><?= $isEmailVerified ? 'OTP Verified' : 'Pending Verification' ?></div>
            </div>
        </div>

        <!-- 3. Onboarding Submitted -->
        <div class="mobile-step-row <?= $step3State ?>">
            <div class="mobile-step-circle">
                <?php if ($step3State === 'completed'): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <?php else: ?>
                    3
                <?php endif; ?>
            </div>
            <div class="mobile-step-content">
                <div class="mobile-step-title">Onboarding Submitted</div>
                <div class="mobile-step-date"><?= $isOnboardingSubmitted ? 'KYC &amp; Profile Submitted' : 'Pending' ?></div>
            </div>
        </div>

        <!-- 4. Aptitude Test (with score on completion) -->
        <div class="mobile-step-row <?= $step4State ?>">
            <div class="mobile-step-circle">
                <?php if ($step4State === 'completed'): ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                <?php else: ?>
                    4
                <?php endif; ?>
            </div>
            <div class="mobile-step-content">
                <div class="mobile-step-title">Aptitude Test</div>
                <div class="mobile-step-date">
                    <?php if ($isTestCompleted): ?>
                        Score: <?= $attemptScore ?>/<?= $attemptTotal ?> (<?= round($attemptPercent) ?>%) &bull; <?= $attemptPassed ? 'Passed' : 'Not passed' ?>
                    <?php else: ?>
                        Pending Test
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Details Panel -->
<div class="p-4 bg-white border mb-4" style="border-radius: var(--r-lg); border-color: var(--line) !important;">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="h5 mb-0">Your details</h2>
        <a href="<?= url('/dashboard/onboarding') ?>" class="btn btn-secondary btn-sm">
            View Onboarding
        </a>
    </div>

    <!-- Definition List: 2-column desktop / 1-column mobile -->
    <dl class="details-dl">
        <div>
            <dt>Full Name</dt>
            <dd><?= e($candidate['full_name']) ?></dd>
        </div>

        <div>
            <dt>Father's Name</dt>
            <dd><?= e($profile['father_name'] ?? ($candidate['father_name'] ?? 'Not provided')) ?></dd>
        </div>

        <div>
            <dt>Email Address</dt>
            <dd style="overflow-wrap: anywhere;"><?= e($candidate['email']) ?></dd>
        </div>

        <?php if ($isPhoneSame): ?>
            <div>
                <dt>Mobile / WhatsApp</dt>
                <dd class="nowrap">+91 <?= e($mobileNum) ?></dd>
            </div>
        <?php else: ?>
            <div>
                <dt>Mobile Number</dt>
                <dd class="nowrap">+91 <?= e($mobileNum) ?></dd>
            </div>
            <div>
                <dt>WhatsApp Number</dt>
                <dd class="nowrap">+91 <?= e($whatsappNum) ?></dd>
            </div>
        <?php endif; ?>

        <div>
            <dt>Aadhaar</dt>
            <dd class="nowrap d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <span>XXXX XXXX <?= e($aadhaarLast4) ?></span>
            </dd>
        </div>

        <div>
            <dt>PAN</dt>
            <dd class="nowrap d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <span>XXXXX<?= e($panLast4) ?>X</span>
            </dd>
        </div>

        <div style="grid-column: 1 / -1;">
            <dt>Current Address</dt>
            <dd><?= e($profile['address'] ?? ($profile['current_address'] ?? 'Not specified')) ?><?= !empty($profile['city']) ? ', ' . e($profile['city']) : '' ?><?= !empty($profile['state']) ? ', ' . e($profile['state']) : '' ?><?= !empty($profile['pincode']) ? ' ' . e($profile['pincode']) : '' ?></dd>
        </div>
    </dl>
</div>

<!-- Uploaded Documents Card -->
<div class="p-4 bg-white border mb-4" style="border-radius: var(--r-lg); border-color: var(--line) !important;">
    <h2 class="h5 mb-3">Submitted Documents</h2>
    <div class="d-flex flex-column gap-2">
        <?php
        $docTypesList = [
            'aadhaar_front' => ['Aadhaar Card (Front)', !empty($docs['aadhaar_front']) || !empty($docs['aadhaar'])],
            'pan_card'      => ['PAN Card', !empty($docs['pan_card'])],
            'profile_photo' => ['Passport Photograph', !empty($docs['profile_photo'])],
            'marksheet'     => ['Highest Degree / Marksheet', !empty($docs['marksheet'])],
            'offer_letter'  => ['Offer Letter (if applicable)', !empty($docs['offer_letter'])],
            'cancelled_cheque' => ['Cancelled Cheque / Passbook', !empty($docs['cancelled_cheque'])]
        ];
        foreach ($docTypesList as $typeKey => [$typeTitle, $isUploaded]):
        ?>
            <div class="d-flex justify-content-between align-items-center p-3 border bg-light small" style="border-radius: var(--r-md); border-color: var(--line) !important; min-height: 48px;">
                <span class="fw-bold text-dark"><?= e($typeTitle) ?></span>
                <?php if ($isUploaded): ?>
                    <?php $foundDoc = $docs[$typeKey] ?? ($docs['aadhaar'] ?? null); ?>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge" style="background: var(--brand-soft); color: var(--brand);">Uploaded</span>
                        <?php if ($foundDoc): ?>
                            <a href="<?= url('/document/download/' . $foundDoc['id']) ?>" target="_blank" class="small fw-bold" style="color: var(--brand); text-decoration: none;">View</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <a href="<?= url('/dashboard/onboarding?step=step_c') ?>" class="small fw-bold" style="color: var(--brand); text-decoration: none;">Upload &rarr;</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Aptitude Assessment Result -->
<?php if ($attempt && in_array($attempt['status'] ?? '', ['completed', 'passed', 'failed', 'submitted'], true)): ?>
    <div class="p-4 bg-white border mb-4" style="border-radius: var(--r-lg); border-color: var(--line) !important;">
        <h2 class="h5 mb-3">Aptitude Assessment Result</h2>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-3 bg-light border" style="border-radius: var(--r-md); border-color: var(--line) !important;">
            <div>
                <div class="h3 mb-0" style="color: <?= $attemptPassed ? 'var(--brand)' : 'var(--danger)' ?>;">
                    <?= (int)$attemptPercent ?>%
                </div>
                <div class="small fw-bold text-dark mt-1">
                    Score: <?= $attemptScore ?> / <?= $attemptTotal ?> &bull; <?= $attemptPassed ? 'Passed' : 'Not passed' ?>
                </div>
            </div>
            <?php if (!empty($attempt['id'])): ?>
                <a href="<?= url('/dashboard/test/scorecard/' . $attempt['id']) ?>" target="_blank" class="btn btn-secondary btn-sm">
                    Download Scorecard PDF
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
$pageTitle = "Your Application | Realty Smartz Pathshala";
require APP_PATH . '/views/layouts/candidate.php';
?>
