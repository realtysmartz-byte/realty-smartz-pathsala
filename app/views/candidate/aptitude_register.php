<?php
$flashError = flash('error');
$flashSuccess = flash('success');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aptitude Assessment Registration | Realty Smartz Pathshala</title>
    <link rel="preload" href="<?= asset('/assets/fonts/dm-sans-latin.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= asset('/assets/css/tokens.css') ?>">
    <style>
        @font-face {
            font-family: 'DM Sans';
            font-style: normal;
            font-weight: 100 1000;
            font-display: swap;
            src: url('<?= asset("/assets/fonts/dm-sans-latin.woff2") ?>') format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: var(--font-family);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .register-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            width: 100%;
            max-width: 480px;
            padding: 36px 32px;
            box-shadow: 0 4px 16px rgba(38, 51, 45, 0.04);
        }
        @media (max-width: 480px) {
            .register-card {
                padding: 24px 20px;
            }
        }
        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--brand-soft);
            color: var(--brand);
            font-size: 13px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: var(--r-full);
            margin-bottom: 12px;
        }
        .card-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.25;
            margin-bottom: 8px;
        }
        .card-subtitle {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.5;
        }
        .alert-error {
            background: #FDF2F2;
            border: 1px solid #F5C6CB;
            color: #721C24;
            padding: 12px 16px;
            border-radius: var(--r-md);
            font-size: 14px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #E8F3EE;
            border: 1px solid #C1E0D2;
            color: var(--brand);
            padding: 12px 16px;
            border-radius: var(--r-md);
            font-size: 14px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            height: 44px;
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            padding: 0 14px;
            font-family: inherit;
            font-size: 15px;
            color: var(--text);
            background: var(--surface);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-soft);
        }
        .form-hint {
            font-size: 12px;
            color: var(--muted);
            margin-top: 5px;
        }
        .info-box {
            background: var(--surface-2);
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            padding: 14px;
            margin-bottom: 24px;
            font-size: 13px;
            color: var(--muted);
            line-height: 1.45;
        }
        .btn-submit {
            width: 100%;
            height: 48px;
            background: var(--brand);
            color: #FFFFFF;
            border: none;
            border-radius: var(--r-md);
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background-color 0.15s ease;
        }
        .btn-submit:hover {
            background: var(--brand-hover);
        }
        .footer-note {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: var(--muted);
        }
        .footer-note a {
            color: var(--brand);
            text-decoration: none;
            font-weight: 500;
        }
        .footer-note a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="brand-header">
            <div class="brand-badge">Aptitude Assessment</div>
            <h1 class="card-title">Candidate Registration</h1>
            <p class="card-subtitle">Please enter your basic information to begin your 15-question aptitude evaluation.</p>
        </div>

        <?php if ($flashError): ?>
            <div class="alert-error" role="alert"><?= e($flashError) ?></div>
        <?php endif; ?>

        <?php if ($flashSuccess): ?>
            <div class="alert-success" role="status"><?= e($flashSuccess) ?></div>
        <?php endif; ?>

        <form action="<?= url('/aptitude/register') ?>" method="POST" novalidate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="full_name" class="form-label">Full Name <span style="color: #B91C1C;">*</span></label>
                <input type="text" id="full_name" name="full_name" class="form-input" placeholder="e.g. Rahul Sharma" value="<?= e($_POST['full_name'] ?? '') ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="mobile" class="form-label">Mobile Number <span style="color: #B91C1C;">*</span></label>
                <input type="tel" id="mobile" name="mobile" class="form-input" placeholder="e.g. 9876543210" maxlength="10" value="<?= e($_POST['mobile'] ?? '') ?>" required>
                <div class="form-hint">10-digit Indian mobile number for communication</div>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address <span style="color: #B91C1C;">*</span></label>
                <input type="email" id="email" name="email" class="form-input" placeholder="e.g. rahul.sharma@example.com" value="<?= e($_POST['email'] ?? '') ?>" required>
                <div class="form-hint">Your assessment scorecard will be sent to this email</div>
            </div>

            <div class="info-box">
                <strong>Assessment Overview:</strong> 15 multiple-choice questions &bull; 10 minutes time limit &bull; Passing benchmark: 50%. You can download your official score verification card immediately after completing the test.
            </div>

            <button type="submit" class="btn-submit">
                <span>Start Aptitude Assessment</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
        </form>

        <div class="footer-note">
            Already have an account? <a href="<?= url('/login') ?>">Sign in to portal</a>
        </div>
    </div>
</body>
</html>
