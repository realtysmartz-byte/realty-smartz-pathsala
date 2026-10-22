<?php
ob_start();
$pageTitle = "Aptitude Candidates | Realty Smartz Pathshala";
$viewTitle = "Aptitude Candidates";

function buildAptitudeFilterUrl(array $overrides = []): string {
    $params = array_merge($_GET, $overrides);
    $filtered = array_filter($params, fn($v) => $v !== '' && $v !== null);
    return url('/admin/aptitude-candidates' . (!empty($filtered) ? '?' . http_build_query($filtered) : ''));
}
?>

<style>
    .apt-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .apt-title-group h2 {
        font-size: 24px;
        font-weight: 600;
        color: var(--text);
        letter-spacing: -0.02em;
        margin-bottom: 4px;
    }
    .apt-title-group p {
        font-size: 14px;
        color: var(--muted);
    }
    .apt-stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .apt-stat-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        padding: 20px;
    }
    .apt-stat-label {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 6px;
        font-weight: 500;
    }
    .apt-stat-val {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
    }
    .filter-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        padding: 16px 20px;
        margin-bottom: 24px;
    }
    .filter-form {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }
    .filter-input {
        height: 40px;
        padding: 0 14px;
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-family: inherit;
        font-size: 14px;
        color: var(--text);
        background: var(--surface);
        min-width: 240px;
        flex: 1;
    }
    .filter-input:focus, .filter-select:focus {
        outline: none;
        border-color: var(--brand);
    }
    .filter-select {
        height: 40px;
        padding: 0 12px;
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-family: inherit;
        font-size: 14px;
        color: var(--text);
        background: var(--surface);
    }
    .btn-filter-submit {
        height: 40px;
        padding: 0 16px;
        background: var(--brand);
        color: #FFFFFF;
        border: none;
        border-radius: var(--r-md);
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-filter-reset {
        height: 40px;
        padding: 0 14px;
        background: transparent;
        color: var(--muted);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .table-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        overflow: hidden;
    }
    .apt-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .apt-table th {
        background: var(--surface-2);
        padding: 14px 18px;
        font-size: 12.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--muted);
        border-bottom: 1px solid var(--line);
    }
    .apt-table td {
        padding: 16px 18px;
        font-size: 14px;
        color: var(--text);
        border-bottom: 1px solid var(--line);
        vertical-align: middle;
    }
    .apt-table tr:last-child td {
        border-bottom: none;
    }
    .apt-table tr:hover td {
        background: rgba(31, 92, 74, 0.02);
    }
    .cand-name {
        font-weight: 600;
        color: var(--text);
    }
    .cand-id {
        font-size: 12px;
        color: var(--muted);
        margin-top: 2px;
    }
    .badge-passed {
        display: inline-block;
        background: #E8F3EE;
        color: var(--brand);
        font-size: 12px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: var(--r-full);
    }
    .badge-failed {
        display: inline-block;
        background: #FDF2F2;
        color: #721C24;
        font-size: 12px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: var(--r-full);
    }
    .badge-pending {
        display: inline-block;
        background: var(--surface-2);
        color: var(--muted);
        font-size: 12px;
        font-weight: 500;
        padding: 3px 8px;
        border-radius: var(--r-full);
    }
    .action-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .btn-action-pdf {
        height: 34px;
        padding: 0 12px;
        background: var(--brand-soft);
        color: var(--brand);
        border: 1px solid #C1E0D2;
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-action-pdf:hover {
        background: #D8ECDF;
    }
    .btn-action-upgrade {
        height: 34px;
        padding: 0 12px;
        background: transparent;
        color: var(--text);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-action-upgrade:hover {
        background: var(--surface-2);
        border-color: var(--muted);
    }
    .pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-top: 1px solid var(--line);
        font-size: 13px;
        color: var(--muted);
    }
    .pagination-links {
        display: flex;
        gap: 6px;
    }
    .page-btn {
        height: 32px;
        min-width: 32px;
        padding: 0 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--line);
        border-radius: var(--r-sm);
        text-decoration: none;
        color: var(--text);
        font-size: 13px;
    }
    .page-btn.active {
        background: var(--brand);
        color: #FFFFFF;
        border-color: var(--brand);
    }
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: var(--muted);
        font-size: 15px;
    }
    @media (max-width: 768px) {
        .apt-table {
            display: none;
        }
        .mobile-card-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 16px;
        }
        .mobile-item-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--r-md);
            padding: 16px;
        }
    }
    @media (min-width: 769px) {
        .mobile-card-list {
            display: none;
        }
    }
