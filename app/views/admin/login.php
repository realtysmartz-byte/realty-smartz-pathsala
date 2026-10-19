<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrative Sign In | Realty Smartz Pathshala</title>
    <link rel="icon" type="image/png" href="<?= asset('assets/img/favicon.png') ?>">
    <!-- Master Design Tokens -->
    <link rel="stylesheet" href="<?= asset('assets/css/tokens.css') ?>">

    <!-- Preload Self-Hosted DM Sans Variable Font -->
    <link rel="preload" href="<?= asset('assets/fonts/dm-sans-latin.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <style>
        @font-face {
            font-family: 'DM Sans';
            font-style: normal;
            font-weight: 100 1000;
            font-display: swap;
            src: url('<?= asset("assets/fonts/dm-sans-latin.woff2") ?>') format('woff2');
            unicode-range: U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD;
        }

        :root {
            --bg: #F7F5EF;
            --surface: #FFFFFF;
            --surface-2: #EFEBE0;
            --border: #E2DDD0;
            --line: #E2DDD0;
            --text: #26332D;
            --muted: #6D766F;
            --brand: #1F5C4A;
            --brand-hover: #174839;
            --brand-soft: #E2EEE8;
            --gold: #C9A24A;
            --danger: #C0392B;
            --success: #2E7D5B;
            --warning: #C77700;
            --font-family: 'DM Sans', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            --r-sm: 8px;
            --r-md: 12px;
            --r-lg: 20px;
            --r-full: 999px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        button, input, select, textarea {
            font-family: var(--font-family);
        }

        a {
            color: var(--brand);
            text-decoration: none;
        }

        body.admin-login-body {
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            font-family: var(--font-family);
            -webkit-font-smoothing: antialiased;
        }

        .admin-login-wrapper {
            position: relative;
            z-index: 1;
            max-width: 440px;
            width: 100%;
        }

        .admin-login-card {
            background: var(--surface);
            border-radius: var(--r-lg);
            border: 1px solid var(--line);
            box-shadow: 0 16px 36px -12px rgba(38, 51, 45, 0.08);
            padding: 36px 32px;
            overflow: hidden;
        }

        .admin-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--brand-soft);
            color: var(--brand);
            border: 1px solid var(--line);
            padding: 5px 14px;
            border-radius: var(--r-full);
            font-size: 11.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
        }

        .admin-title {
            font-family: var(--font-family);
            font-size: 1.75rem;
            color: var(--text);
            font-weight: 500;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }

        .gold-rule-center {
            width: 48px;
            height: 2px;
            background: var(--gold);
            margin: 10px auto 14px;
        }

        .admin-subtitle {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
            margin: 0;
        }

        .admin-input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .admin-label {
            display: block;
            color: var(--text);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 7px;
        }

        .admin-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .admin-input-icon {
            position: absolute;
            left: 14px;
            color: var(--muted);
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        .admin-input {
            width: 100%;
            height: 44px;
            padding: 10px 42px 10px 42px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            color: var(--text);
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .admin-input:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-soft);
        }

        .admin-input::placeholder {
            color: var(--muted);
            opacity: 0.8;
        }

        .pwd-toggle-btn {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            color: var(--muted);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--r-sm);
            transition: color 0.15s ease;
        }

        .pwd-toggle-btn:hover {
            color: var(--text);
        }

        .btn-submit-portal {
            width: 100%;
            height: 44px;
            border-radius: var(--r-md);
            background: var(--brand);
            color: #FFFFFF;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.15s ease, transform 0.15s ease;
            margin-top: 10px;
        }

        .btn-submit-portal:hover:not(:disabled) {
            background: var(--brand-hover);
            transform: translateY(-1px);
        }

        .btn-submit-portal:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .alert-error-box {
            background: #FDF4F3;
            border: 1px solid rgba(192, 57, 43, 0.25);
            color: var(--danger);
            padding: 12px 16px;
            border-radius: var(--r-md);
            font-size: 13.5px;
            line-height: 1.45;
            margin-bottom: 22px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-success-box {
            background: #F2F8F5;
            border: 1px solid rgba(46, 125, 91, 0.25);
            color: var(--success);
            padding: 12px 16px;
            border-radius: var(--r-md);
            font-size: 13.5px;
            line-height: 1.45;
            margin-bottom: 22px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .return-link {
            color: var(--brand);
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            transition: color 0.15s ease;
        }

        .return-link:hover {
            color: var(--brand-hover);
            text-decoration: underline;
        }

        .security-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: var(--muted);
            line-height: 1.5;
            padding: 0 10px;
        }

        .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #FFFFFF;
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .admin-login-card {
                padding: 28px 20px;
            }
            .admin-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body class="admin-login-body">

<div class="admin-login-wrapper">

    <!-- Top Branding & Headers -->
    <div style="text-align:center; margin-bottom: 26px;">
        <a href="<?= url('/') ?>" style="display:inline-block; margin-bottom: 16px;" title="Realty Smartz Pathshala">
            <img src="<?= asset('assets/img/logo/Pathshala.webp') ?>" alt="Realty Smartz Pathshala" style="height: 52px; width:auto; object-fit: contain;">
        </a>
        <br>
        <div class="admin-chip">
            <?= icon('lock', '', 13) ?>
            <span>Administration Portal</span>
        </div>
        <h1 class="admin-title">Administrative Sign In</h1>
        <div class="gold-rule-center"></div>
        <p class="admin-subtitle">Authorized staff &amp; recruitment management access only</p>
    </div>

    <!-- Centered Card -->
    <div class="admin-login-card">
        
        <?php if ($msg = flash('error')): ?>
            <div class="alert-error-box" role="alert">
                <span style="flex-shrink:0; margin-top:1px;"><?= icon('alert-circle', '', 18) ?></span>
                <div><?= e($msg) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($msg = flash('success')): ?>
            <div class="alert-success-box" role="status">
                <span style="flex-shrink:0; margin-top:1px;"><?= icon('check-circle', '', 18) ?></span>
                <div><?= e($msg) ?></div>
            </div>
        <?php endif; ?>

        <form action="<?= url('/admin/login') ?>" method="POST" id="adminLoginForm">
            <?= csrf_field() ?>

            <!-- Staff Email Field -->
            <div class="admin-input-group">
                <label for="adminEmail" class="admin-label">Staff Email Address</label>
                <div class="admin-input-wrap">
                    <span class="admin-input-icon"><?= icon('mail', '', 17) ?></span>
                    <input type="email" 
                           id="adminEmail" 
                           name="email" 
                           required 
                           autocomplete="email"
                           placeholder="admin@realtysmartzpathshala.in" 
                           class="admin-input"
                           value="<?= e($_POST['email'] ?? '') ?>">
                </div>
            </div>

            <!-- Secure Password Field -->
            <div class="admin-input-group" style="margin-bottom: 24px;">
                <label for="adminPassword" class="admin-label">Secure Password</label>
                <div class="admin-input-wrap">
                    <span class="admin-input-icon"><?= icon('lock', '', 17) ?></span>
                    <input type="password" 
                           id="adminPassword" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="••••••••" 
                           class="admin-input" 
                           style="padding-right: 44px;">
                    <button type="button" 
                            id="togglePasswordBtn" 
                            class="pwd-toggle-btn" 
                            aria-label="Show or hide password"
                            onclick="togglePasswordVisibility()">
                        <span id="eyeOpen"><?= icon('eye', '', 17) ?></span>
                        <span id="eyeClosed" style="display:none;"><?= icon('eye-off', '', 17) ?></span>
                    </button>
                </div>
            </div>

            <!-- Full-width Filled Brand Button with Loading State -->
            <button type="submit" id="submitBtn" class="btn-submit-portal">
                <span class="spinner" id="btnSpinner"></span>
                <span id="btnIcon"><?= icon('shield-check', '', 17) ?></span>
                <span id="btnText">Sign In to Portal</span>
            </button>
        </form>

        <!-- Clearly Visible Return Link -->
        <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--line); text-align: center;">
            <a href="<?= url('/') ?>" class="return-link">
                <?= icon('arrow-left', '', 15) ?>
                <span>Return to Public Website</span>
            </a>
        </div>
    </div>

    <!-- Security Footer Notice -->
    <div class="security-footer">
        Protected by IP rate limiting &amp; session auditing. 5 failed attempts locks access for 15 minutes.
    </div>

</div>

<script>
function togglePasswordVisibility() {
    const pwdInput = document.getElementById('adminPassword');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');
    
    if (pwdInput.type === 'password') {
        pwdInput.type = 'text';
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'inline-flex';
    } else {
        pwdInput.type = 'password';
        eyeOpen.style.display = 'inline-flex';
        eyeClosed.style.display = 'none';
    }
}

document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('submitBtn');
    const spinner = document.getElementById('btnSpinner');
    const icon = document.getElementById('btnIcon');
    const text = document.getElementById('btnText');
    
    // Check form validity before triggering visual loading state
    if (!this.checkValidity()) {
        return;
    }

    btn.disabled = true;
    spinner.style.display = 'inline-block';
    icon.style.display = 'none';
    text.textContent = 'Authenticating...';
});
</script>

</body>
</html>
