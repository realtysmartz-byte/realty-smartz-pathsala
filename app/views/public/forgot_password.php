<?php
ob_start();
?>
<section class="hero section-pad d-flex align-items-center" style="min-height: 80vh;">
    <div class="container" style="max-width: 960px;">
        <div class="row g-4 align-items-stretch">
            <!-- Left: Form -->
            <div class="col-lg-6">
                <div class="p-4 p-md-5 bg-white border h-100 d-flex flex-column justify-content-between overflow-hidden" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important;">
                    <div>
                        <div class="eyebrow mb-2">Password Recovery</div>
                        <h1 class="h2 mb-2">Reset Password</h1>
                        <p class="small text-muted mb-4">Enter your registered email address and we will dispatch a secure password reset link.</p>

                        <form action="<?= url('/forgot-password') ?>" method="POST">
                            <?= csrf_field() ?>

                            <div class="mb-4">
                                <label class="form-label" for="reset_email">Registered Email Address</label>
                                <input type="email" id="reset_email" name="email" class="form-control" placeholder="name@example.com" required autofocus>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                Send Password Reset Link
                            </button>
                        </form>
                    </div>

                    <div class="pt-4 mt-4 border-top text-center small text-muted">
                        Remembered your password?<br>
                        <a href="<?= url('/login') ?>" class="text-primary fw-bold text-decoration-none mt-1 d-inline-block">
                            &larr; Back to Sign In
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right: Instructions (Desktop) -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="h-100 border overflow-hidden p-4 p-md-5 bg-white d-flex flex-column justify-content-between" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important;">
                    <div>
                        <h2 class="h3 mb-3">Security Guidelines</h2>
                        <p class="small text-muted mb-4">
                            Password reset instructions are dispatched exclusively to verified candidate and administrator email addresses.
                        </p>

                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="p-3 bg-light border" style="border-radius: var(--r-md) !important; border-color: var(--line) !important;">
                                <div class="small fw-bold text-dark mb-1">Time-Limited Link</div>
                                <div class="small text-muted">Reset links expire automatically after 30 minutes for security purposes.</div>
                            </div>

                            <div class="p-3 bg-light border" style="border-radius: var(--r-md) !important; border-color: var(--line) !important;">
                                <div class="small fw-bold text-dark mb-1">Need Immediate Assistance?</div>
                                <div class="small text-muted">If you no longer have access to your registered email, contact admissions at admissions@realtysmartzpathshala.in.</div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light border small text-muted" style="border-radius: var(--r-md) !important; border-color: var(--line) !important;">
                        Realty Smartz Pathshala &bull; Talent Assessment &amp; Onboarding
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$pageTitle = "Reset Password | Realty Smartz Pathshala";
require APP_PATH . '/views/layouts/main.php';
?>
