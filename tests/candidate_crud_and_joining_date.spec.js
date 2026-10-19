const { test, expect } = require('@playwright/test');

async function loginAdmin(page) {
    await page.goto('/admin/login');
    await page.fill('input[name="email"]', 'admin@realtysmartzpathshala.in');
    await page.fill('input[name="password"]', 'Admin@2026');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/admin/dashboard');
}

test.describe('Candidate Joining Date, Edit Details, and Soft Delete', () => {

    test('Dashboard has Joining Date column and Edit/Delete modals function properly', async ({ page }) => {
        page.on('console', msg => console.log('PAGE LOG:', msg.text()));
        page.on('pageerror', err => console.log('PAGE ERROR:', err.message));

        await loginAdmin(page);
        await page.goto('/admin/dashboard');
        await page.waitForLoadState('networkidle');

        // Check table headers
        const headers = await page.locator('.clean-table th').allTextContents();
        expect(headers.some(h => h.includes('Joining date'))).toBeTruthy();
        expect(headers.some(h => h.includes('Action'))).toBeTruthy();

        // Check action buttons exist in recent candidates table
        const editBtn = page.locator('.btn-edit').first();
        const deleteBtn = page.locator('.btn-delete').first();
        await expect(editBtn).toBeVisible();
        await expect(deleteBtn).toBeVisible();

        // Test Edit Modal
        await editBtn.click();
        const editModal = page.locator('#editCandidateModal');
        await expect(editModal).toHaveClass(/active/);
        await expect(page.locator('#editModalTitle')).toHaveText('Edit candidate details');

        // Update joining date in modal
        await page.fill('#editJoiningDate', '2026-11-01');
        const savePromise = page.waitForResponse(res => res.url().includes('/update') && res.status() === 200);
        await page.evaluate(() => handleSaveCandidate());
        const response = await savePromise;
        const resJson = await response.json();
        console.log('Update Candidate Response:', resJson);

        // Modal should close upon successful save
        await expect(editModal).not.toHaveClass(/active/);

        // Verify updated joining date in table row
        const joiningCell = page.locator('.joining-date-val').first();
        await expect(joiningCell).toBeVisible();
        const joiningText = await joiningCell.textContent();
        expect(joiningText).toContain('01 Nov 2026');

        // Test Delete Modal
        await deleteBtn.click();
        const deleteModal = page.locator('#deleteCandidateModal');
        await expect(deleteModal).toHaveClass(/active/);
        await expect(page.locator('#deleteModalTitle')).toHaveText('Delete candidate');

        // Close delete modal without deleting
        await page.locator('#btnCancelDelete').dispatchEvent('click');
        await expect(deleteModal).not.toHaveClass(/active/);
    });

    test('Candidates Directory has Joining Date column and Edit/Delete buttons', async ({ page }) => {
        await loginAdmin(page);
        await page.goto('/admin/candidates');
        await page.waitForLoadState('networkidle');

        // Check table headers
        const headers = await page.locator('.candidates-table th').allTextContents();
        expect(headers.some(h => h.includes('Joining date'))).toBeTruthy();

        // Check action buttons
        const editBtn = page.locator('.btn-action-edit').first();
        const deleteBtn = page.locator('.btn-action-delete').first();
        await expect(editBtn).toBeVisible();
        await expect(deleteBtn).toBeVisible();

        // Test opening Edit modal
        await editBtn.click();
        const editModal = page.locator('#editCandidateModal');
        await expect(editModal).toHaveClass(/active/);
        await page.locator('#btnCancelEdit').dispatchEvent('click');
        await expect(editModal).not.toHaveClass(/active/);

        // Test opening Delete modal
        await deleteBtn.click();
        const deleteModal = page.locator('#deleteCandidateModal');
        await expect(deleteModal).toHaveClass(/active/);
        await page.locator('#btnCancelDelete').dispatchEvent('click');
        await expect(deleteModal).not.toHaveClass(/active/);
    });

    test('Candidate Profile displays Joining Date and has functional Edit Details modal', async ({ page }) => {
        await loginAdmin(page);
        await page.goto('/admin/dashboard');
        await page.waitForLoadState('networkidle');

        // Click first candidate View button
        await page.locator('.btn-view').first().click();
        await page.waitForURL(/\/admin\/candidates\/\d+/);

        // Check Personal details card has Joining date
        const joiningDateVal = page.locator('#profileJoiningDateVal');
        await expect(joiningDateVal).toBeVisible();

        // Check Edit details button
        const editDetailsBtn = page.locator('button[onclick="openProfileEditModal()"]');
        await expect(editDetailsBtn).toBeVisible();

        // Click Edit details
        await editDetailsBtn.click();
        const profileModal = page.locator('#editProfileModal');
        await expect(profileModal).toHaveClass(/active/);

        // Edit joining date to 2026-12-15
        await page.fill('#editProfileModal input[name="joining_date"]', '2026-12-15');
        const savePromise = page.waitForResponse(res => res.url().includes('/update') && res.status() === 200);
        await page.evaluate(() => handleSaveProfileDetails());
        await savePromise;

        // Modal should close
        await expect(profileModal).not.toHaveClass(/active/);

        // Verify updated joining date displayed
        await expect(joiningDateVal).toHaveText('15 Dec 2026');
    });

    test('Check zero horizontal overflow at multiple screen sizes', async ({ page }) => {
        const viewports = [360, 768, 1024, 1440];
        await loginAdmin(page);

        for (const w of viewports) {
            await page.setViewportSize({ width: w, height: 800 });
            await page.goto('/admin/dashboard');
            await page.waitForLoadState('networkidle');

            const hasHScrollDashboard = await page.evaluate(() => document.documentElement.scrollWidth > document.documentElement.clientWidth);
            expect(hasHScrollDashboard).toBeFalsy();

            await page.goto('/admin/candidates');
            await page.waitForLoadState('networkidle');

            const hasHScrollCandidates = await page.evaluate(() => document.documentElement.scrollWidth > document.documentElement.clientWidth);
            expect(hasHScrollCandidates).toBeFalsy();
        }
    });

});
