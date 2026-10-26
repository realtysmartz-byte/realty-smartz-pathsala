<?php
$adminUser = auth();
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/admin/dashboard', PHP_URL_PATH);
$pageTitle = $pageTitle ?? 'Dashboard';
$viewTitle = $viewTitle ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= asset('assets/img/favicon.png') ?>">

    <!-- Master Design Tokens -->
    <link rel="stylesheet" href="<?= asset('/assets/css/tokens.css') ?>">
    <link rel="stylesheet" href="<?= asset('/assets/css/style.css') ?>">

    <!-- Preload Self-Hosted DM Sans Variable Font -->
    <link rel="preload" href="<?= asset('/assets/fonts/dm-sans-latin.woff2') ?>" as="font" type="font/woff2" crossorigin>

    <style>
        @font-face {
            font-family: 'DM Sans';
            font-style: normal;
            font-weight: 100 1000;
            font-display: swap;
            src: url('<?= asset("/assets/fonts/dm-sans-latin.woff2") ?>') format('woff2');
            unicode-range: U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;
        }

        :root {
            --bg: #F7F5EF;
            --surface: #FFFFFF;
            --surface-2: #EFEBE0;
            --line: #E2DDD0;
            --border: #E2DDD0;
            --text: #26332D;
            --muted: #6D766F;
            --brand: #1F5C4A;
            --brand-hover: #174839;
            --brand-soft: #E2EEE8;
            --danger: #C0392B;
            --danger-soft: #FDE8E8;
            --success: #2E7D5B;
            --success-soft: #E2EEE8;
            --font-family: 'DM Sans', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            --r-sm: 8px;
            --r-md: 12px;
            --r-lg: 20px;
            --r-full: 999px;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            font-family: var(--font-family);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: var(--brand);
            text-decoration: none;
        }

        button, input, select, textarea {
            font-family: inherit;
        }

        /* SVG icon sizing & alignment */
        .svg-icon {
            display: inline-block;
            vertical-align: middle;
            flex-shrink: 0;
        }

        /* -------------------------------------------------------------
           2. ADMIN SHELL: STRICT SPEC GRID
           ------------------------------------------------------------- */
        .app-shell {
            display: grid;
            grid-template-columns: 240px minmax(0, 1fr);
            min-height: 100vh;
            background: var(--bg);
        }

        /* Sticky Left Sidebar */
        .sidebar {
            position: sticky;
            top: 0;
            align-self: start;
            height: 100vh;
            overflow-y: auto;
            background: var(--surface);
            border-right: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            z-index: 40;
        }

        .sidebar-header {
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            border-bottom: 1px solid var(--line);
            flex-shrink: 0;
        }

        .sidebar-logo {
            height: 36px;
            width: auto;
            display: block;
        }

        .sidebar-close-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--muted);
            cursor: pointer;
            padding: 6px;
            border-radius: var(--r-sm);
        }

        .sidebar-close-btn:hover {
            color: var(--text);
            background: var(--surface-2);
        }

        /* Navigation List */
        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        /* Sidebar Item: icon (20px) + label (15px), height 44px, radius --r-md; active = --brand-soft background + --brand text */
        .sidebar-item {
            height: 44px;
            padding: 0 14px;
            border-radius: var(--r-md);
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--muted);
            font-size: 15px;
            font-weight: 500;
            transition: background 0.15s ease, color 0.15s ease;
            white-space: nowrap;
            position: relative;
        }

        .sidebar-item svg {
            width: 20px;
            height: 20px;
            stroke-width: 2;
            color: var(--muted);
            flex-shrink: 0;
        }

        .sidebar-item:hover {
            background: var(--surface-2);
            color: var(--text);
        }

        .sidebar-item:hover svg {
            color: var(--text);
        }

        .sidebar-item.active {
            background: var(--brand-soft);
            color: var(--brand);
            font-weight: 600;
        }

        .sidebar-item.active svg {
            color: var(--brand);
        }

        /* Footer pinned to the bottom */
        .sidebar .sidebar-footer {
            margin-top: auto;
            padding: 14px 16px;
            border-top: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: var(--surface);
            flex-shrink: 0;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: var(--r-full);
            background: var(--brand-soft);
            color: var(--brand);
            font-size: 14px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 110px;
        }

        .logout-link {
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
            text-decoration: none;
            white-space: nowrap;
            flex-shrink: 0;
            padding: 6px 8px;
            border-radius: var(--r-sm);
            transition: color 0.15s ease;
        }

        .logout-link:hover {
            color: var(--danger);
            background: var(--danger-soft);
        }

        /* Main Content Column */
        .main {
            min-width: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Topbar: sticky, height 64px, padding 0 32px, full width of content column */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            background: var(--surface);
            border-bottom: 1px solid var(--line);
            gap: 16px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .topbar-menu-btn {
            display: none;
            background: transparent;
            border: none;
            color: var(--text);
            cursor: pointer;
            padding: 8px;
            border-radius: var(--r-md);
            align-items: center;
            justify-content: center;
        }

        .topbar-menu-btn:hover {
            background: var(--surface-2);
        }

        .topbar-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-shrink: 0;
        }

        .topbar-search-form {
            position: relative;
            width: 280px;
            max-width: 320px;
        }

        .topbar-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .topbar-search-input {
            width: 100%;
            height: 40px;
            border-radius: var(--r-md);
            border: 1px solid var(--line);
            background: var(--bg);
            padding: 0 14px 0 38px;
            font-size: 14px;
            color: var(--text);
            outline: none;
            transition: border-color 0.15s ease, background 0.15s ease;
        }

        .topbar-search-input:focus {
            background: var(--surface);
            border-color: var(--brand);
        }

        .topbar-search-input::placeholder {
            color: var(--muted);
        }

        /* Page Container */
        .page {
            padding: 32px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            flex: 1;
        }

        /* Mobile Backdrop */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(38, 51, 45, 0.4);
            z-index: 35;
            backdrop-filter: blur(2px);
        }

        /* -------------------------------------------------------------
           TABLET BREAKPOINT: Under 1024px (72px sticky icon rail)
           ------------------------------------------------------------- */
        @media (max-width: 1023.98px) and (min-width: 768px) {
            .app-shell {
                grid-template-columns: 72px minmax(0, 1fr);
            }

            .sidebar-header {
                padding: 0;
                justify-content: center;
            }

            .sidebar-logo {
                height: 28px;
            }

            .sidebar-nav {
                padding: 16px 8px;
                align-items: center;
            }

            .sidebar-item {
                width: 44px;
                height: 44px;
                padding: 0;
                justify-content: center;
            }

            .sidebar-item span {
                display: none;
            }

            /* Tooltip on tablet hover */
            .sidebar-item::after {
                content: attr(data-tooltip);
                position: absolute;
                left: 100%;
                top: 50%;
                transform: translateY(-50%);
                margin-left: 10px;
                background: var(--text);
                color: var(--surface);
                font-size: 12px;
                font-weight: 500;
                padding: 6px 10px;
                border-radius: var(--r-sm);
                white-space: nowrap;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.15s ease;
                z-index: 100;
                box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            }

            .sidebar-item:hover::after {
                opacity: 1;
            }

            .sidebar .sidebar-footer {
                padding: 12px 6px;
                flex-direction: column;
                justify-content: center;
                gap: 10px;
            }

            .user-name {
                display: none;
            }

            .logout-label {
                display: none;
            }

            .logout-link {
                padding: 6px;
            }

            .topbar {
                padding: 0 24px;
            }

            .page {
                padding: 24px;
            }
        }

        /* -------------------------------------------------------------
           MOBILE BREAKPOINT: Under 768px (Bootstrap-style offcanvas)
           ------------------------------------------------------------- */
        @media (max-width: 767.98px) {
            .app-shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: -280px;
                width: 280px;
                height: 100vh;
                height: 100dvh;
                z-index: 50;
                transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: none;
            }

            .sidebar.open {
                transform: translateX(280px);
                box-shadow: 0 0 24px rgba(38, 51, 45, 0.2);
            }

            .sidebar-close-btn {
                display: flex;
            }

            .sidebar-backdrop.active {
                display: block;
            }

            .topbar {
                padding: 0 16px;
            }

            .topbar-menu-btn {
                display: flex;
            }

            .topbar-search-form {
                width: 170px;
            }

            .topbar-search-input {
                height: 36px;
                font-size: 13px;
                padding-left: 32px;
            }

            .topbar-search-icon svg {
                width: 14px;
                height: 14px;
            }

            .page {
                padding: 16px;
            }
        }

        /* Ensure all admin modals and child interactive elements have pointer-events auto */
        .modal-overlay {
            pointer-events: auto !important;
        }
        .modal-dialog {
            pointer-events: auto !important;
        }
        .modal-dialog * {
            pointer-events: auto;
        }
    </style>
