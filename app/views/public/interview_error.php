<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Notice | Realty Smartz Pathshala</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #1F5C4A;
            --brand-hover: #17483A;
            --bg: #F7F5EF;
            --surface: #FFFFFF;
            --line: #E2DDD0;
            --text: #26332D;
            --muted: #6D766F;
            --danger: #B3261E;
            --danger-soft: #FDF2F2;
            --r-md: 12px;
            --r-lg: 20px;
            --r-full: 9999px;
            --font-main: 'DM Sans', system-ui, -apple-system, sans-serif;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-main);
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
        }

        .public-header {
            background-color: var(--surface);
            border-bottom: 1px solid var(--line);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .public-header a {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
        }

        .public-logo {
            height: 42px;
            width: auto;
            display: block;
        }

        .error-container {
            width: 100%;
            max-width: 520px;
            margin: auto;
            padding: 24px 16px;
        }

        .error-card {
            background-color: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            padding: 44px 32px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(38, 51, 45, 0.04);
        }

        .icon-circle {
            width: 64px;
            height: 64px;
            background-color: var(--danger-soft);
            color: var(--danger);
            border-radius: var(--r-full);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .error-message {
            font-size: 16px;
            color: var(--text);
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--brand);
            color: #FFFFFF;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: var(--r-full);
            font-size: 14.5px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header class="public-header" role="banner">
        <a href="<?= url('/') ?>" aria-label="Realty Smartz Pathshala Homepage">
            <img src="<?= asset('/assets/img/logo/Pathshala.webp') ?>" alt="Realty Smartz Pathshala" class="public-logo">
        </a>
    </header>

    <main class="error-container" role="main">
        <div class="error-card">
            <div class="icon-circle" aria-hidden="true">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <p class="error-message"><?= e($errorMessage ?? 'An error occurred while processing your request. Please try again.') ?></p>
            <a href="<?= url('/q/' . urlencode($slug)) ?>" class="btn-action">Return to Form</a>
        </div>
    </main>
</body>
</html>
