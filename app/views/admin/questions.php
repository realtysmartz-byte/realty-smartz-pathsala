<?php
ob_start();
$pageTitle = "Question Bank & Assessment Manager | Admin Portal";
?>

<!-- Header -->
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
    <div>
        <h1 style="font-size:1.8rem; font-weight:800; color:var(--text, #2E2A24); font-family:var(--font-heading); margin-bottom:4px;">
            Assessment Question Bank &amp; Rules
        </h1>
        <p style="color:var(--muted, #7A7164); font-size:13.5px;">
            Manage evaluation modules, multiple-choice questions, and test configuration
        </p>
    </div>

    <button type="button" onclick="openQuestionModal()" class="btn-primary" style="padding:10px 20px; font-size:13.5px;">
        <i class="fas fa-plus"></i> Add New Question
    </button>
</div>

<!-- Test Rules Summary Bar -->
<div class="bento-card" style="padding:20px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
    <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
        <div>
            <div style="font-size:11px; text-transform:uppercase; color:var(--muted, #7A7164); font-weight:700;">Active Test</div>
            <div style="color:var(--text, #2E2A24); font-weight:700;"><?= e($testConfig['title'] ?? 'General Real Estate Aptitude') ?></div>
        </div>
        <div>
            <div style="font-size:11px; text-transform:uppercase; color:var(--muted, #7A7164); font-weight:700;">Duration</div>
            <div style="color:var(--brand, #9A6410); font-weight:700;"><?= (int)($testConfig['time_limit_minutes'] ?? 30) ?> Minutes</div>
        </div>
        <div>
            <div style="font-size:11px; text-transform:uppercase; color:var(--muted, #7A7164); font-weight:700;">Question Quota</div>
            <div style="color:var(--text, #2E2A24); font-weight:700;"><?= (int)($testConfig['total_questions'] ?? 25) ?> Random MCQs</div>
        </div>
        <div>
            <div style="font-size:11px; text-transform:uppercase; color:var(--muted, #7A7164); font-weight:700;">Passing Benchmark</div>
            <div style="color:var(--success, #2E7D5B); font-weight:700;"><?= (float)($testConfig['passing_percentage'] ?? 60) ?>%</div>
        </div>
        <div>
            <div style="font-size:11px; text-transform:uppercase; color:var(--muted, #7A7164); font-weight:700;">Negative Penalty</div>
            <div style="color:var(--warning, #C77700); font-weight:700;">-<?= (float)($testConfig['negative_marking_per_question'] ?? 0.25) ?> marks</div>
        </div>
    </div>
</div>

<!-- Question List Table -->
<div class="bento-card" style="padding:0; overflow:hidden;">
    <div style="padding:18px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
        <h3 style="font-size:1.1rem; color:var(--text, #2E2A24); font-weight:700;">
            Question Bank Inventory (<?= count($questions) ?> Questions)
        </h3>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13.5px; text-align:left;">
            <thead>
                <tr style="background:rgba(255,255,255,0.03); border-bottom:1px solid var(--card-border); color:var(--text-muted);">
                    <th style="padding:12px 20px;">#</th>
                    <th style="padding:12px 20px;">Category</th>
                    <th style="padding:12px 20px;">Question Content</th>
                    <th style="padding:12px 20px;">Marks</th>
                    <th style="padding:12px 20px;">Options</th>
                    <th style="padding:12px 20px; text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($questions)): ?>
                    <?php foreach ($questions as $idx => $q): ?>
                        <tr style="border-bottom:1px solid var(--border);">
                            <td style="padding:14px 20px; color:var(--muted);"><?= $idx + 1 ?></td>
                            <td style="padding:14px 20px;">
                                <span style="background:var(--brand-soft); color:var(--brand); border:1px solid var(--line); padding:3px 10px; border-radius:12px; font-size:11px; font-weight:700;">
                                    <?= e($q['category_name']) ?>
                                </span>
                            </td>
                            <td style="padding:14px 20px; color:var(--text); max-width:460px;">
                                <div style="line-height:1.4;"><?= e(mb_strimwidth($q['question_text'], 0, 110, '...')) ?></div>
                            </td>
                            <td style="padding:14px 20px;">
                                <span style="color:var(--success); font-weight:700;">+<?= (float)$q['marks'] ?></span> / 
                                <span style="color:var(--warning); font-weight:700;">-<?= (float)$q['negative_marks'] ?></span>
                            </td>
                            <td style="padding:14px 20px; color:var(--muted);">
                                <?= (int)$q['option_count'] ?> Choices
                            </td>
                            <td style="padding:14px 20px; text-align:right;">
                                <button type="button" onclick="editQuestion(<?= htmlspecialchars(json_encode($q), ENT_QUOTES, 'UTF-8') ?>)" class="btn-outline-gold" style="padding:5px 12px; font-size:12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="padding:40px; text-align:center; color:var(--muted);">
                            No assessment questions found in the bank. Click "Add New Question" above to seed.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Add / Edit Question -->
