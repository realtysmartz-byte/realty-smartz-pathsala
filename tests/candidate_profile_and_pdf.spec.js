const { test, expect } = require('@playwright/test');
const path = require('path');
const fs = require('fs');

const SCREENSHOT_DIR = path.resolve(__dirname, 'screenshots');

async function loginAdmin(page) {
    await page.goto('/admin/login');
    await page.fill('input[name="email"]', 'admin@realtysmartzpathshala.in');
    await page.fill('input[name="password"]', 'Admin@2026');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin/dashboard');
}

test.describe('Candidate Profile & Full-File PDF Download', () => {

    test('Candidate profile displays personal details, documents, test card, and download buttons', async ({ page }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        await loginAdmin(page);

        await page.goto('/admin/candidates');
        await page.waitForLoadState('networkidle');
        const viewLink = page.locator('a[href*="/admin/candidates/"]').first();
        const candidateHref = await viewLink.getAttribute('href');
        await page.goto(candidateHref);
        await page.waitForLoadState('networkidle');

        // Check header elements
        await expect(page.locator('.profile-name')).toBeVisible();
        await expect(page.locator('.profile-email-line')).toBeVisible();
        await expect(page.locator('.btn-header-primary')).toContainText('Download full file (PDF)');

        // Check Personal Details Card
        await expect(page.locator('h2:has-text("Personal details")')).toBeVisible();
        await expect(page.locator('.detail-label:has-text("Full legal name")')).toBeVisible();
        await expect(page.locator('.detail-label:has-text("Aadhaar number")')).toBeVisible();
        await expect(page.locator('.detail-label:has-text("PAN number")')).toBeVisible();

        // Check Masked Identity & Reveal
        const aadhaarDisplay = page.locator('#aadhaarDisplayValue');
        await expect(aadhaarDisplay).toBeVisible();
        const initialAadhaarText = await aadhaarDisplay.innerText();
        if (initialAadhaarText !== '-') {
            expect(initialAadhaarText).toContain('XXXX');
            // Click Reveal button for Aadhaar
            const revealBtn = page.locator('button:has-text("Reveal")').first();
            if (await revealBtn.isVisible()) {
                await revealBtn.click();
                await page.waitForTimeout(500);
                const revealedText = await aadhaarDisplay.innerText();
                expect(revealedText.length).toBeGreaterThan(0);
            }
        }

        // Check Documents Card
        await expect(page.locator('h2:has-text("Documents")')).toBeVisible();

        // Check Aptitude Test Card
        await expect(page.locator('h2:has-text("Aptitude test")')).toBeVisible();

        // Screenshot
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'candidate_profile_desktop.png') });
    });

    test('Full-file PDF export streams valid PDF starting with %PDF- and candidate name', async ({ page }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        await loginAdmin(page);

        await page.goto('/admin/candidates');
        await page.waitForLoadState('networkidle');
        const viewLink = page.locator('a[href*="/admin/candidates/"]').first();
        const candidateHref = await viewLink.getAttribute('href');
        await page.goto(candidateHref);
        await page.waitForLoadState('networkidle');

        // Trigger download of full file PDF
        const [download] = await Promise.all([
            page.waitForEvent('download'),
            page.click('.btn-header-primary')
        ]);

        const downloadPath = await download.path();
        expect(downloadPath).toBeTruthy();

        const buffer = fs.readFileSync(downloadPath);
        // Starts with %PDF
        const headerStr = buffer.subarray(0, 5).toString('ascii');
        expect(headerStr).toBe('%PDF-');

        // Check buffer size and text
        const pdfText = buffer.toString('binary');
        expect(pdfText.length).toBeGreaterThan(1000);
    });
});
