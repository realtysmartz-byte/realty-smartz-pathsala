const { test, expect } = require('@playwright/test');

test('Verify Delete Candidate Button works cleanly', async ({ page }) => {
    page.on('console', msg => console.log('PAGE LOG:', msg.type(), msg.text()));
    page.on('pageerror', err => console.log('PAGE ERROR:', err.message));

    let deleteRequested = false;
    let deleteSucceeded = false;

    page.on('request', req => {
        if (req.url().includes('/delete')) {
            deleteRequested = true;
            console.log('REQ:', req.method(), req.url());
        }
    });
    page.on('response', async res => {
        if (res.url().includes('/delete')) {
            console.log('RES:', res.status(), res.url());
            const text = await res.text();
            console.log('RES BODY:', text);
            if (res.status() === 200 && text.includes('success')) {
                deleteSucceeded = true;
            }
        }
    });

    // Login
    await page.goto('/admin/login');
    await page.fill('input[name="email"]', 'admin@realtysmartzpathshala.in');
    await page.fill('input[name="password"]', 'Admin@2026');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin/dashboard');

    // Go to candidates
    await page.goto('/admin/candidates');
    await page.waitForLoadState('networkidle');

    const deleteBtn = page.locator('.btn-action-delete').first();
    await expect(deleteBtn).toBeVisible();
    await deleteBtn.click();

    const deleteModal = page.locator('#deleteCandidateModal');
    await expect(deleteModal).toHaveClass(/active/);

    const confirmBtn = page.locator('#btnConfirmDelete');
    await expect(confirmBtn).toBeVisible();

    // Standard Playwright click - verifies that pointer events and actionability pass perfectly!
    await confirmBtn.click();

    await page.waitForTimeout(1000);

    expect(deleteRequested).toBe(true);
    expect(deleteSucceeded).toBe(true);
    await expect(deleteModal).not.toHaveClass(/active/);
});
