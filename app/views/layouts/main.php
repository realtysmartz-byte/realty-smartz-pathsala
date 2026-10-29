<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Realty Smartz Pathshala | Master the Art of Real Estate') ?></title>
    <meta name="description" content="<?= e($pageDescription ?? 'India\'s #1 Real Estate Training & Hiring Platform in Gurgaon. Master real estate sales, documentation, CRM and closing.') ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= e($pageTitle ?? 'Realty Smartz Pathshala') ?>">
    <meta property="og:description" content="<?= e($pageDescription ?? 'Master the Art of Real Estate with Realty Smartz Pathshala.') ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= url($_SERVER['REQUEST_URI'] ?? '/') ?>">
    <meta property="og:image" content="<?= asset('/assets/img/logo/Pathshala.webp') ?>">

    <link rel="canonical" href="<?= url($_SERVER['REQUEST_URI'] ?? '/') ?>">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?= asset('/assets/img/favicon.png') ?>">

    <!-- Master Design Tokens -->
    <link rel="stylesheet" href="<?= asset('/assets/css/tokens.css') ?>">

    <!-- Self-Hosted DM Sans Variable Font Preload (Latin subset with opsz + wght axes) -->
    <link rel="preload" href="<?= asset('/assets/fonts/dm-sans-latin.woff2') ?>" as="font" type="font/woff2" crossorigin>

    <!-- Application Master Stylesheet (Hashed Dist via manifest.json) -->
    <link rel="stylesheet" href="<?= asset('/assets/css/style.css') ?>">

    <!-- Interactive Motion & Mobile Shield -->
    <link rel="stylesheet" href="<?= asset('/assets/css/interactive.css') ?>">
</head>
<body>
    <!-- Navbar (Transforming Pill) -->
    <?php require APP_PATH . '/views/partials/navbar.php'; ?>

    <!-- Main Page Content -->
    <main>
        <?= $content ?? '' ?>
    </main>

    <!-- Master Footer -->
    <?php require APP_PATH . '/views/partials/footer.php'; ?>

    <!-- Master Scripts (Bootstrap 5.3 + Motion) -->
    <script src="<?= asset('/assets/js/main.js') ?>" defer></script>
    <script src="<?= asset('/assets/js/interactive.js') ?>" defer></script>

    <!-- Flash Notifications Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1090;">
        <?php
        $successFlash = flash('success');
        $errorFlash = flash('error');
        $warningFlash = flash('warning');
        ?>
        <?php if (!empty($successFlash)): ?>
            <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body"><?= e($successFlash) ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($errorFlash)): ?>
            <div class="toast align-items-center text-bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body"><?= e($errorFlash) ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($warningFlash)): ?>
            <div class="toast align-items-center text-bg-warning border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body"><?= e($warningFlash) ?></div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