</head>
<body>

<!-- 1. Master Inline SVG Sprite (loaded once) -->
<?= render_svg_sprite() ?>

<!-- 2. Main App Shell -->
<div class="app-shell">

    <!-- Mobile Offcanvas Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleAdminSidebar(false)"></div>

    <!-- Sticky / Collapsible Sidebar -->
    <aside class="sidebar" id="adminSidebar" aria-label="Admin Navigation">
        <!-- Top Logo -->
        <div class="sidebar-header">
            <a href="<?= url('/') ?>" aria-label="Realty Smartz Pathshala Homepage" style="display:flex; align-items:center;">
                <img src="<?= asset('/assets/img/logo/Pathshala.webp') ?>" alt="Pathshala" class="sidebar-logo">
            </a>
            <button type="button" class="sidebar-close-btn" aria-label="Close navigation menu" onclick="toggleAdminSidebar(false)">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-x"></use></svg>
            </button>
        </div>

        <!-- 9 Core Sidebar Items:
             Dashboard, Candidates, Documents, Assessments, Enquiries, Blog, Projects, Site Content, Settings -->
        <nav class="sidebar-nav">
            <a href="<?= url('/admin/dashboard') ?>" 
               class="sidebar-item <?= ($currentPath === '/admin' || $currentPath === '/admin/dashboard') ? 'active' : '' ?>" 
               data-tooltip="Dashboard" 
               aria-label="Dashboard">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-dashboard"></use></svg>
                <span>Dashboard</span>
            </a>

            <a href="<?= url('/admin/candidates') ?>" 
               class="sidebar-item <?= ($currentPath === '/admin/candidates' || str_starts_with($currentPath, '/admin/candidates/')) ? 'active' : '' ?>" 
               data-tooltip="Candidates" 
               aria-label="Candidates">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-users"></use></svg>
                <span>Candidates</span>
            </a>

            <a href="<?= url('/admin/interview-candidates') ?>" 
               class="sidebar-item <?= str_starts_with($currentPath, '/admin/interview-candidates') ? 'active' : '' ?>" 
               data-tooltip="Interview Candidates" 
               aria-label="Interview Candidates">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-file-text"></use></svg>
                <span>Interview Candidates</span>
                <?php 
                $unopenedCount = \App\Models\InterviewApplication::getUnopenedCount();
                ?>
                <span class="badge sidebar-count-badge" id="interviewSidebarBadge" style="margin-left:auto; background:var(--brand); color:#fff; border-radius:var(--r-full); font-size:11px; padding:2px 7px; font-weight:600; <?= ($unopenedCount > 0) ? '' : 'display:none;' ?>">
                    <?= $unopenedCount ?>
                </span>
            </a>

            <a href="<?= url('/admin/aptitude-candidates') ?>" 
               class="sidebar-item <?= str_starts_with($currentPath, '/admin/aptitude-candidates') ? 'active' : '' ?>" 
               data-tooltip="Aptitude Candidates" 
               aria-label="Aptitude Candidates">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-user-check"></use></svg>
                <span>Aptitude Candidates</span>
            </a>

            <a href="<?= url('/admin/qr-codes') ?>" 
               class="sidebar-item <?= str_starts_with($currentPath, '/admin/qr-codes') ? 'active' : '' ?>" 
               data-tooltip="QR Codes" 
               aria-label="QR Codes">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-qr-code"></use></svg>
                <span>QR Codes</span>
            </a>

            <a href="<?= url('/admin/documents') ?>" 
               class="sidebar-item <?= str_starts_with($currentPath, '/admin/documents') ? 'active' : '' ?>" 
               data-tooltip="Documents" 
               aria-label="Documents">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-folder"></use></svg>
                <span>Documents</span>
            </a>

            <a href="<?= url('/admin/assessments') ?>" 
               class="sidebar-item <?= (str_starts_with($currentPath, '/admin/assessments') || str_starts_with($currentPath, '/admin/questions')) ? 'active' : '' ?>" 
               data-tooltip="Assessments" 
               aria-label="Assessments">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-award"></use></svg>
                <span>Assessments</span>
            </a>

            <a href="<?= url('/admin/content?tab=enquiries') ?>" 
               class="sidebar-item <?= ($currentPath === '/admin/content' && ($_GET['tab'] ?? '') === 'enquiries') ? 'active' : '' ?>" 
               data-tooltip="Enquiries" 
               aria-label="Enquiries">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-mail"></use></svg>
                <span>Enquiries</span>
            </a>

            <a href="<?= url('/admin/content?tab=blog') ?>" 
               class="sidebar-item <?= ($currentPath === '/admin/content' && ($_GET['tab'] ?? '') === 'blog') ? 'active' : '' ?>" 
               data-tooltip="Blog" 
               aria-label="Blog">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-book-open"></use></svg>
                <span>Blog</span>
            </a>

            <a href="<?= url('/admin/content?tab=projects') ?>" 
               class="sidebar-item <?= ($currentPath === '/admin/content' && ($_GET['tab'] ?? '') === 'projects') ? 'active' : '' ?>" 
               data-tooltip="Projects" 
               aria-label="Projects">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-building"></use></svg>
                <span>Projects</span>
            </a>

            <a href="<?= url('/admin/content') ?>" 
               class="sidebar-item <?= ($currentPath === '/admin/content' && empty($_GET['tab'])) ? 'active' : '' ?>" 
               data-tooltip="Site Content" 
               aria-label="Site Content">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-layout"></use></svg>
                <span>Site Content</span>
            </a>

            <a href="<?= url('/admin/content?tab=settings') ?>" 
               class="sidebar-item <?= ($currentPath === '/admin/content' && ($_GET['tab'] ?? '') === 'settings') ? 'active' : '' ?>" 
               data-tooltip="Settings" 
               aria-label="Settings">
                <svg width="20" height="20" aria-hidden="true"><use href="#icon-settings"></use></svg>
                <span>Settings</span>
            </a>
        </nav>

        <!-- Sidebar Footer: Avatar, Name, Log out -->
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="user-avatar" aria-hidden="true">
                    <?= strtoupper(substr($adminUser['full_name'] ?? 'A', 0, 1)) ?>
                </div>
                <div class="user-name" title="<?= e($adminUser['full_name'] ?? 'Admin') ?>">
                    <?= e($adminUser['full_name'] ?? 'Admin') ?>
                </div>
            </div>
            <a href="<?= url('/logout') ?>" class="logout-link" aria-label="Log out of admin">
                <svg width="16" height="16" aria-hidden="true" style="margin-right: 4px; vertical-align: -2px;"><use href="#icon-log-out"></use></svg><span class="logout-label">Log out</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="main">
        <!-- Top bar: sticky, 64px, title on left, search and avatar on right -->
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="topbar-menu-btn" aria-label="Open sidebar navigation" onclick="toggleAdminSidebar(true)">
                    <svg width="22" height="22" aria-hidden="true"><use href="#icon-menu"></use></svg>
                </button>
                <h1 class="topbar-title"><?= e($viewTitle ?? 'Dashboard') ?></h1>
            </div>

            <div class="topbar-right">
                <form action="<?= url('/admin/candidates') ?>" method="GET" class="topbar-search-form" role="search">
                    <span class="topbar-search-icon" aria-hidden="true">
                        <svg width="16" height="16" aria-hidden="true"><use href="#icon-search"></use></svg>
                    </span>
                    <input type="text" name="q" class="topbar-search-input" placeholder="Search candidates" value="<?= e($_GET['q'] ?? '') ?>" aria-label="Search candidates">
                </form>
                <div class="user-avatar" aria-label="<?= e($adminUser['full_name'] ?? 'Admin') ?>" title="<?= e($adminUser['full_name'] ?? 'Admin') ?>">
                    <?= strtoupper(substr($adminUser['full_name'] ?? 'A', 0, 1)) ?>
                </div>
            </div>
        </header>

        <!-- Page View Container -->
        <main class="page">
            <?= $content ?? '' ?>
        </main>
    </div>

