<?php
$attemptId = (int)$session['attempt']['id'];
$initialTime = (int)($timeRemaining ?? 600);
$totalQuestionsCount = count($questions);
$initialViolations = (int)($session['attempt']['tab_switch_count'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Aptitude Test</title>
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
            --surface-2: #EFEBE0;
            --border: #E2DDD0;
            --line: #E2DDD0;
            --text: #26332D;
            --muted: #6D766F;
            --brand: #1F5C4A;
            --brand-soft: #E2EEE8;
            --primary: #1F5C4A;
            --primary-soft: #E2EEE8;
            --gold: #C9A24A;
            --accent: #C77700;
            --danger: #C0392B;
            --success: #2E7D5B;
            --purple: #1F5C4A;
            --purple-soft: #E2EEE8;
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
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: var(--font-family);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            min-height: 100dvh;
            user-select: none;
            -webkit-user-select: none;
            display: flex;
            flex-direction: column;
        }

        /* 1. STICKY TOP BAR (height 56px, white, 1px bottom border) */
        .top-bar-wrap {
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .top-bar {
            height: 56px;
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .q-progress-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            white-space: nowrap;
        }

        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Questions Button (Grid Icon) */
        .palette-toggle-btn {
            height: 38px;
            padding: 0 10px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        @media (max-width: 400px) {
            .palette-toggle-btn {
                padding: 0;
                width: 38px;
                justify-content: center;
            }
            .palette-toggle-btn span {
                display: none;
            }
            .top-bar {
                padding: 0 12px;
            }
        }

        /* Timer Chip */
        .timer-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 38px;
            padding: 0 12px;
            border-radius: 12px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            font-size: 15px;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: var(--primary);
            transition: color 0.2s ease;
        }

        .timer-chip.timer-accent {
            color: var(--accent);
            border-color: rgba(245, 165, 36, 0.4);
        }

        .timer-chip.timer-danger {
            color: var(--danger);
            border-color: rgba(229, 72, 77, 0.4);
        }

        /* 4px Progress Bar */
        .progress-bar-track {
            height: 4px;
            width: 100%;
            background: var(--border);
        }

        .progress-bar-fill {
            height: 100%;
            width: 0%;
            background: var(--primary);
            transition: width 0.2s ease;
        }

        /* Main Content Layout */
        .test-container {
            max-width: 1100px;
            width: 100%;
            margin: 0 auto;
            padding: 20px 16px 96px 16px;
            flex: 1;
            display: flex;
            gap: 24px;
            align-items: flex-start;
        }

        .test-main-col {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* 2. QUESTION CARD (white, radius 20px, padding 20px) */
        .question-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 1px 2px rgba(30, 42, 68, 0.06);
        }

        .category-chip {
            display: inline-block;
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            background: var(--primary-soft);
            padding: 4px 10px;
            border-radius: var(--r-sm);
            margin-bottom: 14px;
        }

        .question-text {
            font-size: 17px;
            line-height: 1.5;
            font-weight: 400;
            color: var(--text);
            margin-bottom: 24px;
            word-break: break-word;
        }

        /* 3. OPTIONS: 4 full-width tappable rows, min-height 52px, 12px gap */
        .options-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .option-row {
            min-height: 52px;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            border-radius: var(--r-md);
            border: 1px solid var(--border);
            background: var(--surface);
            cursor: pointer;
            width: 100%;
            text-align: left;
            transition: background 0.15s ease, border-color 0.15s ease;
        }

        .option-row.selected {
            background: var(--primary-soft);
            border: 1.5px solid var(--primary);
        }

        .option-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 1px solid var(--border);
            background: var(--surface-2);
            color: var(--text);
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .option-row.selected .option-circle {
            background: var(--primary);
            color: #FFFFFF;
            border-color: var(--primary);
        }

        .option-text {
            font-size: 15px;
            font-weight: 500;
            line-height: 1.45;
            color: var(--text);
            flex: 1;
            word-break: break-word;
        }

        .option-radio {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            flex-shrink: 0;
            pointer-events: none;
        }

        /* 4. STICKY BOTTOM ACTION BAR (Mobile & Tablet) */
        .bottom-action-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 10px 16px;
            padding-bottom: calc(10px + env(safe-area-inset-bottom));
        }

        .action-row {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: nowrap;
        }

        .btn-prev {
            height: 44px;
            padding: 0 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text);
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .btn-prev:disabled {
            opacity: 0.35;
            cursor: not-allowed;
        }

        .btn-bookmark {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }

        .btn-bookmark.active {
            background: var(--primary-soft);
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-next {
            height: 44px;
            padding: 0 20px;
            border-radius: 12px;
            border: none;
            background: var(--primary);
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            flex-grow: 1;
            white-space: nowrap;
        }

        /* Desktop Controls (Inline under Options above 900px) */
        .desktop-action-bar {
            display: none;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid var(--border);
        }

        /* 5. QUESTION PALETTE CARD (Desktop Side Column) */
        .desktop-palette-col {
            display: none;
            width: 320px;
            flex-shrink: 0;
            position: sticky;
            top: 76px;
        }

        .palette-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 1px 2px rgba(30, 42, 68, 0.06);
        }

        .palette-grid {
            display: grid;
            grid-template-columns: repeat(5, 44px);
            gap: 8px;
            justify-content: center;
            margin: 16px 0;
        }

        .palette-btn {
            width: 44px;
            height: 44px;
            border-radius: var(--r-sm);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            background: var(--surface-2);
            color: var(--text);
            transition: all 0.1s ease;
        }

        .palette-btn.current {
            border: 2px solid var(--primary);
        }

        .palette-btn.answered {
            background: rgba(31, 169, 113, 0.14);
            color: var(--success);
            border-color: var(--success);
        }

        .palette-btn.marked {
            background: var(--purple-soft);
            color: var(--purple);
            border-color: var(--purple);
            box-shadow: 0 0 0 1.5px var(--purple);
        }

        .palette-legend {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        .legend-dot.ans { background: var(--success); }
        .legend-dot.rev { background: var(--purple); }
        .legend-dot.unans { background: var(--surface-2); border: 1px solid var(--border); }
        .legend-dot.cur { border: 2px solid var(--primary); }

        .btn-submit-test {
            width: 100%;
            height: 44px;
            border-radius: 12px;
            border: none;
            background: var(--primary);
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        /* 5. BOTTOM SHEET (Mobile / Tablet Palette) */
        .sheet-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 200;
            background: rgba(30, 42, 68, 0.4);
            align-items: flex-end;
        }

        .sheet-backdrop.open {
            display: flex;
        }

        .bottom-sheet {
            background: var(--surface);
            width: 100%;
            border-radius: 20px 20px 0 0;
            padding: 16px 20px calc(24px + env(safe-area-inset-bottom)) 20px;
            max-height: 85vh;
            overflow-y: auto;
            border-top: 1px solid var(--border);
        }

        .drag-handle {
            width: 40px;
            height: 4px;
            border-radius: 2px;
            background: var(--border);
            margin: 0 auto 16px auto;
        }

        /* 6. SUBMIT CONFIRMATION MODAL */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 300;
            background: rgba(30, 42, 68, 0.5);
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .modal-backdrop.open {
            display: flex;
        }

        .modal-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 24px;
            max-width: 360px;
            width: 100%;
            box-shadow: 0 1px 2px rgba(30, 42, 68, 0.06);
        }

        .modal-rows {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 24px;
            font-size: 15px;
            font-weight: 500;
        }

        .modal-row {
            display: flex;
            justify-content: space-between;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
        }

        .btn-modal-back {
            flex: 1;
            height: 44px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text);
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-modal-submit {
            flex: 1;
            height: 44px;
            border-radius: 12px;
            border: none;
            background: var(--primary);
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        /* Security Alert Modal */
        .alert-modal-card {
            background: var(--surface);
            border: 1px solid var(--danger);
            border-radius: 20px;
            padding: 24px;
            max-width: 360px;
            width: 100%;
            text-align: center;
        }

        /* DESKTOP MEDIA QUERY (above 900px) */
        @media (min-width: 900px) {
            .test-container {
                padding-bottom: 40px;
            }
            .desktop-palette-col {
                display: block;
            }
            .palette-toggle-btn {
                display: none;
            }
            .bottom-action-bar {
                display: none;
            }
            .desktop-action-bar {
                display: flex;
                align-items: center;
                gap: 12px;
            }
        }
    </style>
</head>
<body>

    <!-- 1. STICKY TOP BAR (height 56px, white, 1px bottom border) -->
    <header class="top-bar-wrap">
        <div class="top-bar">
            <div id="qCounter" class="q-progress-title">Question 1 of <?= $totalQuestionsCount ?></div>
            <div class="top-bar-right">
                <button type="button" class="palette-toggle-btn" onclick="openPaletteSheet()">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    <span>Questions</span>
                </button>
                <div id="timerChip" class="timer-chip">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span id="timerText">10:00</span>
                </div>
            </div>
        </div>
        <div class="progress-bar-track">
            <div id="progressBarFill" class="progress-bar-fill"></div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="test-container">
        
        <!-- Center Column: Question Card -->
        <div class="test-main-col">
            <div class="question-card">
                <div id="categoryChip" class="category-chip">Logical Reasoning</div>
                <div id="questionText" class="question-text">Loading question...</div>
                
                <!-- 3. OPTIONS: 4 full-width tappable rows -->
                <div id="optionsContainer" class="options-list">
                    <!-- Populated dynamically -->
                </div>

                <!-- Desktop Action Bar (Inline below options above 900px) -->
                <div class="desktop-action-bar">
                    <button type="button" id="prevBtnDesktop" onclick="navigateQuestion(-1)" class="btn-prev">
                        Previous
                    </button>
                    <button type="button" id="bookmarkBtnDesktop" onclick="toggleMarkReview()" class="btn-bookmark" aria-label="Mark for review">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                    </button>
                    <button type="button" id="nextBtnDesktop" onclick="navigateQuestion(1)" class="btn-next">
                        Save &amp; Next
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Desktop Palette Card (Sticky above 900px) -->
        <aside class="desktop-palette-col">
            <div class="palette-card">
                <div class="palette-legend">
                    <div class="legend-item"><span class="legend-dot ans"></span><span>Answered</span></div>
                    <div class="legend-item"><span class="legend-dot rev"></span><span>Marked</span></div>
                    <div class="legend-item"><span class="legend-dot unans"></span><span>Unanswered</span></div>
                    <div class="legend-item"><span class="legend-dot cur"></span><span>Current</span></div>
                </div>
                <div id="desktopPaletteGrid" class="palette-grid">
                    <!-- 15 buttons -->
                </div>
                <button type="button" onclick="openSubmitModal()" class="btn-submit-test">Submit Test</button>
            </div>
        </aside>

    </main>

    <!-- 4. STICKY BOTTOM ACTION BAR (Mobile & Tablet) -->
    <div class="bottom-action-bar">
        <div class="action-row">
            <button type="button" id="prevBtnMobile" onclick="navigateQuestion(-1)" class="btn-prev">
                Previous
            </button>
            <button type="button" id="bookmarkBtnMobile" onclick="toggleMarkReview()" class="btn-bookmark" aria-label="Mark for review">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
            </button>
            <button type="button" id="nextBtnMobile" onclick="navigateQuestion(1)" class="btn-next">
                Save &amp; Next
            </button>
        </div>
    </div>

    <!-- 5. QUESTION PALETTE BOTTOM SHEET (Mobile / Tablet) -->
    <div id="paletteSheet" class="sheet-backdrop" onclick="handleSheetBackdropClick(event)">
        <div class="bottom-sheet" onclick="event.stopPropagation()">
            <div class="drag-handle"></div>
            <div class="palette-legend">
                <div class="legend-item"><span class="legend-dot ans"></span><span>Answered</span></div>
                <div class="legend-item"><span class="legend-dot rev"></span><span>Marked</span></div>
                <div class="legend-item"><span class="legend-dot unans"></span><span>Unanswered</span></div>
                <div class="legend-item"><span class="legend-dot cur"></span><span>Current</span></div>
            </div>
            <div id="sheetPaletteGrid" class="palette-grid">
                <!-- 15 buttons -->
            </div>
            <button type="button" onclick="closePaletteSheet(); openSubmitModal();" class="btn-submit-test">Submit Test</button>
        </div>
    </div>

    <!-- 6. SUBMIT CONFIRMATION MODAL -->
    <div id="submitModal" class="modal-backdrop">
        <div class="modal-card">
            <div class="modal-rows">
                <div class="modal-row">
                    <span>Answered:</span>
                    <strong id="modalAnsCount" style="color:var(--success);">0</strong>
                </div>
                <div class="modal-row">
                    <span>Unanswered:</span>
                    <strong id="modalUnansCount" style="color:var(--muted);"><?= $totalQuestionsCount ?></strong>
                </div>
                <div class="modal-row">
                    <span>Marked for review:</span>
                    <strong id="modalRevCount" style="color:var(--purple);">0</strong>
                </div>
            </div>
            <form action="<?= url('/dashboard/test/submit') ?>" method="POST" id="testSubmitForm">
                <?= csrf_field() ?>
                <input type="hidden" name="attempt_id" value="<?= $attemptId ?>">
                <div class="modal-actions">
                    <button type="button" onclick="closeSubmitModal()" class="btn-modal-back">Go back</button>
                    <button type="submit" class="btn-modal-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Security Warning Modal -->
    <div id="securityModal" class="modal-backdrop">
        <div class="alert-modal-card">
            <div style="font-size:16px; font-weight:700; color:var(--danger); margin-bottom:12px;">Warning</div>
            <p id="securityModalText" style="font-size:14px; line-height:1.5; color:var(--text); margin-bottom:20px;">
                Leaving the assessment window is monitored.
            </p>
            <button type="button" id="dismissSecurityBtn" onclick="dismissSecurityModal()" class="btn-next" style="width:100%;">
                Return to Test
            </button>
        </div>
    </div>

    <!-- Script Logic -->
    <script>
    const testQuestions = <?= json_encode($questions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const attemptId = <?= $attemptId ?>;
    let timeRemaining = <?= $initialTime ?>;
    let currentIndex = 0;
    let violationCount = <?= $initialViolations ?>;
    const csrfToken = "<?= csrf_token() ?>";
    let isSubmitting = false;

    // Prevent copy, paste, contextmenu
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('copy', e => e.preventDefault());
    document.addEventListener('cut', e => e.preventDefault());
    document.addEventListener('paste', e => e.preventDefault());

    // Tab Switch / Focus Monitor
    let modalActive = false;
    document.addEventListener('visibilitychange', function() {
        if (document.hidden && !modalActive && !isSubmitting) {
            handleViolation('tab_switch');
        }
    });

    function handleViolation(type) {
        if (isSubmitting) return;
        violationCount++;

        fetch('<?= url("/dashboard/test/warning") ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                csrf_token: csrfToken,
                attempt_id: attemptId,
                type: type
            })
        }).catch(err => console.error(err));

        modalActive = true;
        const modal = document.getElementById('securityModal');
        const text = document.getElementById('securityModalText');
        const btn = document.getElementById('dismissSecurityBtn');

        if (violationCount >= 3) {
            text.innerText = "Maximum tab switches reached. Test is being submitted.";
            btn.style.display = 'none';
            modal.classList.add('open');
            isSubmitting = true;
            setTimeout(() => {
                document.getElementById('testSubmitForm').submit();
            }, 1200);
        } else {
            text.innerText = `Leaving the test window is recorded. Warning ${violationCount} of 3.`;
            modal.classList.add('open');
        }
    }

    function dismissSecurityModal() {
        document.getElementById('securityModal').classList.remove('open');
        modalActive = false;
    }

    // Timer Logic
    const timerElem = document.getElementById('timerText');
    const timerChip = document.getElementById('timerChip');
    const timerInterval = setInterval(function() {
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            timerElem.innerText = "00:00";
            timerChip.className = 'timer-chip timer-danger';
            isSubmitting = true;
            document.getElementById('testSubmitForm').submit();
            return;
        }

        timeRemaining--;
        const mins = Math.floor(timeRemaining / 60);
        const secs = timeRemaining % 60;
        timerElem.innerText = (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;

        if (timeRemaining < 60) {
            timerChip.className = 'timer-chip timer-danger';
        } else if (timeRemaining < 120) {
            timerChip.className = 'timer-chip timer-accent';
        } else {
            timerChip.className = 'timer-chip';
        }
    }, 1000);

    // Render Question
    function renderQuestion(index) {
        if (index < 0 || index >= testQuestions.length) return;
        currentIndex = index;
        const q = testQuestions[currentIndex];

        document.getElementById('qCounter').innerText = `Question ${currentIndex + 1} of ${testQuestions.length}`;
        document.getElementById('categoryChip').innerText = q.category_name || 'General';
        document.getElementById('questionText').innerHTML = escapeHtml(q.question_text);

        const container = document.getElementById('optionsContainer');
        container.innerHTML = '';
        const selectedId = q.selected_option_id ? parseInt(q.selected_option_id) : null;

        if (q.options && q.options.length > 0) {
            q.options.forEach((opt, idx) => {
                const isChecked = (selectedId === parseInt(opt.id));
                const letter = String.fromCharCode(65 + idx);

                const row = document.createElement('div');
                row.className = 'option-row' + (isChecked ? ' selected' : '');
                row.innerHTML = `
                    <div class="option-circle">${letter}</div>
                    <div class="option-text">${escapeHtml(opt.option_text)}</div>
                    <input type="radio" name="opt_radio" class="option-radio" ${isChecked ? 'checked' : ''}>
                `;
                row.onclick = () => selectOption(opt.id);
                container.appendChild(row);
            });
        }

        // Bookmark state
        const isMarked = !!q.is_marked_for_review;
        const bMobile = document.getElementById('bookmarkBtnMobile');
        const bDesktop = document.getElementById('bookmarkBtnDesktop');
        if (isMarked) {
            bMobile.classList.add('active');
            bDesktop.classList.add('active');
        } else {
            bMobile.classList.remove('active');
            bDesktop.classList.remove('active');
        }

        // Prev Buttons
        document.getElementById('prevBtnMobile').disabled = (currentIndex === 0);
        document.getElementById('prevBtnDesktop').disabled = (currentIndex === 0);

        // Next Buttons
        const nextLabel = (currentIndex === testQuestions.length - 1) ? 'Review & Submit' : 'Save & Next';
        document.getElementById('nextBtnMobile').innerText = nextLabel;
        document.getElementById('nextBtnDesktop').innerText = nextLabel;

        updateProgress();
        renderPaletteGrids();
    }

    function selectOption(optId) {
        const q = testQuestions[currentIndex];
        q.selected_option_id = parseInt(optId);
        saveAnswer(q.id, q.selected_option_id, q.is_marked_for_review);
        renderQuestion(currentIndex);
    }

    function toggleMarkReview() {
        const q = testQuestions[currentIndex];
        q.is_marked_for_review = !q.is_marked_for_review;
        saveAnswer(q.id, q.selected_option_id, q.is_marked_for_review);
        renderQuestion(currentIndex);
    }

    function navigateQuestion(direction) {
        const target = currentIndex + direction;
        if (target >= 0 && target < testQuestions.length) {
            renderQuestion(target);
        } else if (target >= testQuestions.length) {
            openSubmitModal();
        }
    }

    function saveAnswer(questionId, optionId, isMarked) {
        fetch('<?= url("/dashboard/test/answer") ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                csrf_token: csrfToken,
                attempt_id: attemptId,
                question_id: questionId,
                option_id: optionId || '',
                is_marked: isMarked ? 1 : 0
            })
        }).catch(err => console.error("Autosave error", err));
    }

    function updateProgress() {
        let answered = 0;
        testQuestions.forEach(q => {
            if (q.selected_option_id) answered++;
        });
        const pct = (answered / testQuestions.length) * 100;
        document.getElementById('progressBarFill').style.width = pct + '%';
    }

    function renderPaletteGrids() {
        const desktopGrid = document.getElementById('desktopPaletteGrid');
        const sheetGrid = document.getElementById('sheetPaletteGrid');
        desktopGrid.innerHTML = '';
        sheetGrid.innerHTML = '';

        testQuestions.forEach((q, idx) => {
            const isCurrent = (idx === currentIndex);
            const hasAns = !!q.selected_option_id;
            const isRev = !!q.is_marked_for_review;

            const btnDesktop = createPaletteButton(idx, isCurrent, hasAns, isRev, () => {
                renderQuestion(idx);
            });
            desktopGrid.appendChild(btnDesktop);

            const btnSheet = createPaletteButton(idx, isCurrent, hasAns, isRev, () => {
                renderQuestion(idx);
                closePaletteSheet();
            });
            sheetGrid.appendChild(btnSheet);
        });
    }

    function createPaletteButton(idx, isCurrent, hasAns, isRev, onClick) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.innerText = idx + 1;
        btn.className = 'palette-btn';

        if (hasAns) btn.classList.add('answered');
        if (isRev) btn.classList.add('marked');
        if (isCurrent) btn.classList.add('current');

        btn.onclick = onClick;
        return btn;
    }

    function openPaletteSheet() {
        document.getElementById('paletteSheet').classList.add('open');
    }

    function closePaletteSheet() {
        document.getElementById('paletteSheet').classList.remove('open');
    }

    function handleSheetBackdropClick(e) {
        if (e.target.id === 'paletteSheet') {
            closePaletteSheet();
        }
    }

    function openSubmitModal() {
        let answered = 0;
        let marked = 0;
        let unanswered = 0;

        testQuestions.forEach(q => {
            if (q.selected_option_id) answered++;
            else unanswered++;
            if (q.is_marked_for_review) marked++;
        });

        document.getElementById('modalAnsCount').innerText = answered;
        document.getElementById('modalUnansCount').innerText = unanswered;
        document.getElementById('modalRevCount').innerText = marked;

        document.getElementById('submitModal').classList.add('open');
    }

    function closeSubmitModal() {
        document.getElementById('submitModal').classList.remove('open');
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderQuestion(0);
    });
    </script>
</body>
</html>
