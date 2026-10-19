<?php
ob_start();
$pageTitle = "Site Content & CMS Manager | Admin Portal";
?>

<!-- Header -->
<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:24px;">
    <div>
        <h1 style="font-size:1.8rem; font-weight:800; color:var(--text); font-family:var(--font-heading); margin-bottom:4px;">
            Site Content &amp; CMS Management
        </h1>
        <p style="color:var(--muted); font-size:13.5px;">
            Modify public brand attributes, company statistics, projects, blogs, videos, leads, and email delivery
        </p>
    </div>
</div>

<!-- Tabs Navigation -->
<div style="display:flex; gap:10px; margin-bottom:24px; border-bottom:1px solid var(--border); padding-bottom:12px; overflow-x:auto;">
    <button type="button" class="tab-btn active" onclick="switchContentTab('settingsTab', this)" style="padding:8px 18px; border-radius:20px; font-size:13px; font-weight:700; background:var(--primary-soft); border:1px solid var(--primary); color:var(--primary); cursor:pointer;">
        <i class="fas fa-sliders-h"></i> Brand &amp; Statistics
    </button>
    <button type="button" id="emailTabBtn" class="tab-btn" onclick="switchContentTab('emailTab', this)" style="padding:8px 18px; border-radius:20px; font-size:13px; font-weight:600; background:var(--surface); border:1px solid var(--border); color:var(--muted); cursor:pointer;">
        <i class="fas fa-envelope"></i> Email &amp; SMTP
    </button>
    <button type="button" class="tab-btn" onclick="switchContentTab('projectsTab', this)" style="padding:8px 18px; border-radius:20px; font-size:13px; font-weight:600; background:var(--surface); border:1px solid var(--border); color:var(--muted); cursor:pointer;">
        <i class="fas fa-city"></i> Projects (<?= count($projects) ?>)
    </button>
    <button type="button" class="tab-btn" onclick="switchContentTab('blogsTab', this)" style="padding:8px 18px; border-radius:20px; font-size:13px; font-weight:600; background:var(--surface); border:1px solid var(--border); color:var(--muted); cursor:pointer;">
        <i class="fas fa-newspaper"></i> Blog Posts (<?= count($blogs) ?>)
    </button>
    <button type="button" class="tab-btn" onclick="switchContentTab('videosTab', this)" style="padding:8px 18px; border-radius:20px; font-size:13px; font-weight:600; background:var(--surface); border:1px solid var(--border); color:var(--muted); cursor:pointer;">
        <i class="fab fa-youtube"></i> Training Videos (<?= count($videos) ?>)
    </button>
    <button type="button" class="tab-btn" onclick="switchContentTab('faqsTab', this)" style="padding:8px 18px; border-radius:20px; font-size:13px; font-weight:600; background:var(--surface); border:1px solid var(--border); color:var(--muted); cursor:pointer;">
        <i class="fas fa-question-circle"></i> FAQs (<?= count($faqs) ?>)
    </button>
    <button type="button" class="tab-btn" onclick="switchContentTab('enquiriesTab', this)" style="padding:8px 18px; border-radius:20px; font-size:13px; font-weight:600; background:var(--surface); border:1px solid var(--border); color:var(--muted); cursor:pointer;">
        <i class="fas fa-envelope-open-text"></i> Enquiries (<?= count($enquiries) ?>)
    </button>
</div>

