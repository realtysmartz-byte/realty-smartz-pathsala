<?php
$currentUser = auth();
$pageTitle = $pageTitle ?? 'Candidate Portal | Realty Smartz Pathshala';
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/dashboard', PHP_URL_PATH);
$candidateStatus = $currentUser['status'] ?? 'registered';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="icon" type="image/png" href="<?= asset('/assets/img/favicon.png') ?>">

    <!-- Master Design Tokens -->
    <link rel="stylesheet" href="<?= asset('/assets/css/tokens.css') ?>">

    <!-- Preload Self-Hosted DM Sans Variable Font -->
    <link rel="preload" href="<?= asset('/assets/fonts/dm-sans-latin.woff2') ?>" as="font" type="font/woff2" crossorigin>

    <!-- Master CSS -->
    <link rel="stylesheet" href="<?= asset('/assets/css/style.css') ?>">

    <style>
        .candidate-topbar {
            height: 64px;
            background: var(--surface);
            border-bottom: 1px solid var(--line);
            padding: 0 20px;
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
        }

        @media (max-width: 768px) {
            .candidate-topbar {
                height: 56px;
                padding: 0 16px;
            }
        }

        .candidate-topbar-inner {
            max-width: 1040px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .candidate-avatar-btn {
            background: transparent;
            border: none;
            padding: 0;
            display: flex;
            align-items: center;
            cursor: pointer;
            text-decoration: none;
        }

        .candidate-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--brand-soft);
            color: var(--brand);
            border: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            transition: box-shadow 150ms ease;
        }

        .candidate-avatar-btn:focus .candidate-avatar,
        .candidate-avatar-btn:hover .candidate-avatar {
            box-shadow: 0 0 0 3px var(--brand-soft);
        }

        .candidate-content-container {
            max-width: 1040px;
            width: 100%;
            margin: 0 auto;
            padding: 32px 20px 80px;
            box-sizing: border-box;
            flex: 1 0 auto;
        }
    </style>
</head>
<body class="bg-body">

    <!-- Top Bar: Logo left; Right side = avatar button with dropdown (ONE clean row) -->
    <header class="candidate-topbar" role="banner">
        <div class="candidate-topbar-inner">
            <a href="<?= url('/') ?>" class="text-decoration-none d-flex align-items-center" aria-label="Realty Smartz Pathshala Homepage">
                <img src="<?= asset('/assets/img/logo/Pathshala.webp') ?>" alt="Realty Smartz Pathshala" height="40" width="auto">
            </a>

            <!-- Avatar Dropdown (Name, ID #, Logout) -->
            <div class="dropdown">
                <button class="candidate-avatar-btn" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="User Account Menu">
                    <span class="candidate-avatar">
                        <?= strtoupper(substr($currentUser['full_name'] ?? 'C', 0, 1)) ?>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border" aria-labelledby="userMenuDropdown" style="border-radius: var(--r-md); border-color: var(--line) !important; min-width: 200px; padding: 8px 0;">
                    <li class="px-3 py-2 border-bottom" style="border-color: var(--line) !important;">
                        <div class="fw-bold text-dark" style="font-size: 14px;"><?= e($currentUser['full_name'] ?? 'Candidate') ?></div>
                        <div class="text-muted" style="font-size: 13px;">ID #<?= $currentUser['id'] ?? '' ?></div>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="<?= url('/dashboard') ?>" style="font-size: 14px;">
                            Application Overview
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="<?= url('/dashboard/onboarding') ?>" style="font-size: 14px;">
                            Onboarding Form
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1" style="border-color: var(--line) !important;"></li>
                    <li>
                        <a class="dropdown-item py-2 text-danger" href="<?= url('/logout') ?>" style="font-size: 14px;">
                            Log Out
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content Yield Container (Max-width: 760px on --bg) -->
    <main class="candidate-content-container">
        <!-- Flash Alert Toasts / Notifications -->
        <?php if (!empty($_SESSION['_flash']['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4 small fw-bold" role="alert">
                <?= e(flash('success')) ?>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['_flash']['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4 small fw-bold" role="alert">
                <?= e(flash('error')) ?>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <!-- Master Scripts -->
    <script src="<?= asset('/assets/js/main.js') ?>" defer></script>
</body>
</html>
