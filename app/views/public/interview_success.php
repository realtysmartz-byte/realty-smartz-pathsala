<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Application Submitted | Realty Smartz Pathshala</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #1F5C4A;
            --brand-soft: #E8F1EE;
            --bg: #F7F5EF;
            --surface: #FFFFFF;
            --line: #E2DDD0;
            --text: #26332D;
            --muted: #6D766F;
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

        .success-container {
            width: 100%;
            max-width: 520px;
            margin: auto;
            padding: 24px 16px;
        }

        .success-card {
            background-color: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            padding: 44px 32px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(38, 51, 45, 0.04);
        }

        .icon-circle {
            width: 72px;
            height: 72px;
            background-color: var(--brand-soft);
            color: var(--brand);
            border-radius: var(--r-full);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .success-message {
            font-size: 18px;
            font-weight: 500;
            color: var(--text);
            line-height: 1.55;
        }
    </style>
</head>
<body>
    <header class="public-header" role="banner">
        <a href="/" aria-label="Realty Smartz Pathshala Homepage">
            <img src="<?= asset('/assets/img/logo/Pathshala.webp') ?>" alt="Realty Smartz Pathshala" class="public-logo">
        </a>
    </header>

    <main class="success-container" role="main">
        <div class="success-card">
            <div class="icon-circle" aria-hidden="true">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <p class="success-message">Thank you! Your form has been filled successfully. Our team will contact you shortly. Please wait.</p>
        </div>
    </main>
</body>
</html>
