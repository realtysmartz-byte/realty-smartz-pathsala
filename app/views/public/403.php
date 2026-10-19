<?php
ob_start();
?>
<section class="hero section-pad d-flex align-items-center" style="min-height: 65vh;">
    <div class="container text-center">
        <div style="font-family: var(--font-family); font-size: 80px; font-weight: 300; color: var(--brand); line-height: 1; margin-bottom: 16px; letter-spacing: -0.03em;">
            403
        </div>
        <h1 class="h2 mb-2">Access Restricted</h1>
        <hr class="gold-rule gold-rule-center">
        <p class="text-muted mx-auto mb-4" style="max-width: 480px; font-size: 16px;">
            You do not have permission to access this area. Please sign in with appropriate credentials or return to the main portal.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= url('/') ?>" class="btn btn-primary">
                Return to Homepage
            </a>
            <a href="<?= url('/login') ?>" class="btn btn-secondary">
                Candidate Login
            </a>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$pageTitle = "403 Access Restricted | Realty Smartz Pathshala";
$pageDescription = "Access is restricted to authorized personnel only.";
require APP_PATH . '/views/layouts/main.php';
?>
