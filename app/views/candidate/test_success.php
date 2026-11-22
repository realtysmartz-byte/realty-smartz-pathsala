<?php
$db = \App\Config\Database::getConnection();

$attemptId = (int)($attempt['id'] ?? 0);
$score = (int)($attempt['score'] ?? 0);
$totalQuestions = (int)($attempt['total_questions'] ?? 15);
$percentage = round((float)($attempt['percentage'] ?? 0));
$setCode = $attempt['set_code'] ?? ($attempt['set_name'] ?? 'Set A');

$diffSec = 0;
if (!empty($attempt['started_at']) && !empty($attempt['completed_at'])) {
    $diffSec = max(0, strtotime($attempt['completed_at']) - strtotime($attempt['started_at']));
}
$min = floor($diffSec / 60);
$sec = $diffSec % 60;
$timeTakenStr = sprintf('%02d min %02d sec', $min, $sec);

// Category breakdown
$categoryBreakdown = [];
if ($attemptId > 0) {
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
}

$currentUser = auth();
$candidateName = $currentUser['full_name'] ?? ($attempt['guest_name'] ?? 'Candidate');
$isOnboarded = !empty($currentUser) && ($currentUser['candidate_type'] ?? '') !== 'aptitude_only';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Submitted Successfully | Realty Smartz Pathshala</title>
    <link rel="preload" href="<?= asset('/assets/fonts/dm-sans-latin.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= asset('/assets/css/tokens.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @font-face {
            font-family: 'DM Sans';
            font-style: normal;
            font-weight: 100 1000;
            font-display: swap;
            src: url('<?= asset("/assets/fonts/dm-sans-latin.woff2") ?>') format('woff2');
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: var(--font-family, 'DM Sans', system-ui, sans-serif);
            background: var(--bg, #F7F5EF);
            color: var(--text, #26332D);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }
        .success-card {
            background: #FFFFFF;
            border: 1px solid #E2DDD0;
            border-radius: 24px;
            padding: 40px 36px;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: 0 16px 40px rgba(38, 51, 45, 0.08);
            position: relative;
            overflow: hidden;
        }
        .success-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #1F5C4A 0%, #2E7D5B 50%, #C9A24A 100%);
        }
        .icon-circle {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: #E2EEE8;
            color: #1F5C4A;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(31, 92, 74, 0.15);
        }
        .success-title {
            font-size: 24px;
            font-weight: 700;
            color: #26332D;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }
        .success-sub {
            font-size: 14.5px;
            color: #6D766F;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .score-pill-box {
            background: #F7F5EF;
            border: 1px solid #E2DDD0;
            border-radius: 18px;
            padding: 22px;
            margin-bottom: 24px;
        }
        .score-main {
            font-size: 42px;
            font-weight: 800;
            color: #1F5C4A;
            line-height: 1;
            margin-bottom: 6px;
        }
        .score-meta-text {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #E2EEE8;
            color: #1F5C4A;
            padding: 4px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 14px;
        }
        .breakdown-list {
            margin-top: 14px;
            border-top: 1px dashed #E2DDD0;
            padding-top: 14px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            text-align: left;
            font-size: 13px;
        }
        .breakdown-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #6D766F;
        }
        .breakdown-row strong {
            color: #26332D;
            font-weight: 600;
        }
        .actions-col {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 24px;
        }
        .btn-home {
            width: 100%;
            height: 48px;
            background: #1F5C4A;
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(31, 92, 74, 0.25);
        }
        .btn-home:hover {
            background: #164638;
            transform: translateY(-1px);
        }
        .btn-secondary-link {
            width: 100%;
            height: 46px;
            background: #FFFFFF;
            color: #26332D;
            border: 1px solid #E2DDD0;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-secondary-link:hover {
            background: #F7F5EF;
            border-color: #CBD5E1;
        }
        .support-text {
            font-size: 12.5px;
            color: #8C948D;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="icon-circle">
            <i class="fas fa-check"></i>
        </div>

        <h1 class="success-title">Successfully Submitted!</h1>
        <p class="success-sub">
            Congratulations, <strong><?= e($candidateName) ?></strong>! Your Aptitude Assessment has been recorded and you have passed the benchmark cutoff.
        </p>

        <div class="score-pill-box">
            <div class="score-main"><?= $score ?> / <?= $totalQuestions ?></div>
            <div class="score-meta-text">
                <i class="fas fa-award"></i> <?= $percentage ?>% Score &bull; PASSED
            </div>

            <div class="breakdown-list">
                <div class="breakdown-row">
                    <span><i class="far fa-clock" style="margin-right: 6px;"></i> Time Taken</span>
                    <strong><?= $timeTakenStr ?></strong>
                </div>
                <div class="breakdown-row">
                    <span><i class="fas fa-layer-group" style="margin-right: 6px;"></i> Test Set</span>
                    <strong><?= e($setCode) ?></strong>
                </div>
                <?php foreach ($categoryBreakdown as $cat): ?>
                    <div class="breakdown-row">
                        <span><?= e($cat['category_name']) ?></span>
                        <strong><?= (int)$cat['correct_in_cat'] ?> / <?= (int)$cat['total_in_cat'] ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="actions-col">
            <!-- Primary Link: Homepage -->
            <a href="<?= url('/') ?>" class="btn-home">
                <i class="fas fa-home"></i> Return to Homepage
            </a>

            <!-- Download Scorecard PDF -->
            <?php if ($attemptId > 0): ?>
                <a href="<?= url('/dashboard/test/scorecard/' . $attemptId) ?>" class="btn-secondary-link" target="_blank">
                    <i class="fas fa-file-pdf" style="color: #c5221f;"></i> Download Official Scorecard (PDF)
                </a>
            <?php endif; ?>

            <!-- If Onboarded candidate, link back to onboarding / dashboard -->
            <?php if ($isOnboarded): ?>
                <a href="<?= url('/dashboard/onboarding') ?>" class="btn-secondary-link" style="color: #1F5C4A; border-color: #1F5C4A;">
                    <i class="fas fa-user-check"></i> Continue Onboarding Portal
                </a>
            <?php endif; ?>
        </div>

        <!-- Realty Smartz HRMS Portal Card -->
        <div style="margin-top: 24px; padding: 20px 18px; background: #F0FDF4; border: 1.5px dashed #22C55E; border-radius: 18px; text-align: center;">
            <div style="font-weight: 800; color: #15803D; font-size: 15px; margin-bottom: 6px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fas fa-building"></i> Realty Smartz HRMS Employee Portal
            </div>
            <p style="font-size: 13px; color: #166534; margin-bottom: 12px; line-height: 1.45;">
                Your onboarding & assessment are recorded. Access your official employee portal at <strong>rshrms.in</strong> using your registered email and default password <strong>Realty@123</strong>.
            </p>
            <a href="https://rshrms.in" target="_blank" rel="noopener" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px 20px; background: #15803D; color: #ffffff; font-weight: 700; font-size: 14px; border-radius: 12px; text-decoration: none; box-shadow: 0 4px 12px rgba(21, 128, 61, 0.25);">
                <i class="fas fa-external-link-alt"></i> Go to Realty Smartz HRMS (rshrms.in)
            </a>
        </div>

        <div class="support-text">
            Realty Smartz Pathshala Recruitment Cell &bull; All assessment logs are digitally stamped.
        </div>
    </div>
</body>
</html>

