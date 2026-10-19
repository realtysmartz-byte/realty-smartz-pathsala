<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($pageTitle ?? 'Aptitude Assessment | Realty Smartz Pathshala') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #F7F5EF;
            --surface: #FFFFFF;
            --surface-2: #F0EDE4;
            --line: #E2DDD0;
            --brand: #1F5C4A;
            --brand-hover: #174739;
            --brand-soft: #E2EEE8;
            --text: #26332D;
            --muted: #6D766F;
            --danger: #A33A2A;
            --danger-soft: #FDEEED;
            --warning: #8C5311;
            --warning-soft: #FDF4E6;
            --r-sm: 8px;
            --r-md: 12px;
            --r-lg: 20px;
            --r-full: 999px;
            --font-family: 'DM Sans', system-ui, -apple-system, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-family);
            font-size: 15px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .guest-topbar {
            height: 64px;
            background: var(--surface);
            border-bottom: 1px solid var(--line);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .guest-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text);
            font-weight: 700;
            font-size: 16px;
        }

        .guest-brand img {
            height: 36px;
            width: auto;
            border-radius: var(--r-sm);
        }

        .guest-container {
            max-width: 800px;
            width: 100%;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
        }

        .guest-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            padding: 36px 32px;
            box-shadow: 0 8px 24px rgba(38, 51, 45, 0.04);
        }

        @media (max-width: 600px) {
            .guest-card {
                padding: 24px 20px;
            }
        }

        .guest-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.02em;
            margin-bottom: 8px;
        }

        .guest-desc {
            color: var(--muted);
            font-size: 14.5px;
            margin-bottom: 24px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            background: var(--surface);
            color: var(--text);
            font-size: 15px;
            outline: none;
            transition: border-color 0.15s ease;
        }

        .form-input:focus {
            border-color: var(--brand);
        }

        .btn-primary {
            height: 44px;
            padding: 0 24px;
            background: var(--brand);
            color: var(--surface);
            border: none;
            border-radius: var(--r-md);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: background 0.15s ease;
            width: 100%;
        }

        .btn-primary:hover {
            background: var(--brand-hover);
        }

        /* Spec Strip in Start Screen */
        .spec-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            background: var(--surface-2);
            margin-bottom: 28px;
            overflow: hidden;
        }

        @media (max-width: 500px) {
            .spec-strip {
                grid-template-columns: 1fr;
            }
        }

        .spec-item {
            padding: 16px;
            text-align: center;
            border-right: 1px solid var(--line);
        }

        .spec-item:last-child {
            border-right: none;
        }

        .spec-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted);
            margin-bottom: 4px;
        }

        .spec-val {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
        }

        /* Active Test Engine */
        .test-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .timer-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: var(--brand-soft);
            color: var(--brand);
            font-weight: 700;
            font-size: 18px;
            border-radius: var(--r-full);
            letter-spacing: 0.02em;
        }

        .timer-badge.warning {
            background: var(--danger-soft);
            color: var(--danger);
        }

        .question-card {
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            padding: 24px;
            background: var(--surface);
            margin-bottom: 24px;
        }

        .q-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: 14px;
        }

        .q-text {
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 20px;
            line-height: 1.45;
        }

        .options-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .option-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            cursor: pointer;
            transition: background 0.15s ease, border-color 0.15s ease;
        }

        .option-item:hover {
            background: var(--surface-2);
        }

        .option-item.selected {
            background: var(--brand-soft);
            border-color: var(--brand);
            font-weight: 600;
            color: var(--brand);
        }

        .option-radio {
            accent-color: var(--brand);
            width: 18px;
            height: 18px;
        }

        .q-nav-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 24px;
            justify-content: center;
        }

        .q-nav-btn {
            width: 36px;
            height: 36px;
            border-radius: var(--r-sm);
            border: 1px solid var(--line);
            background: var(--surface);
            color: var(--text);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .q-nav-btn.answered {
            background: var(--brand);
            color: var(--surface);
            border-color: var(--brand);
        }

        .q-nav-btn.active {
            outline: 2px solid var(--brand);
            outline-offset: 2px;
        }

        .test-footer-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .btn-nav {
            height: 44px;
            padding: 0 20px;
            background: var(--surface);
            color: var(--text);
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-nav:hover {
            background: var(--surface-2);
        }

        .btn-submit-test {
            height: 44px;
            padding: 0 24px;
            background: var(--brand);
            color: var(--surface);
            border: none;
            border-radius: var(--r-md);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit-test:hover {
            background: var(--brand-hover);
        }

        /* Result / Status Cards */
        .status-center {
            text-align: center;
            padding: 20px 0;
        }

        .status-badge-big {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: var(--r-full);
            margin-bottom: 16px;
        }

        .score-box {
            background: var(--surface-2);
            border: 1px solid var(--line);
            border-radius: var(--r-lg);
            padding: 24px;
            margin: 20px auto;
            max-width: 320px;
            text-align: center;
        }

        .score-number {
            font-size: 36px;
            font-weight: 800;
            color: var(--brand);
            letter-spacing: -0.03em;
        }

        .save-indicator {
            font-size: 12px;
            color: var(--muted);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
    </style>
</head>
<body>

    <!-- Simple Top Bar -->
    <header class="guest-topbar">
        <div class="guest-brand">
            <img src="<?= url('/assets/img/logo/Pathshala.png') ?>" alt="Realty Smartz Pathshala" width="36" height="36">
            <span>Realty Smartz Pathshala</span>
        </div>
        <div>
            <span style="font-size: 13px; font-weight: 600; color: var(--muted);">Aptitude Portal</span>
        </div>
    </header>

    <main class="guest-container">
        
        <!-- 1. IDENTIFY STATE (Open guest link) -->
        <?php if ($viewState === 'identify'): ?>
            <div class="guest-card">
                <h1 class="guest-title">Candidate Details</h1>
                <p class="guest-desc">Please enter your basic information before beginning the assessment.</p>

                <?php if ($flash = flash('error')): ?>
                    <div style="background: var(--danger-soft); color: var(--danger); padding: 12px 16px; border-radius: var(--r-md); font-size: 14px; margin-bottom: 20px;">
                        <?= e($flash) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/test/g/' . $token . '/identify') ?>">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label class="form-label" for="fullNameInput">Full Legal Name</label>
                        <input type="text" name="full_name" id="fullNameInput" class="form-input" required placeholder="Enter your full name">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="mobileInput">Mobile Number</label>
                        <input type="tel" name="mobile" id="mobileInput" class="form-input" required placeholder="10-digit mobile number">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="emailInput">Email Address</label>
                        <input type="email" name="email" id="emailInput" class="form-input" required placeholder="name@example.com">
                    </div>
                    <button type="submit" class="btn-primary" style="margin-top: 10px;">
                        <span>Continue to Assessment</span>
                    </button>
                </form>
            </div>

        <!-- 2. NOT STARTED STATE (Instructions & Start Button) -->
        <?php elseif ($viewState === 'not_started'): ?>
            <div class="guest-card">
                <h1 class="guest-title">Aptitude Assessment</h1>
                <p class="guest-desc">
                    Welcome, <strong><?= e($guestName) ?></strong>. Please review the assessment guidelines below.
                </p>

                <?php if ($flash = flash('error')): ?>
                    <div style="background: var(--danger-soft); color: var(--danger); padding: 12px 16px; border-radius: var(--r-md); font-size: 14px; margin-bottom: 20px;">
                        <?= e($flash) ?>
                    </div>
                <?php endif; ?>

                <div class="spec-strip">
                    <div class="spec-item">
                        <div class="spec-label">Questions</div>
                        <div class="spec-val">15</div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-label">Time Allowed</div>
                        <div class="spec-val">10 minutes</div>
                    </div>
                    <div class="spec-item">
                        <div class="spec-label">Passing Score</div>
                        <div class="spec-val">50%</div>
                    </div>
                </div>

                <div style="font-size: 14px; color: var(--text); margin-bottom: 28px; line-height: 1.6;">
                    <ul style="padding-left: 20px; display: flex; flex-direction: column; gap: 8px;">
                        <li>The assessment timer of <strong>10 minutes</strong> starts as soon as you click <strong>Start Test</strong>.</li>
                        <li>Your answers are automatically saved as you select them.</li>
                        <li>If you refresh or lose connectivity, you can reopen this same link to resume your session with the remaining time.</li>
                        <li>Once submitted or when the 10-minute timer expires, the test will automatically finalize.</li>
                    </ul>
                </div>

                <form method="POST" action="<?= url('/test/g/' . $token . '/start') ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-primary" aria-label="Start Test">
                        <span>Start Test</span>
                    </button>
                </form>
            </div>

        <!-- 3. IN PROGRESS STATE (Active Assessment Session) -->
        <?php elseif ($viewState === 'in_progress'): ?>
            <div class="guest-card">
                <div class="test-header-bar">
                    <div>
                        <div style="font-size: 13px; font-weight: 600; color: var(--muted); text-transform: uppercase;">
                            <?= e($attempt['set_code'] ?? 'Set A') ?> &bull; Aptitude Assessment
                        </div>
                        <div id="saveStatus" class="save-indicator">All answers saved</div>
                    </div>
                    <div class="timer-badge" id="timerBadge" role="timer" aria-live="polite">
                        <span id="timeRemainingDisplay">10:00</span>
                    </div>
                </div>

                <!-- Question Navigation Palette -->
                <div class="q-nav-grid" id="qNavGrid">
                    <?php foreach ($questions as $idx => $q): ?>
                        <button type="button" 
                                class="q-nav-btn <?= !empty($q['selected_option_id']) ? 'answered' : '' ?> <?= ($idx === 0) ? 'active' : '' ?>" 
                                id="navBtn_<?= $idx ?>" 
                                onclick="jumpToQuestion(<?= $idx ?>)"
                                aria-label="Go to question <?= $idx + 1 ?>">
                            <?= $idx + 1 ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Questions Container -->
                <div id="questionsContainer">
                    <?php foreach ($questions as $idx => $q): ?>
                        <div class="question-card" id="qCard_<?= $idx ?>" style="<?= ($idx !== 0) ? 'display:none;' : '' ?>">
                            <div class="q-meta">
                                <span>Question <?= $idx + 1 ?> of <?= count($questions) ?></span>
                                <span><?= e($q['category_name']) ?></span>
                            </div>

                            <div class="q-text"><?= nl2br(e($q['question_text'])) ?></div>

                            <div class="options-list">
                                <?php foreach ($q['options'] as $opt): ?>
                                    <?php $isChecked = ((int)($q['selected_option_id'] ?? 0) === (int)$opt['id']); ?>
                                    <label class="option-item <?= $isChecked ? 'selected' : '' ?>" id="optLabel_<?= $opt['id'] ?>">
                                        <input type="radio" 
                                               name="q_<?= $q['id'] ?>" 
                                               class="option-radio" 
                                               value="<?= $opt['id'] ?>" 
                                               <?= $isChecked ? 'checked' : '' ?>
                                               onchange="selectOption(<?= $q['id'] ?>, <?= $opt['id'] ?>, <?= $idx ?>)">
                                        <span><?= e($opt['option_text']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Footer Navigation and Submit -->
                <div class="test-footer-actions">
                    <button type="button" class="btn-nav" id="btnPrev" onclick="navigateQuestion(-1)" disabled aria-label="Previous question">
                        &larr; Previous
                    </button>
                    <button type="button" class="btn-nav" id="btnNext" onclick="navigateQuestion(1)" aria-label="Next question">
                        Next &rarr;
                    </button>
                    <form method="POST" id="submitTestForm" action="<?= url('/test/g/' . $token . '/submit') ?>" onsubmit="return confirm('Are you sure you want to submit your assessment?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-submit-test" aria-label="Submit assessment">
                            <span>Submit Assessment</span>
                        </button>
                    </form>
                </div>
            </div>

            <script>
            var totalQuestions = <?= count($questions) ?>;
            var currentQIndex = 0;
            var timeRemaining = <?= (int)$timeRemainingSeconds ?>;
            var token = '<?= e($token) ?>';
            var csrfToken = '<?= csrf_token() ?>';

            // Timer countdown
            var timerEl = document.getElementById('timeRemainingDisplay');
            var timerBadge = document.getElementById('timerBadge');

            function updateTimer() {
                if (timeRemaining <= 0) {
                    timerEl.textContent = '00:00';
                    document.getElementById('submitTestForm').submit();
                    return;
                }

                var mins = Math.floor(timeRemaining / 60);
                var secs = timeRemaining % 60;
                var str = (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
                timerEl.textContent = str;

                if (timeRemaining < 120) {
                    timerBadge.classList.add('warning');
                }

                timeRemaining--;
            }

            setInterval(updateTimer, 1000);
            updateTimer();

            function jumpToQuestion(idx) {
                document.getElementById('qCard_' + currentQIndex).style.display = 'none';
                document.getElementById('navBtn_' + currentQIndex).classList.remove('active');

                currentQIndex = idx;
                document.getElementById('qCard_' + currentQIndex).style.display = 'block';
                document.getElementById('navBtn_' + currentQIndex).classList.add('active');

                document.getElementById('btnPrev').disabled = (currentQIndex === 0);
                document.getElementById('btnNext').disabled = (currentQIndex === totalQuestions - 1);
            }

            function navigateQuestion(delta) {
                var newIdx = currentQIndex + delta;
                if (newIdx >= 0 && newIdx < totalQuestions) {
                    jumpToQuestion(newIdx);
                }
            }

            function selectOption(questionId, optionId, qIndex) {
                var card = document.getElementById('qCard_' + qIndex);
                var labels = card.querySelectorAll('.option-item');
                labels.forEach(l => l.classList.remove('selected'));
                document.getElementById('optLabel_' + optionId).classList.add('selected');

                document.getElementById('navBtn_' + qIndex).classList.add('answered');

                var statusEl = document.getElementById('saveStatus');
                statusEl.textContent = 'Saving answer...';

                var fd = new FormData();
                fd.append('_csrf_token', csrfToken);
                fd.append('question_id', questionId);
                fd.append('option_id', optionId);

                fetch('<?= url("/test/g/" . $token . "/answer") ?>', {
                    method: 'POST',
                    body: fd
                })
                .then(r => r.json())
                .then(data => {
                    if (data.expired) {
                        alert('Time limit expired. Submitting test.');
                        document.getElementById('submitTestForm').submit();
                    } else if (data.success) {
                        statusEl.textContent = 'All answers saved';
                    }
                })
                .catch(() => {
                    statusEl.textContent = 'Error saving answer';
                });
            }
            </script>

        <!-- 4. SUBMITTED STATE (Score Display) -->
        <?php elseif ($viewState === 'submitted'): ?>
            <div class="guest-card status-center">
                <div class="status-badge-big" style="background: var(--brand-soft); color: var(--brand); margin: 0 auto 16px auto;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <h1 class="guest-title">This test has already been submitted</h1>
                <p class="guest-desc">Your assessment has been recorded successfully.</p>

                <?php if ($attempt): ?>
                    <div class="score-box">
                        <div style="font-size: 13px; font-weight: 600; color: var(--muted); text-transform: uppercase; margin-bottom: 6px;">Your Final Score</div>
                        <div class="score-number"><?= (int)$attempt['score'] ?> / <?= (int)$attempt['total_questions'] ?></div>
                        <div style="font-size: 15px; font-weight: 600; color: var(--text); margin-top: 4px;">
                            <?= round($attempt['percentage']) ?>% &bull; 
                            <?php if (!empty($attempt['passed'])): ?>
                                <span style="color: var(--brand);">Passed</span>
                            <?php else: ?>
                                <span style="color: var(--danger);">Not passed</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div style="color: var(--muted); font-size: 13.5px; margin-top: 16px;">
                    Thank you for taking the Realty Smartz Pathshala assessment.
                </div>
            </div>

        <!-- 5. EXPIRED STATE -->
        <?php elseif ($viewState === 'expired'): ?>
            <div class="guest-card status-center">
                <div class="status-badge-big" style="background: var(--danger-soft); color: var(--danger); margin: 0 auto 16px auto;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <h1 class="guest-title">This link has expired</h1>
                <p class="guest-desc">The validity window for this assessment test link has ended.</p>
                <div style="color: var(--muted); font-size: 14px;">
                    Please contact HR or your coordinator to request a new link.
                </div>
            </div>

        <!-- 6. REVOKED STATE -->
        <?php elseif ($viewState === 'revoked'): ?>
            <div class="guest-card status-center">
                <div class="status-badge-big" style="background: var(--danger-soft); color: var(--danger); margin: 0 auto 16px auto;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </div>
                <h1 class="guest-title">This link is no longer active</h1>
                <p class="guest-desc">This guest test link has been revoked by an administrator.</p>
                <div style="color: var(--muted); font-size: 14px;">
                    Please contact HR or the administrator if you believe this is in error.
                </div>
            </div>
        <?php endif; ?>

    </main>

</body>
</html>
