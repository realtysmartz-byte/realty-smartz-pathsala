<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Form Closed | Realty Smartz Pathshala</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #F7F5EF;
            --surface: #FFFFFF;
            --line: #E2DDD0;
            --text: #26332D;
            --r-lg: 20px;
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

        .closed-container {
            width: 100%;
            max-width: 520px;
            margin: auto;
            padding: 24px 16px;
        }

        .closed-card {
            background-color: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            padding: 44px 32px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(38, 51, 45, 0.04);
        }

        .closed-message {
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

    <main class="closed-container" role="main">
        <div class="closed-card">
            <p class="closed-message">This form is not accepting responses right now.</p>
        </div>
    </main>
</body>
</html>
