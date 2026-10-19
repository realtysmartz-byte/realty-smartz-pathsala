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

test.describe('Guest Test Link End-to-End', () => {

    test('Guest test lifecycle: creation, open flow, different sets, mid-test refresh, submission, security checks', async ({ browser }) => {
        // 1. Admin creates an open link
        const adminContext = await browser.newContext();
        const adminPage = await adminContext.newPage();
        await loginAdmin(adminPage);

        await adminPage.goto('/admin/assessments');
        await adminPage.waitForLoadState('networkidle');

        // Get CSRF token from admin page
        const csrfToken = await adminPage.evaluate(() => {
            const input = document.querySelector('input[name="_csrf_token"]') || document.querySelector('input[name="csrf_token"]');
            return input ? input.value : '';
        });

        // Create open guest link via AJAX
        const createRes = await adminPage.request.post('/admin/guest-links/create', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            form: {
                _csrf_token: csrfToken,
                link_kind: 'open',
                guest_name: '',
                guest_mobile: '',
                guest_email: '',
                expiry_hours: '48'
            }
        });
        const createData = await createRes.json();
        expect(createData.success).toBeTruthy();
        expect(createData.token).toBeDefined();
        expect(createData.token.length).toBeGreaterThanOrEqual(32);

        const guestUrl = createData.url;
        const guestToken = createData.token;

        // 2. Open FRESH browser context with NO login cookies
        const guestContext = await browser.newContext();
        const guestPage = await guestContext.newPage();

        // Security check: guest session cannot open /admin or /dashboard
        await guestPage.goto('/admin/dashboard');
        await expect(guestPage).toHaveURL(/.*login.*/);

        await guestPage.goto('/dashboard');
        await expect(guestPage).toHaveURL(/.*login.*/);

        // Wrong token returns 404
        const badTokenRes = await guestPage.goto('/test/g/0000000000000000000000000000000000000000000000000000000000000000');
        expect(badTokenRes.status()).toBe(404);

        // Open legitimate guest test link
        await guestPage.goto(guestUrl);
        await guestPage.waitForLoadState('networkidle');

        // Open guest link prompts for identification
        await expect(guestPage.locator('#fullNameInput')).toBeVisible();
        await guestPage.fill('#fullNameInput', 'Vikram Malhotra');
        await guestPage.fill('#mobileInput', '9876500011');
        await guestPage.fill('#emailInput', 'vikram.guest@example.com');
        await guestPage.click('button[type="submit"]');
        await guestPage.waitForLoadState('networkidle');

        // Verify Start screen: 15 questions, 10 minutes, passing score 50%
        await expect(guestPage.locator('.guest-title')).toContainText('Aptitude Assessment');
        await expect(guestPage.locator('.spec-strip')).toContainText('15');
        await expect(guestPage.locator('.spec-strip')).toContainText('10 minutes');
        await expect(guestPage.locator('.spec-strip')).toContainText('50%');

        await guestPage.screenshot({ path: path.join(SCREENSHOT_DIR, 'guest_start_screen.png') });

        // Start Test
        await guestPage.click('button:has-text("Start Test")');
        await guestPage.waitForLoadState('networkidle');

        // Active test screen: verify 10-minute timer and questions
        await expect(guestPage.locator('#timeRemainingDisplay')).toBeVisible();

        // Answer Question 1 (autosave)
        const firstOption = guestPage.locator('.option-item').first();
        await firstOption.click();
        await guestPage.waitForTimeout(500);

        // Verify question palette button turns answered
        await expect(guestPage.locator('#navBtn_0')).toHaveClass(/answered/);

        // Mid-test refresh: reload page and verify resume with remaining time
        await guestPage.reload();
        await guestPage.waitForLoadState('networkidle');

        await expect(guestPage.locator('#timeRemainingDisplay')).toBeVisible();
        // Option 1 should still be checked/selected
        await expect(guestPage.locator('.option-item.selected')).toHaveCount(1);

        // Submit the test
        await guestPage.evaluate(() => {
            window.confirm = () => true;
        });
        await guestPage.click('button:has-text("Submit Assessment")');
        await guestPage.waitForLoadState('networkidle');

        // Result screen with score
        await expect(guestPage.locator('.guest-title')).toContainText('This test has already been submitted');
        await expect(guestPage.locator('.score-number')).toBeVisible();

        await guestPage.screenshot({ path: path.join(SCREENSHOT_DIR, 'guest_result_screen.png') });

        // Reopening link: says "This test has already been submitted"
        await guestPage.goto(guestUrl);
        await guestPage.waitForLoadState('networkidle');
        await expect(guestPage.locator('.guest-title')).toContainText('This test has already been submitted');

        // Verify guest candidate appears in admin Candidates list with "Guest" chip
        await adminPage.goto('/admin/candidates');
        await adminPage.waitForLoadState('networkidle');
        await expect(adminPage.locator('body')).toContainText('Vikram Malhotra');
        await expect(adminPage.locator('.guest-chip, .chip:has-text("Guest")').first()).toBeVisible();

        // Test Revoked link behavior
        // Revoke the link via admin
        await adminPage.request.post(`/admin/guest-links/${createData.link_id}/revoke`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            form: {
                _csrf_token: csrfToken
            }
        });

        const guestRevokedRes = await guestPage.goto(guestUrl);
        await guestPage.waitForLoadState('networkidle');
        // If revoked, shows "This link is no longer active"
        await expect(guestPage.locator('body')).toContainText('This link is no longer active');

        await adminContext.close();
        await guestContext.close();
    });
});
