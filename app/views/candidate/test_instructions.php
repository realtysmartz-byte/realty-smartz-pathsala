<?php
$timeLimit = (int)($testConfig['time_limit_minutes'] ?? 10);
$totalQuestions = (int)($testConfig['total_questions'] ?? 15);
$passingPercentage = (float)($testConfig['passing_percentage'] ?? 60.0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aptitude Test Instructions | Realty Smartz Pathshala</title>
    <!-- Master Design Tokens -->
    <link rel="stylesheet" href="<?= asset('/assets/css/tokens.css') ?>">

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
            --line: #E2DDD0;
            --text: #26332D;
            --muted: #6D766F;
            --brand: #1F5C4A;
            --brand-soft: #E2EEE8;
            --primary: #1F5C4A;
            --success: #2E7D5B;
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
        body {
            font-family: var(--font-family);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .start-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            padding: 32px;
            max-width: 440px;
            width: 100%;
            overflow: hidden;
        }
        .start-title {
            font-family: var(--font-family);
            font-size: 20px;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 20px;
        }
        .start-rows {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-bottom: 28px;
            border-top: 1px solid var(--line);
            padding-top: 20px;
        }
        .start-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            font-weight: 400;
            color: var(--text);
        }
        .start-btn {
            width: 100%;
            height: 44px;
            background: var(--primary);
            color: #FFFFFF;
            border: none;
            border-radius: var(--r-md);
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: opacity 0.15s ease;
        }
        .start-btn:hover {
            opacity: 0.92;
        }
    </style>
</head>
<body>
    <div class="start-card">
        <h1 class="start-title">Aptitude Assessment</h1>
        <div class="start-rows">
            <div class="start-row">
                <span style="color: var(--muted);">Questions</span>
                <strong><?= (int)$totalQuestions ?> Questions</strong>
            </div>
            <div class="start-row">
                <span style="color: var(--muted);">Time Limit</span>
                <strong><?= (int)$timeLimit ?> Minutes</strong>
            </div>
            <div class="start-row">
                <span style="color: var(--muted);">Passing Score</span>
                <strong><?= round($passingPercentage) ?>%</strong>
            </div>
        </div>
        <form action="<?= url('/dashboard/test/start') ?>" method="POST">
            <?= csrf_field() ?>
            <button type="submit" class="start-btn">Start Assessment</button>
        </form>
    </div>
</body>
</html>
