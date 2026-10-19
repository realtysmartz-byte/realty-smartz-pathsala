<?php
ob_start();
$user = auth() ?? [];
$cooldownUntil = $_SESSION['verify_cooldown_until'] ?? 0;
$remainingSeconds = max(0, $cooldownUntil - time());
?>
<section class="hero section-pad d-flex align-items-center" style="min-height: 80vh;">
    <div class="container" style="max-width: 960px;">
        <div class="row g-4 align-items-stretch">
            <!-- Left: Form -->
            <div class="col-lg-6">
                <div class="p-4 p-md-5 bg-white border h-100 d-flex flex-column justify-content-between overflow-hidden" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important;">
                    <div>
                        <div class="eyebrow mb-2">Step 2 of 5 &bull; Email Verification</div>
                        <h1 class="h2 mb-2">Verify Your Email</h1>
                        <p class="small text-muted mb-4">
                            We have sent a 6-digit verification code to:<br>
                            <strong class="text-dark"><?= e($user['email'] ?? 'your registered email') ?></strong>
                        </p>

                        <form action="<?= url('/verify-email') ?>" method="POST" class="mb-4">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label class="form-label text-center d-block small fw-bold" for="otp_input">
                                    Enter 6-Digit OTP Code
                                </label>
                                <input type="text" id="otp_input" name="otp" maxlength="6" pattern="[0-9]{6}" inputmode="numeric"
                                       class="form-control text-center fs-3 fw-bold letter-spacing-lg text-primary"
                                       style="letter-spacing: 8px; font-family: monospace;"
                                       placeholder="••••••" autocomplete="one-time-code" autofocus required>
                            </div>

                            <p class="small text-muted text-center mb-4" style="font-size: 13px;">
                                Code is valid for 10 minutes (maximum 5 attempts allowed).
                            </p>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                Verify &amp; Unlock Portal
                            </button>
                        </form>
                    </div>

                    <!-- Resend Section with Countdown -->
                    <div class="pt-4 mt-4 border-top text-center small text-muted">
                        Didn't receive the email? Check spam or
                        <form action="<?= url('/resend-otp') ?>" method="POST" id="resendOtpForm" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" id="resendOtpBtn" class="btn btn-link p-0 text-primary fw-bold text-decoration-none small align-baseline" <?= $remainingSeconds > 0 ? 'disabled' : '' ?>>
                                Resend OTP
                            </button>
                        </form>
                        <span id="countdownTimer" style="display: <?= $remainingSeconds > 0 ? 'inline' : 'none' ?>;" class="text-primary fw-bold">
                            (in <span id="secsSpan"><?= $remainingSeconds ?></span>s)
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right: Instructions / Security Note (Desktop) -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="h-100 border overflow-hidden p-4 p-md-5 bg-white d-flex flex-column justify-content-between" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important;">
                    <div>
                        <h2 class="h3 mb-3">Security &amp; Verification</h2>
                        <p class="small text-muted mb-4">
                            To protect your candidate profile and ensure compliant onboarding, your email address must be verified before proceeding to documentation.
                        </p>

                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="p-3 bg-light border" style="border-radius: var(--r-md) !important; border-color: var(--line) !important;">
                                <div class="small fw-bold text-dark mb-1">Check Your Spam or Promotions Folder</div>
                                <div class="small text-muted">Verification emails usually arrive within 30 seconds. If not in your inbox, please verify your spam or junk directory.</div>
                            </div>

                            <div class="p-3 bg-light border" style="border-radius: var(--r-md) !important; border-color: var(--line) !important;">
                                <div class="small fw-bold text-dark mb-1">Next Step: KYC Profile</div>
                                <div class="small text-muted">Once verified, you will be redirected to the candidate onboarding wizard to submit your basic details and identification.</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light border small text-muted" style="border-radius: var(--r-md) !important; border-color: var(--line) !important;">
                        Need assistance? Email admissions at <strong class="text-dark">admissions@realtysmartzpathshala.in</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let secondsLeft = <?= $remainingSeconds ?>;
    const btn = document.getElementById('resendOtpBtn');
    const timer = document.getElementById('countdownTimer');
    const span = document.getElementById('secsSpan');

    if (secondsLeft > 0) {
        const interval = setInterval(function () {
            secondsLeft--;
            if (span) span.innerText = secondsLeft;
            if (secondsLeft <= 0) {
                clearInterval(interval);
                if (btn) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                }
                if (timer) timer.style.display = 'none';
            }
        }, 1000);
    }
});
</script>

<?php
$content = ob_get_clean();
$pageTitle = "Verify Email Address | Realty Smartz Pathshala";
require APP_PATH . '/views/layouts/main.php';
?>
