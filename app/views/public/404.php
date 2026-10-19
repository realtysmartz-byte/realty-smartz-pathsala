<?php
ob_start();
?>
<section class="hero section-pad d-flex align-items-center" style="min-height: 65vh;">
    <div class="container text-center">
        <div style="font-family: var(--font-family); font-size: 80px; font-weight: 300; color: var(--brand); line-height: 1; margin-bottom: 16px; letter-spacing: -0.03em;">
            404
        </div>
        <h1 class="h2 mb-2">Page Not Found</h1>
        <hr class="gold-rule gold-rule-center">
        <p class="text-muted mx-auto mb-4" style="max-width: 480px; font-size: 16px;">
            The page you requested could not be found or has been relocated. You can return to our homepage or get in touch with our team.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="<?= url('/') ?>" class="btn btn-primary">
                Return to Homepage
            </a>
            <a href="<?= url('/contact') ?>" class="btn btn-secondary">
                Contact Admissions
            </a>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$pageTitle = "404 Page Not Found | Realty Smartz Pathshala";
$pageDescription = "The requested page was not found.";
require APP_PATH . '/views/layouts/main.php';
?>