<!-- Tab 1: Brand Attributes & Key Statistics -->
<div id="settingsTab" class="content-tab-pane">
    <form action="<?= url('/admin/content/settings') ?>" method="POST">
        <?= csrf_field() ?>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
            
            <!-- Brand & Contact -->
            <div class="bento-card" style="padding:24px;">
                <h3 style="font-size:1.15rem; color:var(--text); font-weight:700; margin-bottom:18px; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-building" style="color:var(--primary);"></i> Public Identity &amp; Contact Info
                </h3>

                <div style="display:flex; flex-direction:column; gap:14px;">
                    <div>
                        <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:4px;">Website Name</label>
                        <input type="text" name="settings[site_name]" value="<?= e($settings['site_name'] ?? 'Realty Smartz Pathshala') ?>" style="width:100%; padding:9px 12px; background:var(--surface); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                    </div>
                    <div>
                        <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:4px;">Tagline / Headline</label>
                        <input type="text" name="settings[site_tagline]" value="<?= e($settings['site_tagline'] ?? 'Master the Art of Real Estate') ?>" style="width:100%; padding:9px 12px; background:var(--surface); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                    </div>
                    <div>
                        <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:4px;">Official Phone</label>
                        <input type="text" name="settings[contact_phone]" value="<?= e($settings['contact_phone'] ?? '+91 99999 99999') ?>" style="width:100%; padding:9px 12px; background:var(--surface); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                    </div>
                    <div>
                        <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:4px;">Official Email</label>
                        <input type="email" name="settings[contact_email]" value="<?= e($settings['contact_email'] ?? 'info@realtysmartzpathshala.in') ?>" style="width:100%; padding:9px 12px; background:var(--surface); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                    </div>
                    <div>
                        <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:4px;">Gurgaon Head Office Address</label>
                        <textarea name="settings[contact_address]" rows="2" style="width:100%; padding:9px 12px; background:var(--surface); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;"><?= e($settings['contact_address'] ?? 'Golf Course Extension Road, Sector 65, Gurgaon, Haryana, India') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Verified Statistics -->
            <div class="bento-card" style="padding:24px;">
                <h3 style="font-size:1.15rem; color:var(--text); font-weight:700; margin-bottom:18px; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-chart-line" style="color:var(--primary);"></i> Core Track Record &amp; Metrics
                </h3>

                <div style="display:flex; flex-direction:column; gap:14px;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:4px;">Professionals Trained</label>
                            <input type="text" name="settings[stat_professionals_trained]" value="<?= e($settings['stat_professionals_trained'] ?? '500+') ?>" style="width:100%; padding:9px 12px; background:var(--surface); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                        </div>
                        <div>
                            <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:4px;">Live Projects</label>
                            <input type="text" name="settings[stat_live_projects]" value="<?= e($settings['stat_live_projects'] ?? '50+') ?>" style="width:100%; padding:9px 12px; background:var(--surface); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:4px;">Student Success Rating</label>
                            <input type="text" name="settings[stat_success_rate]" value="<?= e($settings['stat_success_rate'] ?? '4.9') ?>" style="width:100%; padding:9px 12px; background:var(--surface); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                        </div>
                        <div>
                            <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:4px;">Placement Percentage</label>
                            <input type="text" name="settings[stat_placement_rate]" value="<?= e($settings['stat_placement_rate'] ?? '98%') ?>" style="width:100%; padding:9px 12px; background:var(--surface); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:4px;">Years in Market</label>
                            <input type="text" name="settings[stat_years_active]" value="<?= e($settings['stat_years_active'] ?? '10+ Years') ?>" style="width:100%; padding:9px 12px; background:var(--surface); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                        </div>
                        <div>
                            <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:4px;">Avg Deal Closed</label>
                            <input type="text" name="settings[stat_avg_deal]" value="<?= e($settings['stat_avg_deal'] ?? '2.4 Cr') ?>" style="width:100%; padding:9px 12px; background:var(--surface); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                        </div>
                    </div>

                    <div>
                        <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:4px;">Loader Greeting Text</label>
                        <input type="text" name="settings[preloader_text]" value="<?= e($settings['preloader_text'] ?? 'Preparing Your Success Path...') ?>" style="width:100%; padding:9px 12px; background:var(--surface); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                    </div>
                </div>
            </div>

        </div>

        <div style="margin-top:24px; text-align:right;">
            <button type="submit" style="background:var(--primary); color:#ffffff; padding:12px 32px; border-radius:9999px; font-size:14px; font-weight:700; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(52,71,230,0.25);">
                <i class="fas fa-save"></i> Save Brand &amp; Statistics Settings
            </button>
        </div>
    </form>
</div>