</style>

<div class="apt-header">
    <div class="apt-title-group">
        <h2>Aptitude Test Candidates</h2>
        <p>Candidates who registered exclusively for the aptitude test assessment.</p>
    </div>
    <div>
        <a href="<?= url('/admin/qr-codes') ?>" class="btn-filter-submit" aria-label="Manage QR Codes">
            <svg width="18" height="18" aria-hidden="true"><use href="#icon-award"></use></svg>
            <span>View Test QR Code</span>
        </a>
    </div>
</div>

<div class="apt-stats-row">
    <div class="apt-stat-card">
        <div class="apt-stat-label">Total Aptitude-Only Candidates</div>
        <div class="apt-stat-val"><?= number_format($statTotal) ?></div>
    </div>
    <div class="apt-stat-card">
        <div class="apt-stat-label">Assessments Completed</div>
        <div class="apt-stat-val"><?= number_format($statTaken) ?></div>
    </div>
</div>

<div class="filter-card">
    <form action="<?= url('/admin/aptitude-candidates') ?>" method="GET" class="filter-form" role="search">
        <input type="text" name="q" class="filter-input" placeholder="Search name, email, mobile..." value="<?= e($search) ?>" aria-label="Search aptitude candidates">

        <select name="test_status" class="filter-select" aria-label="Filter by test status">
            <option value="">All Test Statuses</option>
            <option value="taken" <?= ($testStatus === 'taken') ? 'selected' : '' ?>>Test Taken</option>
            <option value="not_taken" <?= ($testStatus === 'not_taken') ? 'selected' : '' ?>>Not Taken</option>
        </select>

        <select name="date_range" class="filter-select" aria-label="Filter by registration date">
            <option value="">All Time</option>
            <option value="today" <?= ($dateRange === 'today') ? 'selected' : '' ?>>Today</option>
            <option value="7days" <?= ($dateRange === '7days') ? 'selected' : '' ?>>Last 7 Days</option>
            <option value="30days" <?= ($dateRange === '30days') ? 'selected' : '' ?>>Last 30 Days</option>
        </select>

        <button type="submit" class="btn-filter-submit" aria-label="Apply filters">Filter</button>
        <?php if (!empty($search) || !empty($testStatus) || !empty($dateRange)): ?>
            <a href="<?= url('/admin/aptitude-candidates') ?>" class="btn-filter-reset" aria-label="Reset filters">Reset</a>
        <?php endif; ?>
    </form>
</div>

