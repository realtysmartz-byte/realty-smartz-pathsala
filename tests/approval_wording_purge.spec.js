const { test, expect } = require('@playwright/test');

async function loginAdmin(page) {
    await page.goto('/admin/login');
    await page.fill('input[name="email"]', 'admin@realtysmartzpathshala.in');
    await page.fill('input[name="password"]', 'Admin@2026');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin/dashboard');
}

const FORBIDDEN_WORDS = [
    'Approve',
    'Reject',
    'Request Changes',
    'Approved',
    'Pending review',
    'Awaiting review',
    'Under review',
    'Request re-upload'
];

test.describe('Purge of Approval Flow and Wording', () => {

    test('Admin Dashboard has zero approval wording or review queues', async ({ page }) => {
        await loginAdmin(page);
        await page.goto('/admin/dashboard');
        await page.waitForLoadState('networkidle');

        const bodyText = await page.locator('body').innerText();
        for (const word of FORBIDDEN_WORDS) {
            expect(bodyText).not.toContain(word);
        }
    });

    test('Admin Candidates Directory has zero approval wording or filter options', async ({ page }) => {
        await loginAdmin(page);
        await page.goto('/admin/candidates');
        await page.waitForLoadState('networkidle');

        const bodyText = await page.locator('body').innerText();
        for (const word of FORBIDDEN_WORDS) {
            expect(bodyText).not.toContain(word);
        }
    });

    test('Admin Candidate Profile has zero approval/reject buttons or decision steps', async ({ page }) => {
        await loginAdmin(page);
        await page.goto('/admin/candidates/20'); // existing candidate
        await page.waitForLoadState('networkidle');

        const bodyText = await page.locator('body').innerText();
        for (const word of FORBIDDEN_WORDS) {
            expect(bodyText).not.toContain(word);
        }

        // Verify no approve or reject button
        const approveBtn = page.locator('button:has-text("Approve"), a:has-text("Approve")');
        await expect(approveBtn).toHaveCount(0);
        const rejectBtn = page.locator('button:has-text("Reject"), a:has-text("Reject")');
        await expect(rejectBtn).toHaveCount(0);
    });

    test('Admin Assessments page has zero approval wording', async ({ page }) => {
        await loginAdmin(page);
        await page.goto('/admin/assessments');
        await page.waitForLoadState('networkidle');

        const bodyText = await page.locator('body').innerText();
        for (const word of FORBIDDEN_WORDS) {
            expect(bodyText).not.toContain(word);
        }
    });

    test('Admin Documents Repository has zero approval/rejection actions', async ({ page }) => {
        await loginAdmin(page);
        await page.goto('/admin/documents');
        await page.waitForLoadState('networkidle');

        const bodyText = await page.locator('body').innerText();
        for (const word of FORBIDDEN_WORDS) {
            expect(bodyText).not.toContain(word);
        }
    });
});