<!-- Tab 2: Email & SMTP Deliverability -->
<div id="emailTab" class="content-tab-pane" style="display:none;">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
        
        <!-- Current SMTP Settings (Loaded strictly from .env) -->
        <div class="bento-card" style="padding:24px;">
            <h3 style="font-size:1.15rem; color:var(--text); font-weight:700; margin-bottom:8px; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-shield-alt" style="color:var(--primary);"></i> Active SMTP Configuration
            </h3>
            <p style="color:var(--muted); font-size:13px; margin-bottom:20px; line-height:1.5;">
                Read strictly from your environment variables (<code>.env</code>). Password is never stored in DB or committed to git.
            </p>

            <div style="display:flex; flex-direction:column; gap:12px; background:var(--surface-2); border:1px solid var(--border); border-radius:12px; padding:16px; font-size:13px;">
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:8px;">
                    <span style="color:var(--muted);">SMTP Host:</span>
                    <strong style="color:var(--text);"><?= e($_ENV['MAIL_HOST'] ?? 'mail.realtysmartzpathshala.in') ?></strong>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:8px;">
                    <span style="color:var(--muted);">Port &amp; Encryption:</span>
                    <strong style="color:var(--text);"><?= e($_ENV['MAIL_PORT'] ?? '465') ?> (<?= strtoupper(e($_ENV['MAIL_ENCRYPTION'] ?? 'ssl')) ?> / SMTPS)</strong>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:8px;">
                    <span style="color:var(--muted);">Username:</span>
                    <strong style="color:var(--text);"><?= e($_ENV['MAIL_USERNAME'] ?? 'info@realtysmartzpathshala.in') ?></strong>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:8px;">
                    <span style="color:var(--muted);">From Address:</span>
                    <strong style="color:var(--text);"><?= e($_ENV['MAIL_FROM_ADDRESS'] ?? 'info@realtysmartzpathshala.in') ?></strong>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:8px;">
                    <span style="color:var(--muted);">From Name:</span>
                    <strong style="color:var(--text);"><?= e($_ENV['MAIL_FROM_NAME'] ?? 'Realty Smartz Pathshala') ?></strong>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:8px;">
                    <span style="color:var(--muted);">Password Status:</span>
                    <?php if (!empty($_ENV['MAIL_PASSWORD'])): ?>
                        <span style="color:var(--success); font-weight:700;"><i class="fas fa-check-circle"></i> Configured in .env</span>
                    <?php else: ?>
                        <span style="color:var(--danger); font-weight:700;"><i class="fas fa-exclamation-triangle"></i> Not set in .env</span>
                    <?php endif; ?>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="color:var(--muted);">Audit &amp; Error Log:</span>
                    <code style="font-size:12px; background:var(--surface); border:1px solid var(--border); padding:2px 6px; border-radius:4px; color:var(--primary);">/storage/logs/mail.log</code>
                </div>
            </div>
        </div>

        <!-- Dispatch Test Email -->
        <div class="bento-card" style="padding:24px;">
            <h3 style="font-size:1.15rem; color:var(--text); font-weight:700; margin-bottom:8px; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-paper-plane" style="color:var(--primary);"></i> Send Test Email
            </h3>
            <p style="color:var(--muted); font-size:13px; margin-bottom:20px; line-height:1.5;">
                Send an immediate multipart diagnostic email via PHPMailer on Port 465 (SSL) to test connection and inbox delivery.
            </p>

            <form action="<?= url('/admin/email/test') ?>" method="POST" style="margin-bottom:20px;">
                <?= csrf_field() ?>
                <div style="margin-bottom:16px;">
                    <label style="display:block; color:var(--text); font-size:12.5px; font-weight:600; margin-bottom:6px;">
                        Recipient Email Address
                    </label>
                    <input type="email" name="test_email" required value="<?= e(auth()['email'] ?? 'admin@realtysmartzpathshala.in') ?>" placeholder="Enter recipient email" style="width:100%; padding:10px 14px; background:var(--surface); border:1px solid var(--border); border-radius:8px; color:var(--text); font-size:13.5px;">
                </div>

                <button type="submit" style="background:var(--primary); color:#ffffff; padding:11px 26px; border-radius:9999px; font-size:13.5px; font-weight:700; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(52,71,230,0.25); display:inline-flex; align-items:center; gap:8px;">
                    <i class="fas fa-paper-plane"></i> Send Test Email
                </button>
            </form>

            <div style="background:var(--primary-soft); border:1px solid rgba(52,71,230,0.2); border-radius:12px; padding:16px; font-size:12.5px; line-height:1.6; color:var(--text);">
                <strong style="color:var(--primary); display:block; margin-bottom:6px;">Deliverability Checklist (Anti-Spam):</strong>
                <ul style="margin:0; padding-left:18px; color:var(--text);">
                    <li><strong>cPanel SPF &amp; DKIM:</strong> Go to <em>cPanel &gt; Email Deliverability</em> and click <strong>Repair</strong> for both records.</li>
                    <li><strong>DMARC TXT Record:</strong> Add record name <code>_dmarc</code> with value <code>v=DMARC1; p=none; rua=mailto:info@realtysmartzpathshala.in</code>.</li>
                    <li><strong>Score Check:</strong> Send a test email to <a href="https://www.mail-tester.com" target="_blank" style="color:var(--primary); font-weight:600;">mail-tester.com</a> to verify a 10/10 deliverability rating.</li>
                </ul>
            </div>
        </div>

    </div>
