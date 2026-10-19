<?php
$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
function isNavActive(string $path, string $currentUri): bool {
    if ($path === '/' && ($currentUri === '/' || $currentUri === '')) return true;
    if ($path !== '/' && str_starts_with($currentUri, $path)) return true;
    return false;
}
$currentUser = auth();
?>

<!-- ================================================================
     SITE NAVBAR (Blends into hero at top -> Centered pill on scroll)
     Mobile: Pill with logo + menu button, opens Bootstrap Offcanvas
     ================================================================ -->
<header class="site-header" id="siteHeader">
    <nav class="site-nav" aria-label="Main">
        <!-- Logo on the left (clearly readable, min 40px tall, never in a coloured pill) -->
        <a class="brand" href="<?= url('/') ?>" aria-label="Realty Smartz Pathshala Home">
            <img src="<?= asset('/assets/img/logo/Pathshala.webp') ?>" alt="Realty Smartz Pathshala" height="40">
        </a>

        <!-- Desktop Navigation Links (Home, Projects, About, Blog, Contact) -->
        <ul class="nav-links d-none d-lg-flex" role="menubar">
            <li role="none">
                <a href="<?= url('/') ?>" class="<?= isNavActive('/', $currentUri) ? 'active' : '' ?>" role="menuitem">
                    Home
                </a>
            </li>
            <li role="none">
                <a href="<?= url('/projects') ?>" class="<?= isNavActive('/projects', $currentUri) ? 'active' : '' ?>" role="menuitem">
                    Projects
                </a>
            </li>
            <li role="none">
                <a href="<?= url('/about') ?>" class="<?= isNavActive('/about', $currentUri) ? 'active' : '' ?>" role="menuitem">
                    About
                </a>
            </li>
            <li role="none">
                <a href="<?= url('/blog') ?>" class="<?= isNavActive('/blog', $currentUri) ? 'active' : '' ?>" role="menuitem">
                    Blog
                </a>
            </li>
            <li role="none">
                <a href="<?= url('/contact') ?>" class="<?= isNavActive('/contact', $currentUri) ? 'active' : '' ?>" role="menuitem">
                    Contact
                </a>
            </li>
        </ul>

        <!-- Right Side Actions: Adaptive Portal/Login + Mobile Menu Toggle -->
        <div class="nav-actions">
            <?php if (!empty($currentUser)): ?>
                <?php if ($currentUser['role'] === 'candidate'): ?>
                    <a href="<?= url('/dashboard') ?>" class="btn btn-primary btn-sm px-3" style="min-height: 36px; padding: 6px 14px; font-size: 13.5px; border-radius: 999px;">Portal</a>
                <?php else: ?>
                    <a href="<?= url('/admin/dashboard') ?>" class="btn btn-primary btn-sm px-3" style="min-height: 36px; padding: 6px 14px; font-size: 13.5px; border-radius: 999px;">Admin</a>
                <?php endif; ?>
                <a href="<?= url('/logout') ?>" class="btn btn-secondary btn-sm d-none d-sm-inline-flex" style="min-height: 36px; padding: 6px 14px; font-size: 13.5px; border-radius: 999px;" title="Sign Out">Sign Out</a>
            <?php else: ?>
                <a href="<?= url('/login') ?>" class="nav-login-link d-none d-sm-inline">
                    Log In
                </a>
                <a href="<?= url('/login') ?>" class="nav-login-link d-sm-none" style="font-size: 13.5px; font-weight: 600; padding: 4px 6px;">
                    Log In
                </a>
                <a href="<?= url('/register') ?>" class="btn btn-primary btn-sm d-none d-md-inline-flex" style="min-height: 36px; padding: 6px 16px; font-size: 13.5px; border-radius: 999px;">
                    Start Onboarding
                </a>
            <?php endif; ?>

            <!-- Dedicated Mobile Menu Toggle Button -->
            <button class="mobile-nav-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#siteMobileNav" aria-controls="siteMobileNav" aria-label="Toggle navigation">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
            </button>
        </div>
    </nav>
</header>

<!-- Bootstrap 5.3 Mobile Offcanvas Navigation -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="siteMobileNav" aria-labelledby="siteMobileNavLabel" style="max-width: 330px; border-top-left-radius: 16px; border-bottom-left-radius: 16px;">
    <div class="offcanvas-header border-bottom py-3 px-4">
        <a href="<?= url('/') ?>" class="d-flex align-items-center">
            <img src="<?= asset('/assets/img/logo/Pathshala.webp') ?>" alt="Realty Smartz Pathshala" height="36" id="siteMobileNavLabel">
        </a>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column justify-content-between p-4">
        <div class="d-flex flex-column gap-2">
            <?php if (!empty($currentUser)): ?>
                <div class="p-3 mb-2 bg-light border" style="border-radius: var(--r-md); border-color: var(--line) !important;">
                    <div class="fw-bold text-dark" style="font-size: 14.5px;"><?= e($currentUser['full_name']) ?></div>
                    <div class="text-muted" style="font-size: 12.5px;"><?= e($currentUser['email']) ?> &bull; <?= ucfirst($currentUser['role']) ?></div>
                </div>
            <?php endif; ?>
            <a href="<?= url('/') ?>" class="nav-link-mobile <?= isNavActive('/', $currentUri) ? 'active' : '' ?>">
                <span>Home</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="<?= url('/projects') ?>" class="nav-link-mobile <?= isNavActive('/projects', $currentUri) ? 'active' : '' ?>">
                <span>Projects</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="<?= url('/about') ?>" class="nav-link-mobile <?= isNavActive('/about', $currentUri) ? 'active' : '' ?>">
                <span>About</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="<?= url('/blog') ?>" class="nav-link-mobile <?= isNavActive('/blog', $currentUri) ? 'active' : '' ?>">
                <span>Blog</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="<?= url('/contact') ?>" class="nav-link-mobile <?= isNavActive('/contact', $currentUri) ? 'active' : '' ?>">
                <span>Contact</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
        <div class="pt-4 border-top d-flex flex-column gap-2" style="border-color: var(--line) !important;">
            <?php if (!empty($currentUser)): ?>
                <?php if ($currentUser['role'] === 'candidate'): ?>
                    <a href="<?= url('/dashboard') ?>" class="btn btn-primary w-100">
                        Candidate Portal
                    </a>
                    <a href="<?= url('/dashboard/onboarding') ?>" class="btn btn-secondary w-100">
                        Onboarding &amp; KYC
                    </a>
                <?php else: ?>
                    <a href="<?= url('/admin/dashboard') ?>" class="btn btn-primary w-100">
                        Admin Dashboard
                    </a>
                <?php endif; ?>
                <a href="<?= url('/logout') ?>" class="btn btn-outline-danger w-100">
                    Sign Out
                </a>
            <?php else: ?>
                <a href="<?= url('/register') ?>" class="btn btn-primary w-100">Start Onboarding</a>
                <a href="<?= url('/login') ?>" class="btn btn-secondary w-100">Log In</a>
            <?php endif; ?>
        </div>
    </div>
</div>
