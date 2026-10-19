<?php
ob_start();
$pageTitle = "System Audit Trail | Admin Portal";
$totalPages = max(1, ceil($total / $perPage));
?>

<!-- Header -->
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
    <div>
        <h1 style="font-size:1.8rem; font-weight:800; color:var(--text); font-family:var(--font-heading); margin-bottom:4px;">
            Compliance &amp; System Audit Trail
        </h1>
        <p style="color:var(--muted); font-size:13.5px;">
            Immutable ledger tracking all portal security events, document operations, and status changes
        </p>
    </div>
    <div style="font-size:13px; color:var(--muted); background:var(--surface); border:1px solid var(--border); padding:8px 16px; border-radius:20px;">
        Total Logged Events: <strong style="color:var(--primary);"><?= number_format($total) ?></strong>
    </div>
</div>

<!-- Logs Table -->
<div class="bento-card" style="padding:0; overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px; text-align:left;">
            <thead>
                <tr style="background:var(--surface-2); border-bottom:1px solid var(--border); color:var(--muted);">
                    <th style="padding:14px 18px;">Timestamp</th>
                    <th style="padding:14px 18px;">Event Action</th>
                    <th style="padding:14px 18px;">Target Entity</th>
                    <th style="padding:14px 18px;">Actor</th>
                    <th style="padding:14px 18px;">Client IP</th>
                    <th style="padding:14px 18px;">Payload / Metadata</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                        <tr style="border-bottom:1px solid var(--border);">
                            <td style="padding:14px 18px; color:var(--muted); white-space:nowrap;">
                                <?= date('d M Y, h:i:s A', strtotime($log['created_at'])) ?>
                            </td>

                            <td style="padding:14px 18px;">
                                <span style="background:var(--primary-soft); border:1px solid rgba(52,71,230,0.2); color:var(--primary); padding:3px 10px; border-radius:12px; font-size:11px; font-weight:700; text-transform:uppercase;">
                                    <?= e($log['action']) ?>
                                </span>
                            </td>

                            <td style="padding:14px 18px; color:var(--text); font-weight:600;">
                                <?= e($log['entity_type'] ?? 'system') ?>
                                <?php if (!empty($log['entity_id'])): ?>
                                    <span style="color:var(--muted); font-size:11.5px;">#<?= $log['entity_id'] ?></span>
                                <?php endif; ?>
                            </td>

                            <td style="padding:14px 18px; color:var(--text);">
                                <?php if (!empty($log['user_name'])): ?>
                                    <div style="font-weight:600;"><?= e($log['user_name']) ?></div>
                                    <div style="font-size:11px; color:var(--muted);"><?= e($log['user_email']) ?></div>
                                <?php else: ?>
                                    <span style="color:var(--muted);">System / Guest</span>
                                <?php endif; ?>
                            </td>

                            <td style="padding:14px 18px; font-family:monospace; color:var(--muted); font-size:12px;">
                                <?= e($log['ip_address'] ?? '127.0.0.1') ?>
                            </td>

                            <td style="padding:14px 18px; max-width:320px;">
                                <?php 
                                $details = [];
                                if (!empty($log['old_values'])) {
                                    $details['old'] = json_decode($log['old_values'], true);
                                }
                                if (!empty($log['new_values'])) {
                                    $details['new'] = json_decode($log['new_values'], true);
                                }
                                ?>
                                <?php if (!empty($details)): ?>
                                    <details style="cursor:pointer;">
                                        <summary style="color:var(--primary); font-size:12px; font-weight:600; outline:none;">
                                            Inspect Payload
                                        </summary>
                                        <pre style="background:var(--surface-2); border:1px solid var(--border); padding:8px; border-radius:6px; font-size:11px; color:var(--text); overflow-x:auto; margin-top:6px; max-height:160px; font-family:monospace;"><?= htmlspecialchars(json_encode($details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) ?></pre>
                                    </details>
                                <?php else: ?>
                                    <span style="color:var(--muted); font-size:11.5px;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="padding:40px; text-align:center; color:var(--muted);">
                            No audit trail entries recorded yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div style="padding:16px 20px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div style="font-size:12.5px; color:var(--muted);">
                Page <strong><?= $page ?></strong> of <strong><?= $totalPages ?></strong>
            </div>
            <div style="display:flex; gap:6px;">
                <?php if ($page > 1): ?>
                    <a href="<?= url('/admin/audit-logs?page=' . ($page - 1)) ?>" style="padding:5px 12px; font-size:12px; font-weight:600; color:var(--primary); background:var(--primary-soft); border-radius:8px; text-decoration:none;">&larr; Prev</a>
                <?php endif; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="<?= url('/admin/audit-logs?page=' . ($page + 1)) ?>" style="padding:5px 12px; font-size:12px; font-weight:600; color:var(--primary); background:var(--primary-soft); border-radius:8px; text-decoration:none;">Next &rarr;</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/admin.php';
?>
