<?php
ob_start();
?>
<section class="hero section-pad d-flex align-items-center" style="min-height: 85vh;">
    <div class="container" style="max-width: 1040px;">
        <div class="row g-4 align-items-stretch">
            <!-- Left: Registration Form -->
            <div class="col-lg-7">
                <div class="p-4 p-md-5 bg-white border h-100 d-flex flex-column justify-content-between overflow-hidden" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important;">
                    <div>
                        <div class="eyebrow mb-2">Step 1 of 5 &bull; Candidate Onboarding</div>
                        <h1 class="h2 mb-2">Start Your Registration</h1>
                        <p class="small text-muted mb-4">Create your candidate account to unlock the onboarding wizard and aptitude assessment.</p>

                        <form action="<?= url('/register') ?>" method="POST" id="registerForm">
                            <?= csrf_field() ?>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="reg_full_name">Full Legal Name *</label>
                                    <input type="text" id="reg_full_name" name="full_name" class="form-control" value="<?= e(old('full_name')) ?>" placeholder="e.g. Vikram Malhotra" required autocomplete="name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="reg_father_name">Father's Full Name *</label>
                                    <input type="text" id="reg_father_name" name="father_name" class="form-control" value="<?= e(old('father_name')) ?>" placeholder="e.g. Ramesh Malhotra" required autocomplete="off">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="reg_email">Email Address *</label>
                                    <input type="email" id="reg_email" name="email" class="form-control" value="<?= e(old('email')) ?>" placeholder="vikram@example.com" required autocomplete="email">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="reg_mobile">Mobile Number (10-digit) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted fw-bold">+91</span>
                                        <input type="tel" id="reg_mobile" name="mobile" class="form-control" value="<?= e(old('mobile')) ?>" placeholder="9876543210" maxlength="10" pattern="[6-9][0-9]{9}" required autocomplete="tel">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="registerPassword">Create Password *</label>
                                <input type="password" name="password" id="registerPassword" class="form-control" placeholder="Create a strong password" required autocomplete="new-password">

                                <!-- Password Strength Meter -->
                                <div class="mt-2">
                                    <div class="progress" style="height: 6px; background-color: var(--line);">
                                        <div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0%; transition: width 0.3s ease, background-color 0.3s ease;"></div>
                                    </div>
                                    <div id="strengthText" class="small text-muted mt-1 fw-bold" style="font-size: 12px;">
                                        Strength: Enter password
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="reg_password_confirmation">Confirm Password *</label>
                                <input type="password" id="reg_password_confirmation" name="password_confirmation" class="form-control" placeholder="Re-enter password" required autocomplete="new-password">
                                <div id="matchText" class="small mt-1 fw-bold" style="display: none; font-size: 12px;"></div>
                            </div>

                            <p class="small text-muted mb-4" style="font-size: 13px;">
                                By registering, you consent to identity verification and candidate evaluation by Realty Smartz Pathshala in compliance with Indian DPDP guidelines.
                            </p>

                            <button type="submit" id="btnSubmitReg" class="btn btn-primary btn-lg w-100">
                                Proceed to Email Verification
                            </button>
                        </form>
                    </div>

                    <div class="pt-4 mt-4 border-top text-center small text-muted">
                        Already registered?<br>
                        <a href="<?= url('/login') ?>" class="text-primary fw-bold text-decoration-none mt-1 d-inline-block">
                            Sign In to Your Portal &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right: Photo & Step Overview (Desktop) -->
            <div class="col-lg-5 d-none d-lg-block">
                <div class="h-100 border overflow-hidden position-relative d-flex flex-column justify-content-between p-4 bg-white" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important;">
                    <div class="overflow-hidden mb-4" style="border-radius: var(--r-md) !important; border: none !important; max-height: 240px;">
                        <img src="<?= asset('/assets/img/gallery/towers.jpg') ?>" alt="Realty Smartz High-Rise Developments" class="w-100 h-100 object-fit-cover" style="border: none !important;" loading="lazy">
                    </div>

                    <div class="p-3 bg-light border mb-0" style="border-radius: var(--r-md) !important; border-color: var(--line) !important;">
                        <div class="small fw-bold text-dark mb-2">5-Step Candidate Journey</div>
                        <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                            <li class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1">1</span>
                                <span class="fw-bold text-dark">Registration &amp; Account Setup</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white rounded-pill px-2 py-1">2</span>
                                <span>Email Verification (6-digit OTP)</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white rounded-pill px-2 py-1">3</span>
                                <span>KYC &amp; Profile Documentation</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white rounded-pill px-2 py-1">4</span>
                                <span>Real Estate Aptitude Assessment</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary text-white rounded-pill px-2 py-1">5</span>
                                <span>HR Review &amp; Placement Induction</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pwdInput = document.getElementById('registerPassword');
    const confirmInput = document.getElementById('reg_password_confirmation');
    const bar = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    const matchText = document.getElementById('matchText');

    function checkPassword() {
        const val = pwdInput.value;
        const hasLen = val.length >= 8;
        const hasUpper = /[A-Z]/.test(val);
        const hasLower = /[a-z]/.test(val);
        const hasNum = /[0-9]/.test(val);
        const hasSym = /[^A-Za-z0-9]/.test(val);

        let score = (hasLen ? 1 : 0) + (hasUpper ? 1 : 0) + (hasLower ? 1 : 0) + (hasNum ? 1 : 0) + (hasSym ? 1 : 0);

        if (val.length === 0) {
            bar.style.width = '0%';
            text.textContent = 'Strength: Enter password';
            text.style.color = 'var(--muted)';
        } else if (score <= 2) {
            bar.style.width = '25%';
            bar.style.backgroundColor = 'var(--danger)';
            text.textContent = 'Strength: Weak';
            text.style.color = 'var(--danger)';
        } else if (score === 3) {
            bar.style.width = '50%';
            bar.style.backgroundColor = 'var(--warning)';
            text.textContent = 'Strength: Fair';
            text.style.color = 'var(--warning)';
        } else if (score === 4) {
            bar.style.width = '75%';
            bar.style.backgroundColor = 'var(--primary)';
            text.textContent = 'Strength: Good';
            text.style.color = 'var(--primary)';
        } else {
            bar.style.width = '100%';
            bar.style.backgroundColor = 'var(--success)';
            text.textContent = 'Strength: Strong';
            text.style.color = 'var(--success)';
        }

        checkMatch();
    }

    function checkMatch() {
        if (!confirmInput || !confirmInput.value) {
            if (matchText) matchText.style.display = 'none';
            return;
        }
        matchText.style.display = 'block';
        if (pwdInput.value === confirmInput.value) {
            matchText.style.color = 'var(--success)';
            matchText.textContent = 'Passwords match';
        } else {
            matchText.style.color = 'var(--danger)';
            matchText.textContent = 'Passwords do not match';
        }
    }

    if (pwdInput) pwdInput.addEventListener('input', checkPassword);
    if (confirmInput) confirmInput.addEventListener('input', checkMatch);
});
</script>

<?php
$content = ob_get_clean();
$pageTitle = "Candidate Registration | Realty Smartz Pathshala";
$pageDescription = "Register for candidate onboarding and real estate talent placement at Realty Smartz Pathshala.";
require APP_PATH . '/views/layouts/main.php';
?>