</div>

<script>
function toggleAdminSidebar(open) {
    var sidebar = document.getElementById('adminSidebar');
    var backdrop = document.getElementById('sidebarBackdrop');
    if (!sidebar || !backdrop) return;
    if (open) {
        sidebar.classList.add('open');
        backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
    } else {
        sidebar.classList.remove('open');
        backdrop.classList.remove('active');
        syncBodyModalScrollLock();
    }
}

// Universal Popup & Modal Background Scroll-Lock System
function syncBodyModalScrollLock() {
    var sidebarOpen = document.getElementById('adminSidebar')?.classList.contains('open');
    if (sidebarOpen) {
        document.body.style.overflow = 'hidden';
        return;
    }

    var selectors = [
        '.modal-overlay.active',
        '.drawer-overlay.active',
        '.drawer-panel.open',
        '.drawer-panel.active',
        '.modal-backdrop.open',
        '.modal-backdrop.active',
        '.modal-overlay',
        '.drawer-overlay'
    ];
    var openElements = document.querySelectorAll(selectors.join(','));
    var hasVisibleModal = false;

    for (var i = 0; i < openElements.length; i++) {
        var el = openElements[i];
        if (el.classList.contains('active') || el.classList.contains('open')) {
            hasVisibleModal = true;
            break;
        }
        var comp = window.getComputedStyle(el);
        if (comp.display !== 'none' && comp.visibility !== 'hidden' && comp.opacity !== '0' && (el.style.display === 'flex' || el.style.display === 'block')) {
            hasVisibleModal = true;
            break;
        }
    }

    if (hasVisibleModal) {
        if (!document.body.classList.contains('modal-open')) {
            document.body.classList.add('modal-open');
        }
        document.body.style.overflow = 'hidden';
    } else {
        if (document.body.classList.contains('modal-open')) {
            document.body.classList.remove('modal-open');
        }
        document.body.style.overflow = '';
    }
}

if (window.MutationObserver) {
    var modalObserver = new MutationObserver(function() {
        syncBodyModalScrollLock();
    });
    modalObserver.observe(document.documentElement, {
        subtree: true,
        attributes: true,
        attributeFilter: ['class', 'style']
    });
}
document.addEventListener('DOMContentLoaded', syncBodyModalScrollLock);
</script>

</body>
</html>
