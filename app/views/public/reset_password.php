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
                        <h1 class="h2 mb-2">Choose New Password</h1>
                        <p class="small text-muted mb-4">Please create a secure password for your account (minimum 8 characters).</p>

                        <form action="<?= url('/reset-password') ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="token" value="<?= e($_GET['token'] ?? '') ?>">

                            <div class="mb-3">
                                <label class="form-label" for="new_password">New Password *</label>
                                <input type="password" id="new_password" name="password" class="form-control" placeholder="At least 8 characters" required minlength="8" autofocus>
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="new_password_conf">Confirm New Password *</label>
                                <input type="password" id="new_password_conf" name="password_confirmation" class="form-control" placeholder="Re-enter new password" required minlength="8">
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                Update Password &amp; Sign In
                            </button>
                        </form>
                    </div>

                    <div class="pt-4 mt-4 border-top text-center small text-muted">
                        <a href="<?= url('/login') ?>" class="text-primary fw-bold text-decoration-none">
                            &larr; Back to Sign In
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right: Instructions (Desktop) -->
            <div class="col-lg-6 d-none d-lg-block">
                <div class="h-100 border overflow-hidden p-4 p-md-5 bg-white d-flex flex-column justify-content-between" style="border-radius: var(--r-lg) !important; border-color: var(--line) !important;">
                    <div>
                        <h2 class="h3 mb-3">Password Best Practices</h2>
                        <p class="small text-muted mb-4">
                            Ensure your new password meets the security requirements for accessing your onboarding assessment portal.
                        </p>

                        <div class="d-flex flex-column gap-3 mb-4">
                            <div class="p-3 bg-light border" style="border-radius: var(--r-md) !important; border-color: var(--line) !important;">
                                <div class="small fw-bold text-dark mb-1">Recommended Requirements</div>
                                <div class="small text-muted">Include uppercase letters, lowercase letters, numbers, and symbols to ensure optimal account protection.</div>
                            </div>

                            <div class="p-3 bg-light border" style="border-radius: var(--r-md) !important; border-color: var(--line) !important;">
                                <div class="small fw-bold text-dark mb-1">Single Session Active</div>
                                <div class="small text-muted">Updating your password will reset existing authentication sessions across other devices.</div>
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
$pageTitle = "Set New Password | Realty Smartz Pathshala";
require APP_PATH . '/views/layouts/main.php';
?>