</div>

<!-- Tab 3: Projects Overview -->
<div id="projectsTab" class="content-tab-pane" style="display:none;">
    <div class="bento-card" style="padding:0; overflow:hidden;">
        <div style="padding:18px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
            <h3 style="font-size:1.1rem; color:var(--text); font-weight:700;">
                Real Estate Portfolio Projects
            </h3>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:13.5px; text-align:left;">
                <thead>
                    <tr style="background:var(--surface-2); border-bottom:1px solid var(--border); color:var(--muted);">
                        <th style="padding:12px 20px;">Project Title</th>
                        <th style="padding:12px 20px;">Developer</th>
                        <th style="padding:12px 20px;">Location</th>
                        <th style="padding:12px 20px;">Price Range</th>
                        <th style="padding:12px 20px;">Category</th>
                        <th style="padding:12px 20px; text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $proj): ?>
                        <tr style="border-bottom:1px solid var(--border);">
                            <td style="padding:14px 20px; font-weight:700; color:var(--text);">
                                <?= e($proj['title']) ?>
                            </td>
                            <td style="padding:14px 20px; color:var(--muted);"><?= e($proj['developer']) ?></td>
                            <td style="padding:14px 20px; color:var(--text);"><?= e($proj['location']) ?></td>
                            <td style="padding:14px 20px; color:var(--primary); font-weight:700;"><?= e($proj['price_range']) ?></td>
                            <td style="padding:14px 20px;">
                                <span style="background:var(--primary-soft); color:var(--primary); padding:3px 10px; border-radius:12px; font-size:11px; font-weight:700; text-transform:uppercase;">
                                    <?= e($proj['category']) ?>
                                </span>
                            </td>
                            <td style="padding:14px 20px; text-align:right;">
                                <a href="<?= url('/projects/' . $proj['slug']) ?>" target="_blank" style="padding:5px 12px; font-size:12px; font-weight:600; color:var(--primary); background:var(--primary-soft); border-radius:8px; text-decoration:none;">
                                    View Live
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab 4: Blog Posts -->
<div id="blogsTab" class="content-tab-pane" style="display:none;">
    <div class="bento-card" style="padding:0; overflow:hidden;">
        <div style="padding:18px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
            <h3 style="font-size:1.1rem; color:var(--text); font-weight:700;">
                Articles &amp; Market Insights
            </h3>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:13.5px; text-align:left;">
                <thead>
                    <tr style="background:var(--surface-2); border-bottom:1px solid var(--border); color:var(--muted);">
                        <th style="padding:12px 20px;">Article Title</th>
                        <th style="padding:12px 20px;">Author</th>
                        <th style="padding:12px 20px;">Status</th>
                        <th style="padding:12px 20px;">Published Date</th>
                        <th style="padding:12px 20px; text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($blogs as $b): ?>
                        <tr style="border-bottom:1px solid var(--border);">
                            <td style="padding:14px 20px; font-weight:700; color:var(--text);">
                                <?= e($b['title']) ?>
                            </td>
                            <td style="padding:14px 20px; color:var(--muted);"><?= e($b['author_name']) ?></td>
                            <td style="padding:14px 20px;">
                                <span style="background:rgba(31,169,113,0.15); color:var(--success); padding:3px 10px; border-radius:12px; font-size:11px; font-weight:700; text-transform:uppercase;">
                                    <?= e($b['status']) ?>
                                </span>
                            </td>
                            <td style="padding:14px 20px; color:var(--muted);"><?= date('d M Y', strtotime($b['published_at'] ?? $b['created_at'])) ?></td>
                            <td style="padding:14px 20px; text-align:right;">
                                <a href="<?= url('/blog/' . $b['slug']) ?>" target="_blank" style="padding:5px 12px; font-size:12px; font-weight:600; color:var(--primary); background:var(--primary-soft); border-radius:8px; text-decoration:none;">
                                    View Live
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tab 5: Training Videos -->
<div id="videosTab" class="content-tab-pane" style="display:none;">
    <div class="bento-card" style="padding:24px;">
        <h3 style="font-size:1.15rem; color:var(--text); font-weight:700; margin-bottom:18px;">
            YouTube Channel &amp; Mentorship Videos
        </h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:18px;">
            <?php foreach ($videos as $v): ?>
                <div style="background:var(--surface-2); border:1px solid var(--border); border-radius:12px; padding:16px;">
                    <div style="position:relative; padding-bottom:56.25%; height:0; border-radius:8px; overflow:hidden; margin-bottom:12px;">
                        <iframe src="https://www.youtube.com/embed/<?= e($v['youtube_id']) ?>" style="position:absolute; top:0; left:0; width:100%; height:100%; border:none;" allowfullscreen></iframe>
                    </div>
                    <div style="font-size:14px; font-weight:700; color:var(--text);"><?= e($v['title']) ?></div>
                    <div style="font-size:12px; color:var(--muted); margin-top:4px;">ID: <?= e($v['youtube_id']) ?> &bull; Order: <?= (int)$v['order_num'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Tab 6: FAQs -->
<div id="faqsTab" class="content-tab-pane" style="display:none;">
    <div class="bento-card" style="padding:24px;">
        <h3 style="font-size:1.15rem; color:var(--text); font-weight:700; margin-bottom:18px;">
            Candidate &amp; Trainee FAQs
        </h3>
        <div style="display:flex; flex-direction:column; gap:14px;">
            <?php foreach ($faqs as $f): ?>
                <div style="background:var(--surface-2); border:1px solid var(--border); border-radius:10px; padding:16px;">
                    <div style="font-size:14.5px; font-weight:700; color:var(--text); margin-bottom:6px;">
                        <?= e($f['question']) ?>
                    </div>
                    <div style="font-size:13px; color:var(--muted); line-height:1.5;">
                        <?= nl2br(e($f['answer'])) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Tab 7: Enquiries Desk -->
<div id="enquiriesTab" class="content-tab-pane" style="display:none;">
    <div class="bento-card" style="padding:0; overflow:hidden;">
        <div style="padding:18px 24px; border-bottom:1px solid var(--border);">
            <h3 style="font-size:1.1rem; color:var(--text); font-weight:700;">
                Public Contact Leads &amp; Training Inquiries
            </h3>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:13px; text-align:left;">
                <thead>
                    <tr style="background:var(--surface-2); border-bottom:1px solid var(--border); color:var(--muted);">
                        <th style="padding:12px 16px;">Lead Name</th>
                        <th style="padding:12px 16px;">Contact</th>
                        <th style="padding:12px 16px;">Subject</th>
                        <th style="padding:12px 16px;">Message</th>
                        <th style="padding:12px 16px;">Received On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($enquiries)): ?>
                        <?php foreach ($enquiries as $enq): ?>
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:12px 16px; font-weight:700; color:var(--text);"><?= e($enq['name']) ?></td>
                                <td style="padding:12px 16px; color:var(--text);">
                                    <div><?= e($enq['email']) ?></div>
                                    <div style="color:var(--muted); font-size:11.5px;"><?= e($enq['phone']) ?></div>
                                </td>
                                <td style="padding:12px 16px; color:var(--primary); font-weight:600;"><?= e($enq['subject'] ?? 'General Enquiry') ?></td>
                                <td style="padding:12px 16px; color:var(--muted); max-width:300px;"><?= e($enq['message']) ?></td>
                                <td style="padding:12px 16px; color:var(--muted); font-size:12px;"><?= date('d M Y, h:i A', strtotime($enq['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="padding:30px; text-align:center; color:var(--muted);">
                                No public inquiries received yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function switchContentTab(tabId, btn) {
    document.querySelectorAll('.content-tab-pane').forEach(el => el.style.display = 'none');
    document.getElementById(tabId).style.display = 'block';

    document.querySelectorAll('.tab-btn').forEach(b => {
        b.style.background = 'var(--surface)';
        b.style.borderColor = 'var(--border)';
        b.style.color = 'var(--muted)';
        b.style.fontWeight = '600';
    });

    btn.style.background = 'var(--primary-soft)';
    btn.style.borderColor = 'var(--primary)';
    btn.style.color = 'var(--primary)';
    btn.style.fontWeight = '700';
}

if (window.location.hash === '#emailSettings' || window.location.hash === '#emailTab') {
    const emailBtn = document.getElementById('emailTabBtn');
    if (emailBtn) switchContentTab('emailTab', emailBtn);
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/admin.php';
?>
