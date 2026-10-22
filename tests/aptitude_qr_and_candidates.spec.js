const { test, expect } = require('@playwright/test');
const fs = require('fs');
const path = require('path');

const SCREENSHOT_DIR = path.resolve(__dirname, 'screenshots');
if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

async function loginAdmin(page) {
    await page.goto('/admin/login');
    await page.fill('input[name="email"]', 'admin@realtysmartzpathshala.in');
    await page.fill('input[name="password"]', 'Admin@2026');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin/dashboard');
}

test.describe('Aptitude QR Flow & Admin Aptitude Candidates', () => {

    test('End-to-End: QR Code 2 Candidate Registration, Test Taking, Scorecard PDF, Admin Verification, and Conversion', async ({ browser }) => {
        const uniqueEmail = `rohan.test.${Date.now()}@example.com`;
        const candidateName = 'Rohan Test Verma';
        const mobile = '98' + Math.floor(10000000 + Math.random() * 90000000);

        // -------------------------------------------------------------
        // Step 1: Candidate registers via /aptitude/register
        // -------------------------------------------------------------
        const candidateContext = await browser.newContext();
        const candidatePage = await candidateContext.newPage();

        await candidatePage.goto('/aptitude/register');
        await expect(candidatePage.locator('h1')).toContainText('Candidate Registration');
        await expect(candidatePage.locator('.brand-badge')).toContainText('Aptitude Assessment');

        await candidatePage.fill('input[name="full_name"]', candidateName);
        await candidatePage.fill('input[name="mobile"]', mobile);
        await candidatePage.fill('input[name="email"]', uniqueEmail);

        await candidatePage.click('button[type="submit"]');

        // Should be redirected straight to /dashboard/test (instructions screen)
        await candidatePage.waitForURL('**/dashboard/test');
        await expect(candidatePage.locator('body')).toContainText('Aptitude Assessment');

        // Start Assessment
        const startBtn = candidatePage.locator('button[type="submit"]:has-text("Start Assessment"), button:has-text("Start Assessment"), form button:has-text("Begin"), form button:has-text("Start")');
        await startBtn.first().click();

        // Assessment is in-progress
        await candidatePage.waitForSelector('.question-card, .test-container, #optionsContainer');
        
        // Select an option for question 1
        const firstOption = candidatePage.locator('.option-row, input[type="radio"]').first();
        if (await firstOption.isVisible()) {
            await firstOption.click();
        }

        // Open submit confirmation modal
        await candidatePage.evaluate(() => {
            if (typeof openSubmitModal === 'function') {
                openSubmitModal();
            } else {
                const b = document.querySelector('.btn-submit-test');
                if (b) b.click();
            }
        });

        // Confirm submission in modal
        const modalSubmitBtn = candidatePage.locator('#testSubmitForm button[type="submit"], .btn-modal-submit');
        await modalSubmitBtn.click();

        // Should land on test_result.php
        await candidatePage.waitForSelector('.result-card, .score-large, .status-badge');
        await expect(candidatePage.locator('.result-title')).toContainText('Assessment Result');

        // Check scorecard PDF download
        const scorecardLink = candidatePage.locator('a:has-text("Download Scorecard PDF")');
        await expect(scorecardLink).toBeVisible();

        const [download] = await Promise.all([
            candidatePage.waitForEvent('download'),
            scorecardLink.click()
        ]);
        const stream = await download.createReadStream();
        const chunks = [];
        for await (const chunk of stream) chunks.push(chunk);
        const pdfBuffer = Buffer.concat(chunks);

        expect(pdfBuffer.subarray(0, 4).toString()).toBe('%PDF');
        expect(pdfBuffer.length).toBeGreaterThan(5000);

        await candidatePage.screenshot({ path: path.join(SCREENSHOT_DIR, 'aptitude_result_screen.png') });
        await candidateContext.close();

        // -------------------------------------------------------------
        // Step 2: Admin checks /admin/aptitude-candidates
        // -------------------------------------------------------------
        const adminContext = await browser.newContext();
        const adminPage = await adminContext.newPage();

        await loginAdmin(adminPage);

        await adminPage.goto('/admin/aptitude-candidates');
        await expect(adminPage.locator('h2')).toContainText('Aptitude Test Candidates');

        // Verify Rohan is listed
        await expect(adminPage.locator('body')).toContainText(candidateName);
        await expect(adminPage.locator('body')).toContainText(uniqueEmail);

        // Verify Scorecard PDF button exists for Rohan
        const row = adminPage.locator(`tr:has-text("${candidateName}")`).first();
        const adminScorecardBtn = row.locator('a:has-text("Scorecard PDF")');
        await expect(adminScorecardBtn).toBeVisible();

        // Verify Upgrade to Onboarding button exists and click it
        const upgradeBtn = row.locator('button:has-text("Upgrade to Onboarding")');
        await expect(upgradeBtn).toBeVisible();

        // Handle browser confirm dialog
        adminPage.once('dialog', dialog => dialog.accept());
        await upgradeBtn.click();

        // After conversion, admin should be redirected to candidate profile page
        await adminPage.waitForURL(/\/admin\/candidates\/\d+/);
        await expect(adminPage.locator('body')).toContainText(candidateName);
        await expect(adminPage.locator('body')).toContainText('Personal details');

        await adminPage.screenshot({ path: path.join(SCREENSHOT_DIR, 'converted_candidate_profile.png') });

        // -------------------------------------------------------------
        // Step 3: Admin checks /admin/qr-codes
        // -------------------------------------------------------------
        await adminPage.goto('/admin/qr-codes');
        await expect(adminPage.locator('h2')).toContainText('QR Code Management');
        await expect(adminPage.locator('body')).toContainText('Candidate Onboarding Registration');
        await expect(adminPage.locator('body')).toContainText('Aptitude Test Assessment');

        // Check SVG elements are rendered
        const svgs = adminPage.locator('.qr-svg-wrapper svg');
        await expect(svgs).toHaveCount(2);

        // Test downloading PNG for Aptitude QR
        const [qrDownload] = await Promise.all([
            adminPage.waitForEvent('download'),
            adminPage.locator('a[href*="/download/aptitude"]').first().click()
        ]);
        const qrStream = await qrDownload.createReadStream();
        const qrChunks = [];
        for await (const chunk of qrStream) qrChunks.push(chunk);
        const qrBuffer = Buffer.concat(qrChunks);

        // Verify PNG magic header: \x89PNG
        expect(qrBuffer.subarray(0, 4).toString('hex')).toBe('89504e47');
        expect(qrBuffer.length).toBeGreaterThan(500);

        await adminPage.screenshot({ path: path.join(SCREENSHOT_DIR, 'admin_qr_management.png') });

        await adminContext.close();
    });
});
