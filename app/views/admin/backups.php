<?php
ob_start();
$pageTitle = "Database Backups & Recovery | Admin Portal";
?>

<!-- Header -->
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
    <div>
        <h1 style="font-size:1.8rem; font-weight:800; color:var(--text); font-family:var(--font-heading); margin-bottom:4px;">
            Database Backups &amp; Disaster Recovery
        </h1>
        <p style="color:var(--muted); font-size:13.5px;">
            Automated database dumps, transaction-safe compressed archives, and snapshot restoration
        </p>
    </div>

    <form action="<?= url('/admin/backups/create') ?>" method="POST" onsubmit="return confirm('Generate a complete database snapshot now?');">
        <?= csrf_field() ?>
        <button type="submit" style="background:var(--primary); color:#ffffff; padding:10px 22px; border-radius:9999px; font-size:13.5px; font-weight:700; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(52,71,230,0.25); display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-database"></i> Generate Live Snapshot
        </button>
    </form>
</div>

<!-- Data Protection Notice -->
<div class="bento-card" style="padding:22px; margin-bottom:24px; background:var(--primary-soft); border:1px solid rgba(52,71,230,0.2);">
    <div style="display:flex; align-items:flex-start; gap:16px;">
        <div style="width:40px; height:40px; border-radius:10px; background:var(--surface); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; color:var(--primary); font-size:18px; flex-shrink:0;">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div>
            <h4 style="font-size:1rem; color:var(--text); font-weight:700; margin-bottom:4px;">
                Enterprise Data Safety &amp; Non-Destructive Architecture
            </h4>
            <p style="color:var(--muted); font-size:13px; line-height:1.6; margin:0;">
                All candidate onboarding records, documents, test evaluations, and audit logs are protected with soft-delete architectures and MySQL triggers. No production data is ever deleted. In addition to manual snapshots, database dumps can be automatically scheduled via cPanel cron.
            </p>
        </div>
    </div>
</div>

<!-- Backups Archive Table -->
<div class="bento-card" style="padding:0; overflow:hidden; margin-bottom:24px;">
    <div style="padding:18px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
        <h3 style="font-size:1.1rem; color:var(--text); font-weight:700;">
            Available Snapshot Archives (<?= count($backups) ?>)
        </h3>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13.5px; text-align:left;">
            <thead>
                <tr style="background:var(--surface-2); border-bottom:1px solid var(--border); color:var(--muted);">
                    <th style="padding:14px 20px;">Archive Filename</th>
                    <th style="padding:14px 20px;">File Size</th>
                    <th style="padding:14px 20px;">Snapshot Created At</th>
                    <th style="padding:14px 20px; text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($backups)): ?>
                    <?php foreach ($backups as $bk): ?>
                        <tr style="border-bottom:1px solid var(--border);">
                            <td style="padding:14px 20px; font-weight:700; color:var(--text); font-family:monospace;">
                                <i class="fas fa-file-archive" style="color:var(--primary); margin-right:8px;"></i>
                                <?= e($bk['filename']) ?>
                            </td>
                            <td style="padding:14px 20px; color:var(--muted);">
                                <?= e($bk['size']) ?>
                            </td>
                            <td style="padding:14px 20px; color:var(--text);">
                                <?= date('d M Y, h:i:s A', strtotime($bk['created_at'])) ?>
                            </td>
                            <td style="padding:14px 20px; text-align:right;">
                                <a href="<?= url('/admin/backups/download/' . urlencode($bk['filename'])) ?>" style="padding:6px 14px; font-size:12.5px; font-weight:600; color:var(--primary); background:var(--primary-soft); border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                                    <i class="fas fa-download"></i> Download Archive
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="padding:40px; text-align:center; color:var(--muted);">
                            No database backups found in <code style="color:var(--primary);">storage/backups</code>. Click "Generate Live Snapshot" above to create one.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- cPanel Automated Cron Instruction -->
<div class="bento-card" style="padding:24px;">
    <h3 style="font-size:1.1rem; color:var(--text); font-weight:700; margin-bottom:12px; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-terminal" style="color:var(--primary);"></i> Automated Daily Cron Setup in cPanel
    </h3>
    <p style="color:var(--muted); font-size:13px; margin-bottom:14px; line-height:1.5;">
        To automate nightly database backups at 02:00 AM, navigate to <strong>cPanel &gt; Cron Jobs</strong> and add the following command:
    </p>

    <div style="background:var(--surface-2); border:1px solid var(--border); border-radius:8px; padding:12px 16px; font-family:monospace; font-size:12.5px; color:var(--success); font-weight:600; display:flex; justify-content:space-between; align-items:center; overflow-x:auto;">
        <span>0 2 * * * /usr/local/bin/php /home/USERNAME/pathshala_app/bin/backup.php &gt; /dev/null 2&gt;&amp;1</span>
    </div>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/admin.php';
?>
