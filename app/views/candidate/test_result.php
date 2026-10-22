<?php
$db = \App\Config\Database::getConnection();

$attemptId = (int)$attempt['id'];
$stmtCat = $db->prepare("SELECT 
        c.name as category_name,
        COUNT(aa.id) as total_in_cat,
        SUM(CASE WHEN aa.is_correct = 1 THEN 1 ELSE 0 END) as correct_in_cat
    FROM `attempt_answers` aa
    JOIN `questions` q ON aa.question_id = q.id
    JOIN `test_categories` c ON q.category_id = c.id
    WHERE aa.attempt_id = :aid
    GROUP BY c.id, c.name
    ORDER BY c.id ASC");
$stmtCat->execute([':aid' => $attemptId]);
$categoryBreakdown = $stmtCat->fetchAll();

$passed = !empty($attempt['passed']);
$score = (int)$attempt['score'];
$totalQuestions = (int)($attempt['total_questions'] ?? 15);
$percentage = round((float)$attempt['percentage']);

$diffSec = 0;
if (!empty($attempt['started_at']) && !empty($attempt['completed_at'])) {
    $diffSec = max(0, strtotime($attempt['completed_at']) - strtotime($attempt['started_at']));
}
$min = floor($diffSec / 60);
$sec = $diffSec % 60;
$timeTakenStr = sprintf('%02d:%02d', $min, $sec);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Result | Realty Smartz Pathshala</title>
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
            padding: 24px 20px;
            font-optical-sizing: auto;
        }
        .result-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            padding: 32px;
            max-width: 480px;
            width: 100%;
            overflow: hidden;
        }
        .result-title {
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }
        .score-large {
            font-size: 40px;
            font-weight: 400;
            color: <?= $passed ? 'var(--success)' : 'var(--danger)' ?>;
            line-height: 1.1;
            letter-spacing: -0.025em;
        }
        .percentage-chip {
            font-size: 16px;
            font-weight: 500;
            color: var(--muted);
            margin-top: 4px;
            margin-bottom: 14px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: var(--r-full);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
            background: <?= $passed ? 'var(--brand-soft)' : 'rgba(192, 57, 43, 0.12)' ?>;
            color: <?= $passed ? 'var(--brand)' : 'var(--danger)' ?>;
        }
        .result-rows {
            display: flex;
            flex-direction: column;
            gap: 12px;
            border-top: 1px solid var(--line);
            padding-top: 20px;
            margin-bottom: 28px;
        }
        .result-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: 400;
            color: var(--text);
        }
        .result-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .btn-primary {
            width: 100%;
            height: 48px;
            background: var(--brand);
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
            transition: background-color 0.2s ease;
        }
        .btn-primary:hover {
            background: var(--brand-hover);
        }
        .btn-secondary {
            width: 100%;
            height: 48px;
            background: transparent;
            color: var(--text);
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        .btn-secondary:hover {
            background: var(--surface-2);
        }
    </style>
</head>
<body>
    <div class="result-card">
        <div class="result-title">Assessment Result</div>
        <div class="score-large"><?= $score ?> / <?= $totalQuestions ?></div>
        <div class="percentage-chip"><?= $percentage ?>% Correct</div>
        <div class="status-badge"><?= $passed ? 'Passed &bull; Recommended' : 'Failed &bull; Below 60%' ?></div>

        <div class="result-rows">
            <div class="result-row">
                <span style="color: var(--muted);">Time Taken</span>
                <strong><?= $timeTakenStr ?></strong>
            </div>
            <?php foreach ($categoryBreakdown as $cat): ?>
                <div class="result-row">
                    <span style="color: var(--muted);"><?= e($cat['category_name']) ?></span>
                    <strong><?= (int)$cat['correct_in_cat'] ?> / <?= (int)$cat['total_in_cat'] ?></strong>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="result-actions">
            <a href="<?= url('/dashboard/test/scorecard/' . $attemptId) ?>" class="btn-primary">Download Scorecard PDF</a>
            <a href="<?= url('/dashboard') ?>" class="btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