<div id="questionModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(46,42,36,0.5); backdrop-filter:blur(6px); align-items:center; justify-content:center; padding:20px;">
    <div class="bento-card" style="max-width:680px; width:100%; max-height:90vh; overflow-y:auto; background:var(--surface); border:1px solid var(--border); box-shadow:var(--shadow-lg); padding:32px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 id="modalTitle" style="font-size:1.3rem; color:var(--text); font-weight:800;">
                Add Assessment Question
            </h3>
            <button type="button" onclick="closeQuestionModal()" style="background:transparent; border:none; color:var(--muted); font-size:20px; cursor:pointer;">
                &times;
            </button>
        </div>

        <form action="<?= url('/admin/questions/save') ?>" method="POST" id="questionForm">
            <?= csrf_field() ?>
            <input type="hidden" name="question_id" id="q_id" value="">

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
                <div>
                    <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:6px;">Category / Domain</label>
                    <select name="category_id" id="q_category" required style="width:100%; padding:10px 14px; background:var(--surface-2); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:6px;">Marks Awarded</label>
                    <input type="number" name="marks" id="q_marks" value="1" min="1" max="10" required style="width:100%; padding:10px 14px; background:var(--surface-2); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:6px;">Question Statement</label>
                <textarea name="question_text" id="q_text" rows="3" required placeholder="Type the question statement clearly..." style="width:100%; padding:10px 14px; background:var(--surface-2); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;"></textarea>
            </div>

            <!-- Options (4 Choices) with Correct Radio -->
            <div style="margin-bottom:20px;">
                <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:10px;">
                    Answer Options (Select the correct choice radio button)
                </label>
                
                <div style="display:flex; flex-direction:column; gap:10px;" id="optionsBox">
                    <?php for ($i = 0; $i < 4; $i++): ?>
                        <div style="display:flex; align-items:center; gap:12px; background:var(--surface-2); border:1px solid var(--border); border-radius:8px; padding:8px 14px;">
                            <input type="radio" name="correct_option" value="<?= $i ?>" <?= $i === 0 ? 'checked' : '' ?> style="accent-color:var(--primary); width:18px; height:18px; cursor:pointer;" title="Mark as correct option">
                            <span style="font-weight:700; color:var(--primary); font-size:13px;"><?= chr(65 + $i) ?>.</span>
                            <input type="text" name="options[]" required placeholder="Option <?= chr(65 + $i) ?> text..." style="flex:1; background:transparent; border:none; color:var(--text); font-size:13.5px; outline:none;">
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:6px;">Explanation / Solution Rationale (Optional)</label>
                <textarea name="explanation" id="q_explanation" rows="2" placeholder="Explanation shown during review..." style="width:100%; padding:10px 14px; background:var(--surface-2); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13px;"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" onclick="closeQuestionModal()" class="btn-outline-gold" style="padding:10px 20px; font-size:13px;">
                    Cancel
                </button>
                <button type="submit" class="btn-primary" style="padding:10px 24px; font-size:13.5px; font-weight:700;">
                    Save Question
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openQuestionModal() {
    document.getElementById('modalTitle').innerText = 'Add Assessment Question';
    document.getElementById('q_id').value = '';
    document.getElementById('q_text').value = '';
    document.getElementById('q_marks').value = '1';
    document.getElementById('q_explanation').value = '';
    
    // Reset option inputs
    const optInputs = document.querySelectorAll('input[name="options[]"]');
    optInputs.forEach(i => i.value = '');
    
    const radios = document.querySelectorAll('input[name="correct_option"]');
    if (radios.length > 0) radios[0].checked = true;

    document.getElementById('questionModal').style.display = 'flex';
}

function closeQuestionModal() {
    document.getElementById('questionModal').style.display = 'none';
}

function editQuestion(q) {
    document.getElementById('modalTitle').innerText = 'Edit Question #' + q.id;
    document.getElementById('q_id').value = q.id;
    document.getElementById('q_category').value = q.category_id;
    document.getElementById('q_text').value = q.question_text;
    document.getElementById('q_marks').value = q.marks;
    document.getElementById('q_explanation').value = q.explanation || '';

    // Fetch question options via API or populate
    fetch('<?= url("/api/questions") ?>?category_id=' + q.category_id)
        .then(res => res.json())
        .then(data => {
            document.getElementById('questionModal').style.display = 'flex';
        })
        .catch(() => {
            document.getElementById('questionModal').style.display = 'flex';
        });
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/admin.php';
?>
