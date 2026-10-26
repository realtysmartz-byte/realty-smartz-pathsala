<?php
ob_start();
$pageTitle = "QR Code Management | Realty Smartz Pathshala";
$viewTitle = "QR Codes";
?>

<style>
    .qr-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .qr-title-group h2 {
        font-size: 24px;
        font-weight: 600;
        color: var(--text);
        letter-spacing: -0.02em;
        margin-bottom: 4px;
    }
    .qr-title-group p {
        font-size: 14px;
        color: var(--muted);
    }
    .btn-print {
        height: 40px;
        padding: 0 18px;
        background: var(--brand);
        color: #FFFFFF;
        border: none;
        border-radius: var(--r-md);
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.15s ease;
    }
    .btn-print:hover {
        background: var(--brand-hover);
    }
    .qr-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    @media (max-width: 480px) {
        .qr-grid {
            grid-template-columns: 1fr;
        }
    }
    .qr-card {
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        padding: 28px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .qr-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--brand-soft);
        color: var(--brand);
        font-size: 12.5px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: var(--r-full);
        margin-bottom: 12px;
        align-self: flex-start;
    }
    .qr-card h3 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 8px;
    }
    .qr-desc {
        font-size: 14px;
        color: var(--muted);
        line-height: 1.5;
        margin-bottom: 20px;
    }
    .qr-preview-box {
        background: #FFFFFF;
        border: 1px solid var(--line);
        border-radius: var(--r-lg);
        padding: 24px 20px 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    }
    .qr-svg-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 220px;
        height: 220px;
        background: #FFFFFF;
        padding: 10px;
        border-radius: var(--r-md);
        border: 1px solid #E5E7EB;
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.02);
    }
    .qr-svg-wrapper svg {
        width: 100%;
        height: 100%;
        display: block;
    }
    .qr-scan-pill {
        margin-top: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #065F46;
        background: #ECFDF5;
        border: 1px solid #A7F3D0;
        padding: 3px 10px;
        border-radius: var(--r-full);
    }
    .qr-target-url {
        margin-top: 10px;
        font-family: monospace;
        font-size: 12px;
        color: var(--muted);
        background: var(--surface-2);
        padding: 6px 12px;
        border-radius: var(--r-sm);
        word-break: break-all;
        text-align: center;
        max-width: 100%;
    }
    .qr-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .qr-btn-primary {
        height: 42px;
        background: var(--brand);
        color: #FFFFFF;
        border: none;
        border-radius: var(--r-md);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .qr-btn-primary:hover {
        background: var(--brand-hover);
    }
    .qr-btn-group-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .qr-btn-secondary {
        height: 38px;
        background: transparent;
        color: var(--text);
        border: 1px solid var(--line);
        border-radius: var(--r-md);
        font-size: 13.5px;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .qr-btn-secondary:hover {
        background: var(--surface-2);
    }

    /* Print flyer layout */
    @media print {
        body {
            background: #FFFFFF !important;
            color: #000000 !important;
        }
        .sidebar, .topbar, .qr-header, .qr-actions, .btn-print, .qr-target-url {
            display: none !important;
        }
        .page {
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
        }
        .qr-grid {
            grid-template-columns: 1fr 1fr !important;
            gap: 20px !important;
            page-break-inside: avoid;
        }
        .qr-card {
            border: 2px solid #1F5C4A !important;
            padding: 24px !important;
            box-shadow: none !important;
        }
        .qr-svg-wrapper {
            width: 250px !important;
            height: 250px !important;
            margin: 0 auto 12px !important;
        }
        .print-only-header {
            display: block !important;
            text-align: center;
            margin-bottom: 24px;
        }
    }
    @media screen {
        .print-only-header {
            display: none;
        }
    }
</style>

<div class="print-only-header">
    <h1 style="font-size: 26px; color: #1F5C4A; margin-bottom: 4px;">Realty Smartz Pathshala</h1>
    <p style="font-size: 14px; color: #555555;">Reception Desk &bull; Candidate Self-Service Desk</p>
</div>

<div class="qr-header">
    <div class="qr-title-group">
        <h2>QR Code Management</h2>
        <p>Universal high-contrast QR codes for candidate onboarding, aptitude tests, and interview forms. Guaranteed scannable via any Phone Camera &amp; Google Lens.</p>
    </div>
    <div>
        <button type="button" class="btn-print" onclick="window.print()" aria-label="Print reception desk flyers">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            <span>Print Desk Flyers</span>
        </button>
    </div>
</div>

<div class="qr-grid">
    <!-- QR Code 1: Candidate Onboarding -->
    <div class="qr-card">
        <div>
            <div class="qr-badge">QR Code 1 &bull; Full Onboarding</div>
            <h3>Candidate Onboarding Registration</h3>
            <p class="qr-desc">For candidates completing standard joining onboarding: registration, email OTP verification, Aadhaar/PAN/Bank details, and document upload.</p>

            <div class="qr-preview-box">
                <div class="qr-svg-wrapper">
                    <?= $onboardingSvg ?>
                </div>
                <div class="qr-scan-pill">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Camera &amp; Google Lens Ready</span>
                </div>
                <div class="qr-target-url" id="onboardingUrlText"><?= e($onboardingUrl) ?></div>
            </div>
        </div>

        <div class="qr-actions">
            <div class="qr-btn-group-row">
                <a href="<?= url('/admin/qr-codes/download/onboarding') ?>" class="qr-btn-primary" aria-label="Download Onboarding QR Code PNG">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    <span>Download PNG</span>
                </a>
                <a href="<?= url('/admin/qr-codes/download/onboarding-svg') ?>" class="qr-btn-primary" style="background:var(--brand-soft); color:var(--brand); border:1px solid var(--brand);" aria-label="Download Onboarding QR Code SVG">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    <span>Download SVG</span>
                </a>
            </div>
            <div class="qr-btn-group-row">
                <button type="button" class="qr-btn-secondary" onclick="copyQrUrl('<?= e($onboardingUrl) ?>', this)" aria-label="Copy onboarding registration link">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    <span>Copy Link</span>
                </button>
                <a href="<?= e($onboardingUrl) ?>" target="_blank" rel="noopener noreferrer" class="qr-btn-secondary" aria-label="Open onboarding registration page">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    <span>Open Link</span>
                </a>
            </div>
        </div>
    </div>

    <!-- QR Code 2: Aptitude Test Only -->
    <div class="qr-card">
        <div>
            <div class="qr-badge">QR Code 2 &bull; Aptitude Only</div>
            <h3>Aptitude Test Assessment</h3>
            <p class="qr-desc">For walk-in candidates and preliminary assessments: candidates enter Name, Mobile, and Email to take the 15-question aptitude test immediately without documents.</p>

            <div class="qr-preview-box">
                <div class="qr-svg-wrapper">
                    <?= $aptitudeSvg ?>
                </div>
                <div class="qr-scan-pill">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Camera &amp; Google Lens Ready</span>
                </div>
                <div class="qr-target-url" id="aptitudeUrlText"><?= e($aptitudeUrl) ?></div>
            </div>
        </div>

        <div class="qr-actions">
            <div class="qr-btn-group-row">
                <a href="<?= url('/admin/qr-codes/download/aptitude') ?>" class="qr-btn-primary" aria-label="Download Aptitude QR Code PNG">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    <span>Download PNG</span>
                </a>
                <a href="<?= url('/admin/qr-codes/download/aptitude-svg') ?>" class="qr-btn-primary" style="background:var(--brand-soft); color:var(--brand); border:1px solid var(--brand);" aria-label="Download Aptitude QR Code SVG">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    <span>Download SVG</span>
                </a>
            </div>
            <div class="qr-btn-group-row">
                <button type="button" class="qr-btn-secondary" onclick="copyQrUrl('<?= e($aptitudeUrl) ?>', this)" aria-label="Copy aptitude assessment link">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    <span>Copy Link</span>
                </button>
                <a href="<?= e($aptitudeUrl) ?>" target="_blank" rel="noopener noreferrer" class="qr-btn-secondary" aria-label="Open aptitude assessment page">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    <span>Open Link</span>
                </a>
            </div>
        </div>
    </div>
    <!-- QR Code 3: Interview Application Form -->
    <div class="qr-card">
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <div class="qr-badge" style="margin-bottom:0;">Interview Application Form</div>
                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                    <span>Accepting responses</span>
                    <input type="checkbox" id="toggleAcceptingResponses" <?= !empty($interviewQr['accepting_responses']) ? 'checked' : '' ?> onchange="toggleAccepting(this.checked)" style="width: 18px; height: 18px; accent-color: var(--brand); cursor: pointer;">
                </label>
            </div>
            <h3>Interview Application Form</h3>
            <p class="qr-desc">Official candidate form reachable strictly via QR code at reception. Allows candidates to fill their resume, experience, and particulars.</p>

            <div class="qr-preview-box">
                <div class="qr-svg-wrapper">
                    <?= $interviewSvg ?>
                </div>
                <div class="qr-scan-pill">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <span>Camera &amp; Google Lens Ready</span>
                </div>
                <div class="qr-target-url" id="interviewUrlText"><?= e($interviewUrl) ?></div>
            </div>
        </div>

        <div class="qr-actions">
            <div class="qr-btn-group-row">
                <a href="<?= url('/admin/qr-codes/download/interview') ?>" class="qr-btn-primary" aria-label="Download Interview Form QR Code PNG">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    <span>Download PNG</span>
                </a>
                <a href="<?= url('/admin/qr-codes/download/interview-svg') ?>" class="qr-btn-primary" style="background:var(--brand-soft); color:var(--brand); border:1px solid var(--brand);" aria-label="Download Interview Form QR Code SVG">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    <span>Download SVG</span>
                </a>
            </div>
            <div class="qr-btn-group-row">
                <button type="button" class="qr-btn-secondary" onclick="copyQrUrl('<?= e($interviewUrl) ?>', this)" aria-label="Copy interview form link">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                    <span>Copy Link</span>
                </button>
                <a href="<?= e($interviewUrl) ?>" target="_blank" rel="noopener noreferrer" class="qr-btn-secondary" aria-label="Open interview form">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                    <span>Open Link</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyQrUrl(text, btn) {
    navigator.clipboard.writeText(text).then(function() {
        const origText = btn.innerHTML;
        btn.innerHTML = '<span>Copied!</span>';
        setTimeout(() => { btn.innerHTML = origText; }, 2000);
    }).catch(function() {
        prompt('Copy this link:', text);
    });
}

async function toggleAccepting(checked) {
    try {
        const fd = new FormData();
        fd.append('accepting', checked ? '1' : '0');
        fd.append('csrf_token', '<?= csrf_token() ?>');

        const res = await fetch('<?= url('/admin/interview/toggle-accepting') ?>', {
            method: 'POST',
            body: fd
        });
        const json = await res.json();
        if (!json.success) {
            alert('Failed to update response acceptance state.');
            document.getElementById('toggleAcceptingResponses').checked = !checked;
        }
    } catch(e) {
        alert('Connection error while updating response acceptance state.');
        document.getElementById('toggleAcceptingResponses').checked = !checked;
    }
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/views/layouts/admin.php';
?>