<div class="table-card">
    <?php if (empty($candidates)): ?>
        <div class="empty-state">No aptitude test candidates found.</div>
    <?php else: ?>
        <table class="apt-table" aria-label="Aptitude test candidates table">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Contact</th>
                    <th>Registered</th>
                    <th>Assessment Status</th>
                    <th>Score</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($candidates as $c): ?>
                    <tr>
                        <td>
                            <div class="cand-name"><?= e($c['full_name']) ?></div>
                            <div class="cand-id">ID #<?= str_pad((string)$c['id'], 4, '0', STR_PAD_LEFT) ?></div>
                        </td>
                        <td>
                            <div><?= e($c['mobile']) ?></div>
                            <div style="font-size: 13px; color: var(--muted);"><?= e($c['email']) ?></div>
                        </td>
                        <td>
                            <time datetime="<?= $c['created_at'] ?>"><?= date('M j, Y', strtotime($c['created_at'])) ?></time>
                            <div style="font-size: 12px; color: var(--muted);"><?= date('h:i A', strtotime($c['created_at'])) ?></div>
                        </td>
                        <td>
                            <?php if ($c['test_score'] !== null): ?>
                                <?php if (!empty($c['test_passed'])): ?>
                                    <span class="badge-passed">Passed</span>
                                <?php else: ?>
                                    <span class="badge-failed">Failed</span>
                                <?php endif; ?>
                                <span style="font-size: 12px; color: var(--muted); margin-left: 4px;"><?= e($c['set_name'] ?? 'Set A') ?></span>
                            <?php else: ?>
                                <span class="badge-pending">Not Taken</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($c['test_score'] !== null): ?>
                                <strong><?= (int)$c['test_score'] ?> / <?= (int)$c['test_total'] ?></strong>
                                <span style="font-size: 13px; color: var(--muted); margin-left: 4px;">(<?= round((float)$c['test_percentage']) ?>%)</span>
                            <?php else: ?>
                                <span style="color: var(--muted);">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-group" style="justify-content: flex-end;">
                                <?php if (!empty($c['attempt_id'])): ?>
                                    <a href="<?= url('/dashboard/test/scorecard/' . $c['attempt_id']) ?>" class="btn-action-pdf" aria-label="Download scorecard for <?= e($c['full_name']) ?>">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                                        <span>Scorecard PDF</span>
                                    </a>
                                <?php endif; ?>

                                <form action="<?= url('/admin/aptitude-candidates/' . $c['id'] . '/convert') ?>" method="POST" onsubmit="return confirm('Upgrade <?= e($c['full_name']) ?> to full onboarding profile?');" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-action-upgrade" aria-label="Upgrade <?= e($c['full_name']) ?> to onboarding">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M8.5 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8zM20 8v6M23 11h-6"/></svg>
                                        <span>Upgrade to Onboarding</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Mobile Stacked Card View -->
        <div class="mobile-card-list">
            <?php foreach ($candidates as $c): ?>
                <div class="mobile-item-card">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px;">
                        <div>
                            <div class="cand-name"><?= e($c['full_name']) ?></div>
                            <div class="cand-id">ID #<?= str_pad((string)$c['id'], 4, '0', STR_PAD_LEFT) ?> &bull; <?= date('M j, Y', strtotime($c['created_at'])) ?></div>
                        </div>
                        <div>
                            <?php if ($c['test_score'] !== null): ?>
                                <?php if (!empty($c['test_passed'])): ?>
                                    <span class="badge-passed">Passed</span>
                                <?php else: ?>
                                    <span class="badge-failed">Failed</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge-pending">Not Taken</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div style="font-size:13px; color:var(--muted); margin-bottom:12px;">
                        <div>Phone: <a href="tel:<?= e($c['mobile']) ?>" style="color:var(--text); text-decoration:none;"><?= e($c['mobile']) ?></a></div>
                        <div>Email: <?= e($c['email']) ?></div>
                        <?php if ($c['test_score'] !== null): ?>
                            <div style="margin-top:4px;">Score: <strong><?= (int)$c['test_score'] ?> / <?= (int)$c['test_total'] ?></strong> (<?= round((float)$c['test_percentage']) ?>%) &bull; <?= e($c['set_name'] ?? 'Set A') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="action-group">
                        <?php if (!empty($c['attempt_id'])): ?>
                            <a href="<?= url('/dashboard/test/scorecard/' . $c['attempt_id']) ?>" class="btn-action-pdf" aria-label="Download scorecard for <?= e($c['full_name']) ?>">Scorecard PDF</a>
                        <?php endif; ?>
                        <form action="<?= url('/admin/aptitude-candidates/' . $c['id'] . '/convert') ?>" method="POST" onsubmit="return confirm('Upgrade to full onboarding?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-action-upgrade" aria-label="Upgrade <?= e($c['full_name']) ?>">Upgrade to Onboarding</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination-bar">
                <div>Showing <?= count($candidates) ?> of <?= $totalCandidates ?> candidate(s)</div>
                <div class="pagination-links">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <a href="<?= buildAptitudeFilterUrl(['page' => $p]) ?>" class="page-btn <?= ($p === $page) ? 'active' : '' ?>" aria-label="Page <?= $p ?>">
                            <?= $p ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/admin.php';
?>
